<?php
require_once 'config.php';
require_once 'locale/'.LOCALE.'.php';
ini_set('error_reporting',ERROR_REPORTING);
ini_set('display_errors',DISPLAY_ERRORS);

session_start();
if (isset($_GET['action'])) {
	if ($_GET['action'] == 'logout') {
		session_destroy();
		header("Location: login.php");
	}
}
ob_start();
//buffers output until end of page or ob_ functions

require_once 'functions.php';
include 'inc/head-login.php';

if ($_POST) {
	$con = ldap_connect(LDAP_HOST);
	ldap_set_option($con,LDAP_OPT_PROTOCOL_VERSION,3);

	if ($con) {
		if (strlen($_POST['username']) == 0 || strlen($_POST['password']) == 0) {
			$err[] = all_fields_required;
			writeLog('login-error.log','empty fields');
		}
		else {
			$username = $_POST['username'];
			$bind = @ldap_bind($con,"uid=$username,ou=users,".LDAP_TREE,$_POST['password']); //@ supresses warnings
			if ($bind) {
				$result = ldap_read($con,LDAP_AUTH_GROUP,"(memberuid=*)",array('memberuid')); //equivalent to ldap_search()
				$entries = ldap_get_entries($con,$result);
				ldap_close($con);
				$success = false;
				for ($i = 0; $i < $entries[0]['memberuid']['count']; $i++) {
					if ($entries[0]['memberuid'][$i] == $username) {
						$_SESSION['id'] = $username;
						$success = true;
						writeLog('login-access.log','logged in');
						ob_end_clean(); //cleans the output buffer and stops buffering
						header("Location: index.php");
					}
				}
				if (!$success) {
					$err[] = unauthorized;
					writeLog('login-error.log','unauthorized');
				}
			}
			else {
				$err[] = user_and_or_password_incorrect;
				writeLog('login-error.log','wrong creds');
			}
		}
	}
}
else {
	$err = [];
}
?>
			<h1><?=TITLE?></h1>
			<form id="login" enctype="application/x-www-form-urlencoded" method="post" action="<?=$_SERVER['PHP_SELF']?>">
				<div><label for="username"><?=username?></label></div>
				<div><input name="username" type="text" class="" value="" /></div>
				
				<div><label for="password"><?=password?></label></div>
				<div><input name="password" type="password" class="" value="" /></div>
			
				<input name="envia" type="submit" class="" value="<?=login_submit?>" />
<?php
	printMessages($err);
	echo <<<EOD
			</form>
		</div>
	</body>
</html>
EOD;
?>
