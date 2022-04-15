<?php

require 'config.php';
ini_set('error_reporting',ERROR_REPORTING);
ini_set('display_errors',DISPLAY_ERRORS);

require 'include/functions.php';

$db = new DB();
$stmt = $db->prepare('UPDATE pw_set_requests SET expired=1 WHERE id IN (SELECT id FROM pw_set_requests WHERE expired=0 AND hour(timediff(now(),created))>23)');
$stmt->execute();

?>
