<?php
require_once 'config.php';
require_once 'locale/'.LOCALE.'.php';
ini_set('error_reporting',ERROR_REPORTING);
ini_set('display_errors',DISPLAY_ERRORS);

session_start();
if (!isset($_SESSION['id'])) {
	header("Location: login.php");
}

require_once 'include/functions.php';
include 'include/template/head.php';

$LDAPcon = LDAPconnect();
$con = $LDAPcon[0];
$bind = $LDAPcon[1];

if ($_POST && $bind) {
	if ($_GET['action'] == 'password') {
		include('include/password.php');
	}
	else if ($_GET['action'] == 'add' && $_GET['object'] == 'user' && $_GET['mode'] == 'manual') {
		include('include/add-user.php');
	}
	else if ($_GET['action'] == 'add' && $_GET['object'] == 'user' && $_GET['mode'] == 'assisted') {
		include('include/add-user-assisted.php');
	}
	else if ($_GET['action'] == 'add' && $_GET['object'] == 'group') {
		include('include/add-group.php');
	}
	else if ($_GET['action'] == 'edit') {
		include('include/edit.php');
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
	if ($_GET['action'] == 'password') {
		include('include/password-form.php');
	}
	else if ($_GET['action'] == 'add' && $_GET['object'] == 'user' && $_GET['mode'] == 'assisted') {
		include('include/add-user-assisted-form.php');
	}
	else if ($_GET['action'] == 'add' && $_GET['object'] == 'group') {
		include('include/add-group-form.php');
	}
	else if ($_GET['action'] == 'add' && $_GET['object'] == 'user' && $_GET['mode'] == 'manual') {
		include('include/add-user-form.php');
	}
	else if ($_GET['action'] == 'list' && $_GET['object'] == 'user') {
		include('include/list-user.php');
	}
	else if ($_GET['action'] == 'list' && $_GET['object'] == 'group') {
		include('include/list-group.php');
	}
	else if ($_GET['action'] == 'edit') {
		include('include/edit-form.php');
	}
	else if ($_GET['action'] == 'disable') {
		include('include/disable.php');
	}
}

include 'include/template/base.php';
?>
