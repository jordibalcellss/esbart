<?php
require_once 'config.php';
require_once 'locale/'.LOCALE.'.php';
ini_set('error_reporting',ERROR_REPORTING);
ini_set('display_errors',DISPLAY_ERRORS);

session_start();
if (!isset($_SESSION['id'])) {
	header("Location: login.php");
}

require_once 'functions.php';
include 'inc/head.php';

$LDAPcon = LDAPconnect();
$con = $LDAPcon[0];
$bind = $LDAPcon[1];

if ($_POST && $bind) {
	if ($_GET['action'] == 'password') {
		if (strlen($_POST['password']) == 0 || strlen($_POST['password_c']) == 0) {
			$err[] = both_fields_required;
		}
		else {
			if ($_POST['password'] != $_POST['password_c']) {
				$err[] = password_does_not_match;
			}
			else {
				if (strlen($_POST['password']) < MIN_PASSWORD_LENGTH) {
					$err[] = password_must_contain_at_least." ".MIN_PASSWORD_LENGTH." ".characters;
				}
				if (!preg_match("/[^a-zA-Z]/",$_POST['password'])) {
					$err[] = password_must_contain_at_least." ".num_symbol;
				}
				if (!preg_match("#[a-z]+#",$_POST['password'])) {
					$err[] = password_must_contain_at_least." ".lowercase_letter;
				}
				if (!preg_match("#[A-Z]+#",$_POST['password'])) {
					$err[] = password_must_contain_at_least." ".uppercase_letter;
				}
				if (!isset($err)) {
					//prepare entry
					$password = $_POST['password'];
					$uid = $_GET['object'];
					//set the samba password
					//this is the set of 4 attributes that smbpasswd actually creates
					$entry['sambantpassword'] = strtoupper(hash('md4',iconv('UTF-8','UTF-16LE',$password)));
					$entry['sambapasswordhistory'] = '0000000000000000000000000000000000000000000000000000000000000000';
					$entry['sambapwdlastset'] = time();
					$entry['sambaacctflags'] = '[U          ]';
					//set the password
					$entry['userpassword'] = "{SHA}".base64_encode(pack("H*",sha1($password)));
					$res = ldap_mod_replace($con,'uid='.$uid.",ou=users,".LDAP_TREE,$entry);
					if (!$res) {
						$err_des = ldap_error($con);
						$err_num = ldap_errno($con);
						$err[] = "Error: LDAP $err_num - $error_desc.";
					}
					else {
						$err[] = new_password_ready;
					}
				}
			}
		}
	}
	if ($_GET['action'] == 'add' && $_GET['object'] == 'user' && $_GET['mode'] == 'manual') {
		if (strlen($_POST['name']) == 0 || strlen($_POST['username']) == 0) {
			$err[] = name_username_cannot_be_empty;
		}
		else {
			if (strlen(trim($_POST['email'])) > 0) {
				if (!filter_var(trim($_POST['email']),FILTER_VALIDATE_EMAIL)) {
					$err[] = verify_email;
				}
			}
			if (preg_match("/[^a-z0-9\.]/",trim($_POST['username']))) {
				$err[] = username_legal_characters;
			}
			else if (getPersonalData(trim($_POST['username']))) {
				$err[] = user_already_exists;
			}
			if (!isset($err)) {
				//prepare entry
				if (strlen($_POST['homedirectory']) == 0) {
					$homedirectory = '/home/'.trim($_POST['username']);
				}
				else {
					$homedirectory = trim($_POST['homedirectory']);
				}

				$uidnumber = LDAPgetNextId($con,'user');
				$entry['objectClass'][0] = 'top';
				$entry['objectClass'][1] = 'account';
				$entry['objectClass'][2] = 'posixAccount';
				$entry['objectClass'][3] = 'shadowAccount';
				$entry['objectClass'][4] = 'extensibleObject';
				$entry['objectClass'][5] = 'sambaSamAccount';
				$entry['cn'] = trim($_POST['name']);
				$entry['uid'] = trim($_POST['username']);
				$entry['uidNumber'] = $uidnumber;
				$entry['gidNumber'] = LDAP_PRIMARY_GROUP_ID;
				$entry['homeDirectory'] = $homedirectory;
				$entry['loginShell'] = trim($_POST['loginshell']);
				//$entry['gecos'] = '';
				$entry['emailAddress'] = trim($_POST['email']);
				$entry['sambaSID'] = LDAPgetSambaSID($uidnumber);

				//add entry
				$res_entry = ldap_add($con,'uid='.trim($_POST['username']).",ou=users,".LDAP_TREE,$entry);
			
				//add memberships
				if (isset($_POST["groups"])) {
					addUserToGroups(trim($_POST['username']),$_POST["groups"]);
				}
			
				if ($res_entry) {
					$err[] = user_add_success;
				}
				else {
					$err[] = a_problem_occurred;
				}
			}
		}
	}
	else if ($_GET['action'] == 'edit') {
		if (strlen($_POST['name']) == 0) {
			$err[] = name_cannot_be_empty;
		}
		if (strlen(trim($_POST['email'])) > 0) {
			if (!filter_var(trim($_POST['email']),FILTER_VALIDATE_EMAIL)) {
				$err[] = verify_email;
			}
		}
		if (!isset($err)) {
			//prepare entry
			$entry['cn'] = trim($_POST['name']);
			$entry['emailaddress'] = trim($_POST['email']);
			$entry['homedirectory'] = trim($_POST['homedirectory']);
			$entry['loginshell'] = trim($_POST['loginshell']);
			
			//add entry
			$res_entry = ldap_mod_replace($con,'uid='.$_GET['object'].",ou=users,".LDAP_TREE,$entry);
			
			//edit memberships
			if (isset($_POST["groups"])) {
				//get the groups the user is a member of
				$current_groups = getUserMembership($_GET['object']);
				//obtain the new groups to assign (the ones missing in the latest assignation)
				$new_groups = array_diff($_POST["groups"],$current_groups);
				//add the memberships
				addUserToGroups($_GET['object'],$new_groups);
					
				//get the groups the user is not a member of (the ones missing in the latest assignation)
				$foreign_groups = array_diff(getAssignableGroups(),$_POST["groups"]);
				//get the groups the user must be removed from (the ones missing in the latest assignation
				//but present in the expired membership)
				$remaining_groups = array_intersect($foreign_groups,$current_groups);
				//remove de memberships
				removeUserFromGroups($_GET['object'],$remaining_groups);
			}
				
			if ($res_entry) {
				$err[] = user_edit_success;
			}
			else {
				$err[] = a_problem_occurred;
			}
		}
	}
	else if ($_GET['action'] == 'add' && $_GET['object'] == 'user' && $_GET['mode'] == 'assisted') {
		if (strlen($_POST['name_1']) == 0 || strlen($_POST['name_2']) == 0 || strlen($_POST['email']) == 0) {
			$err[] = user_add_assisted_name_surname_email_required;
		}
		else {
			if (!filter_var(trim($_POST['email']),FILTER_VALIDATE_EMAIL)) {
				$err[] = verify_email;
			}
			else {
				$name_1_f = trimSplitFormatName($_POST['name_1'],0);
				$name_2_f = trimSplitFormatSurname($_POST['name_2'],1);
				if (isset($_POST['name_3'])) {
					if (strlen($_POST['name_3']) != 0) {
						$name_3_f = trimSplitFormatSurname($_POST['name_3'],1);
						$name_3 = ' '.trim($_POST['name_3']);
					}
					else {
						$name_3_f = '';
						$name_3 = '';
					}
				}
				if (getPersonalData($name_1_f.'.'.$name_2_f.$name_3_f)) {
					$err[] = user_already_exists;
				}
				if (!isset($err)) {
					//prepare entry
					$uidnumber = LDAPgetNextId($con,'user');
					$entry['objectClass'][0] = 'top';
					$entry['objectClass'][1] = 'account';
					$entry['objectClass'][2] = 'posixAccount';
					$entry['objectClass'][3] = 'shadowAccount';
					$entry['objectClass'][4] = 'extensibleObject';
					$entry['objectClass'][5] = 'sambaSamAccount';
					$entry['cn'] = trim($_POST['name_1']).' '.trim($_POST['name_2']).$name_3;
					$entry['uid'] = $name_1_f.'.'.$name_2_f.$name_3_f;
					$entry['uidNumber'] = $uidnumber;
					$entry['gidNumber'] = LDAP_PRIMARY_GROUP_ID;
					$entry['homeDirectory'] = '/home/'.$name_1_f.'.'.$name_2_f.$name_3_f;
					$entry['loginShell'] = '/bin/false';
					//$entry['gecos'] = '';
					$entry['emailAddress'] = trim($_POST['email']);
					$entry['sambaSID'] = LDAPgetSambaSID($uidnumber);
			
					//add entry
					$res_entry = ldap_add($con,'uid='.$name_1_f.'.'.$name_2_f.$name_3_f.",ou=users,".LDAP_TREE,$entry);
			
					//add memberships
					if (isset($_POST["groups"])) {
						addUserToGroups($name_1_f.'.'.$name_2_f.$name_3_f,$_POST["groups"]);
					}
				
					if ($res_entry) {
						$err[] = user_add_success;
						if (sendOneTimeSetPasswordEmail($name_1_f.'.'.$name_2_f.$name_3_f,false)) {
							$err[] = user_add_welcome_email_sent;
						}
						else {
							$err[] = user_add_welcome_email_not_sent;
						}
					}
					else {
						$err[] = a_problem_occurred;
					}
				}
			}
		}
	}
	else if ($_GET['action'] == 'add' && $_GET['object'] == 'group') {
		if (strlen($_POST['name']) == 0) {
			$err[] = name_cannot_be_empty;
		}
		else {
			//prepare entry
			$entry['objectClass'][0] = 'top';
			$entry['objectClass'][1] = 'posixGroup';
			$entry['gidnumber'] = LDAPgetNextId($con,'group');
			
			//add entry
			$res_entry = @ldap_add($con,'cn='.trim($_POST['name']).",ou=groups,".LDAP_TREE,$entry);
			
			if ($res_entry) {
				$err[] = group_add_success;
			}
			else {
				if (ldap_errno($con) == 68) {
					$err[] = group_already_exists;
				}
				else {
					$err[] = a_problem_occurred;
				}
			}
		}
	}
}
else {
	$err = [];
}

