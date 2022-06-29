<?php

require 'config.php';
require 'locale/'.LOCALE.'.php';
ini_set('error_reporting',ERROR_REPORTING);
ini_set('display_errors',DISPLAY_ERRORS);

session_start();
if (!isset($_SESSION['id'])) {
  header("Location: login.php");
}

require 'include/functions.php';
require 'include/template/head.php';

if ($_POST) {
  $con = LDAPconnect()[0];
  if ($_GET['action'] == 'password') {
    require('include/password.php');
  }
  else if ($_GET['action'] == 'add' && $_GET['object'] == 'user' && $_GET['mode'] == 'manual') {
    require('include/add-user.php');
  }
  else if ($_GET['action'] == 'add' && $_GET['object'] == 'user' && $_GET['mode'] == 'assisted') {
    require('include/add-user-assisted.php');
  }
  else if ($_GET['action'] == 'add' && $_GET['object'] == 'group') {
    require('include/add-group.php');
  }
  else if ($_GET['action'] == 'edit') {
    require('include/edit.php');
  }
  ldap_close($con);
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
    require('include/password-form.php');
  }
  else if ($_GET['action'] == 'add' && $_GET['object'] == 'user' && $_GET['mode'] == 'assisted') {
    require('include/add-user-assisted-form.php');
  }
  else if ($_GET['action'] == 'add' && $_GET['object'] == 'group') {
    require('include/add-group-form.php');
  }
  else if ($_GET['action'] == 'add' && $_GET['object'] == 'user' && $_GET['mode'] == 'manual') {
    require('include/add-user-form.php');
  }
  else if ($_GET['action'] == 'list') {
    $con = LDAPconnect()[0];
    require('include/list-'.$_GET['object'].'.php');
    ldap_close($con);
  }
  else if ($_GET['action'] == 'edit') {
    require('include/edit-form.php');
  }
  else if ($_GET['action'] == 'disable') {
    require('include/disable.php');
  }
}

require 'include/template/base.php';

?>
