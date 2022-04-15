<?php

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

		$uidnumber = getNextId($con,'user');
		$entry['objectclass'][0] = 'top';
		$entry['objectclass'][1] = 'account';
		$entry['objectclass'][2] = 'posixAccount';
		$entry['objectclass'][3] = 'shadowAccount';
		$entry['objectclass'][4] = 'extensibleObject';
		$entry['objectclass'][5] = 'sambaSamAccount';
		$entry['cn'] = trim($_POST['name']);
		$entry['uid'] = trim($_POST['username']);
		$entry['uidnumber'] = $uidnumber;
		$entry['gidnumber'] = LDAP_PRIMARY_GROUP_ID;
		$entry['homedirectory'] = $homedirectory;
		$entry['loginshell'] = trim($_POST['loginshell']);
		$entry['emailaddress'] = trim($_POST['email']);
		$entry['sambasid'] = getSambaSID($uidnumber);

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

?>
