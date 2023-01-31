<?php

if (disableAccount($_GET['object'])) {
  echo "<p>".$_GET['object']." ".was_disabled."</p>";
  echo "<p>".reactivate_pw_request_necessary."</p>";
}
else {
  echo "<p>".user_does_not_exist."</p>";
}

?>
