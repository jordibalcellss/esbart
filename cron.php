<?php

require 'config.php';
ini_set('error_reporting',ERROR_REPORTING);
ini_set('display_errors',DISPLAY_ERRORS);

require 'include/functions.php';

$db = new DB();
$stmt = $db->prepare('
  UPDATE pw_set_requests SET expired = 1
  WHERE NOT expired
  AND TIMESTAMPDIFF(HOUR, created, NOW()) > '.TOKEN_EXPIRES_H);
$stmt->execute();

?>
