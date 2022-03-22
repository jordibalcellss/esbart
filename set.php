<?php
require_once 'config.php';
require_once 'locale/'.LOCALE.'.php';
ini_set('error_reporting',ERROR_REPORTING);
ini_set('display_errors',DISPLAY_ERRORS);

require_once 'functions.php';
include 'inc/head-login.php';

if (isset($_GET['p'])) {
	$db = new DB();
	$stmt = $db->prepare('SELECT user_id,expired FROM pw_set_requests WHERE pass=:pass');
	$stmt->execute(array(':pass' => $_GET['p']));
	if ($stmt->rowCount() == 1) {
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if (!$result['expired']) {
			if ($_POST) {
				if (strlen($_POST['password']) == 0 || strlen($_POST['password_c']) == 0) {
					$err[] = all_fields_required;
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
							$password = $_POST['password'];
							$uid = $result['user_id'];
							//disable the pass code
							$stmt = $db->query("UPDATE pw_set_requests SET expired=1 WHERE user_id='$uid'");
							//set the samba password
							//this is the set of 4 attributes that smbpasswd actually creates
							$entry['sambantpassword'] = strtoupper(hash('md4',iconv('UTF-8','UTF-16LE',$password)));
							$entry['sambapasswordhistory'] = '0000000000000000000000000000000000000000000000000000000000000000';
							$entry['sambapwdlastset'] = time();
							$entry['sambaacctflags'] = '[U          ]';
							//set the password
							$entry['userpassword'] = "{SHA}".base64_encode(pack("H*",sha1($password)));
							$con = LDAPconnect()[0];
							$res = ldap_mod_replace($con,'uid='.$uid.",ou=users,".LDAP_TREE,$entry);
							if (!$res) {
								$err_des = ldap_error($con);
								$err_num = ldap_errno($con);
								$err[] = "Error: LDAP $err_num - $error_desc.";
							}
							else {
								if (!isset($_GET['man'])) {
									$err[] = password_created_confirmation_sent;
									sendWelcomeEmail($uid);
								}
								else {
									$err[] = new_password_ready;
								}
								ldap_close($con);
							}
						}
					}
				}
			}
			else {
				$err = [];
				if (isset($_GET['man'])) {
					$man = '&man';
				}
			}
?>
			<h1><?=set_password_header?></h1>
			<p><?=set_password_requirements?></p>
			<form id="password" enctype="application/x-www-form-urlencoded" method="post" action="<?=$_SERVER['PHP_SELF'].'?p='.$_GET['p'].$man?>">
				<div><label for="password"><?=password?></label></div>
				<div><input name="password" type="password" class="" value="" /></div>
				
				<div><label for="password_c"><?=repeat?> <?=password?></label></div>
				<div><input name="password_c" type="password" class="" value="" /></div>
			
				<input name="submit" type="submit" class="" value="<?=create?>" />
<?php
	printMessages($err);
	echo <<<EOD
			</form>
		</div>
	</body>
</html>
EOD;
		}
	}
}

?>