?>
			<h1><?=TITLE?></h1>
			<ul id="menu">
				<li><?=user?>
					<ul>
						<li><a href="?action=add&object=user&mode=assisted"><?=add." - ".assisted_mode?></a></li>
						<li><a href="?action=add&object=user&mode=manual"><?=add?></a></li>
						<li><a href="?action=list&object=user"><?=list_action?></a></li>
					</ul>
				</li>
				<li><?=group?>
					<ul>
						<li><a href="?action=add&object=group"><?=add?></a></li>
						<li><a href="?action=list&object=group"><?=list_action?></a></li>
					</ul>
				</li>
			</ul>
<?php

if (isset($_GET['action']) && isset($_GET['object'])) {
	if ($_GET['action'] == 'add' && $_GET['object'] == 'user' && $_GET['mode'] == 'manual') {
?>
			<h2><?=add?> <?=user?></h2>
			<h3><?=user_add_how_to_use?></h3>
			<ul>
				<li><?=user_add_advice_1?></li>
				<li><?=user_add_advice_2?></li>
			</ul>
			<form id="user" enctype="application/x-www-form-urlencoded" method="post" action="index.php?action=add&object=user&mode=manual">
				<div><label for="name"><?=name?>*</label></div>
				<div><input name="name" type="text" class="" value="" /></div>
				
				<div><label for="username"><?=username?>*</label></div>
				<div><input name="username" type="text" class="" value="" /></div>
				
				<div><label for="email"><?=email?></label></div>
				<div><input name="email" type="text" class="" value="" /></div>
				
				<div><label for="homedirectory"><?=home_directory?></label></div>
				<div><input name="homedirectory" type="text" class="" value="" /></div>
				
				<div><label for="loginshell"><?=shell?></label></div>
				<div><input name="loginshell" type="text" class="" value="/bin/false" /></div>
<?php
		if ($bind) {
			echo "\n";
			echo "				<div><label for=\"groups\">".member_of.":</label></div>\n";
			echo "				<div><select name=\"groups[]\" multiple>\n";
			echo "					<option value=\"\"></option>\n";
			$groups = getAssignableGroups();
			foreach ($groups as $group) {
				echo "					<option value=\"$group\">$group</option>\n";
			}
			echo "				</select></div>\n";
		}
		ldap_close($con);
?>			
				<input name="submit" type="submit" class="" value="<?=add?>" />
<?php
		printMessages($err);
		echo "			</form>\n";
	}
	else if ($_GET['action'] == 'edit') {
		if ($pd = getPersonalData($_GET['object'])) {
?>
			<h2><?=edit?> <?=user?></h2>
			<form id="user" enctype="application/x-www-form-urlencoded" method="post" action="index.php?action=edit&object=<?=$pd[2]?>">
				<div><?=username?></div>
				<div class="not-editable-form-item"><?=$pd[2]?></div>
				
				<div><label for="name"><?=name?></label></div>
				<div><input name="name" type="text" class="" value="<?=$pd[1]?>" /></div>
				
				<div><label for="email"><?=email?></label></div>
				<div><input name="email" type="text" class="" value="<?=$pd[3]?>" /></div>
				
				<div><label for="homedirectory"><?=home_directory?></label></div>
				<div><input name="homedirectory" type="text" class="" value="<?=$pd[4]?>" /></div>
				
				<div><label for="loginshell"><?=shell?></label></div>
				<div><input name="loginshell" type="text" class="" value="<?=$pd[5]?>" /></div>
<?php
		if ($bind) {
			echo "\n";
			echo "				<div><label for=\"groups\">".member_of.":</label></div>\n";
			echo "				<div><select name=\"groups[]\" multiple>\n";
			echo "					<option value=\"\"></option>\n";
			//get the groups the user is a member of
			$current_groups = getUserMembership($pd[2]);
			//out of the total assignable groups
			$groups = getAssignableGroups();
			foreach ($groups as $group) {
				//is the group already assigned to the user?
				if (array_search($group,$current_groups) !== false) {
					echo "					<option value=\"$group\" selected>$group</option>\n";
				}
				else {
					echo "					<option value=\"$group\">$group</option>\n";
				}
			}
			echo "				</select></div>\n";
		}
		ldap_close($con);
?>			
				<input name="submit" type="submit" class="" value="<?=save?>" />
<?php
			printMessages($err);
			echo "			</form>\n";
		}
		else {
			echo "<p>".user_does_not_exist."</p>";
		}
	}
	else if ($_GET['action'] == 'password') {
		if ($pd = getPersonalData($_GET['object'])) {
			if (isset($_GET['noemail'])) {
?>
			<h2><?=set_password_header?></h2>
			<p><?=set_password_requirements?></p>
			<form id="password" enctype="application/x-www-form-urlencoded" method="post" action="index.php?action=password&object=<?=$_GET['object']?>&noemail">
				<div><?=username?></div>
				<div class="not-editable-form-item"><?=$_GET['object']?></div>
				
				<div><label for="password"><?=password?></label></div>
				<div><input name="password" type="password" class="" value="" /></div>
				
				<div><label for="password_c"><?=repeat?> <?=password?></label></div>
				<div><input name="password_c" type="password" class="" value="" /></div>
			
				<input name="submit" type="submit" class="" value="<?=create?>" />
<?php
				printMessages($err);
				echo "			</form>\n";
			}
			else {
				if (sendOneTimeSetPasswordEmail($_GET['object'],true)) {
					echo "<p>".one_time_password_email_sent." ".$_GET['object']."</p>";
				}
			}
		}
		else {
			echo "<p>".user_does_not_exist."</p>";
		}
	}
	else if ($_GET['action'] == 'disable') {
		if (disableAccount($_GET['object'])) {
			echo "<p>".$_GET['object']." ".was_disabled."</p>";
			echo "<p>".reactivate_pw_request_necessary."</p>";
		}
		else {
			echo "<p>".user_does_not_exist."</p>";
		}
	}
	if ($_GET['action'] == 'list' && $_GET['object'] == 'user') {
		if ($bind) {
			$result = ldap_search($con,'ou=users,'.LDAP_TREE,"(cn=*)",array('cn','email','uidnumber','uid'));
			$entries = ldap_get_entries($con,$result);
			echo "			<h2>".user_list."</h2>\n";
			echo "			<table>\n";
			echo "				<tr>\n";
			echo "					<th>".name."</th>\n";
			echo "					<th>".login."</th>\n";
			echo "					<th>".email."</th>\n";
			echo "					<th>".id."</th>\n";
			echo "					<th>".member_of."</th>\n";
			echo "					<th align=\"right\">".actions."</th>\n";
			echo "				</tr>\n";
			for ($i = 0; $i < $entries['count']; $i++) {
				if (accountHasEmail($entries[$i]['uid'][0])) {
					$password = '<a href="?action=password&object='.$entries[$i]['uid'][0].'">'.password.'</a>';
				}
				else {
					$password = '<a href="?action=password&object='.$entries[$i]['uid'][0].'&noemail">'.password.'</a>';
				}
				if (accountIsEnabled($entries[$i]['uid'][0])) {
					$disable = '&nbsp;&nbsp;<a href="?action=disable&object='.$entries[$i]['uid'][0].'">'.disable.'</a>';
				}
				else {
					$disable = '';
				}
				$edit = '&nbsp;&nbsp;<a href="?action=edit&object='.$entries[$i]['uid'][0].'">'.edit.'</a>';
				echo "				<tr>\n";
				echo '					<td width="200">'.$entries[$i]['cn'][0]."</td>\n";
				echo '					<td width="100">'.$entries[$i]['uid'][0]."</td>\n";
				echo '					<td width="300">'.$entries[$i]['email'][0]."</td>\n";
				echo '					<td width="70">'.$entries[$i]['uidnumber'][0]."</td>\n";
				echo '					<td>'.implode(', ',getUserMembership($entries[$i]['uid'][0]))."</td>\n";
				echo '					<td align="right" width="200">'.$password.$disable.$edit."</td>\n";
				echo "				</tr>\n";
			}
			echo "			</table>\n";
			echo "			<p>".there_are." ".ldap_count_entries($con,$result)." ".users."</p>\n";
			ldap_close($con);
		}
	}
	else if ($_GET['action'] == 'list' && $_GET['object'] == 'group') {
		if ($bind) {
			$result = ldap_search($con,'ou=groups,'.LDAP_TREE,"(cn=*)",array('cn','gidnumber'));
			$entries = ldap_get_entries($con,$result);
			echo "			<h2>".group_list."</h2>\n";
			echo "			<table>\n";
			echo "				<tr>\n";
			echo "					<th>".name."</th>\n";
			echo "					<th>".id."</th>\n";
			echo "					<th>".members."</th>\n";
			echo "				</tr>\n";
			for ($i = 0; $i < $entries['count']; $i++) {
				echo "				<tr>\n";
				echo '					<td width="130">'.$entries[$i]['cn'][0]."</td>\n";
				echo '					<td width="70">'.$entries[$i]['gidnumber'][0]."</td>\n";
				echo '					<td>'.implode(', ',getGroupMembers($entries[$i]['cn'][0]))."</td>\n";
				echo "				</tr>\n";
			}
			echo "			</table>\n";
			echo "			<p>".there_are." ".ldap_count_entries($con,$result)." ".groups."</p>\n";
			ldap_close($con);
		}
	}
	else if ($_GET['action'] == 'add' && $_GET['object'] == 'user' && $_GET['mode'] == 'assisted') {
?>
			<h2><?=add?> <?=user?> - <?=assisted_mode?></h2>
			<h3><?=user_add_how_to_use?></h3>
			<ul>
				<li><?=user_add_assisted_advice_1?></li>
				<li><?=user_add_assisted_advice_2?></li>
				<li><?=user_add_assisted_advice_3?></li>
			</ul>
			<form id="user" enctype="application/x-www-form-urlencoded" method="post" action="index.php?action=add&object=user&mode=assisted">
				<div><label for="name_1"><?=name?>*</label></div>
				<div><input name="name_1" type="text" class="" value="" /></div>
				
				<div><label for="name_2"><?=surname?>*</label></div>
				<div><input name="name_2" type="text" class="" value="" /></div>
<?php
		if (!HIDE_SECOND_SURNAME) {
			echo "				<div><label for=\"name_3\">".surname_2."</label></div>\n";
			echo "				<div><input name=\"name_3\" type=\"text\" class=\"\" value=\"\" /></div>\n";
		}
?>			
				<div><label for="email"><?=email?>*</label></div>
				<div><input name="email" type="text" class="" value="" /></div>
<?php
		if ($bind) {
			echo "\n";
			echo "				<div><label for=\"groups\">".member_of.":</label></div>\n";
			echo "				<div><select name=\"groups[]\" multiple>\n";
			echo "					<option value=\"\"></option>\n";
			$groups = getAssignableGroups();
			foreach ($groups as $group) {
				echo "					<option value=\"$group\">$group</option>\n";
			}
			echo "				</select></div>\n";
		}
		ldap_close($con);
?>			
				<input name="submit" type="submit" class="" value="<?=add?>" />
<?php
	printMessages($err);
	echo "			</form>\n";
	}
	else if ($_GET['action'] == 'add' && $_GET['object'] == 'group') {
?>
			<h2><?=add?> <?=group?></h2>
			<p><?=group_naming_advice?></p>
			<form id="group" enctype="application/x-www-form-urlencoded" method="post" action="index.php?action=add&object=group">
				<div><label for="name"><?=name?>*</label></div>
				<div><input name="name" type="text" class="" value="" /></div>
				<input name="submit" type="submit" class="" value="<?=add?>" />
<?php
	printMessages($err);
	echo "			</form>\n";
	}
}

include 'inc/base.php';
?>
