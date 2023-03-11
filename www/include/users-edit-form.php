<?php

if ($pd = getPersonalData($_GET['object'])) {

?>
      <h2><?=edit?> <?=user?></h2>
      <form id="user" enctype="application/x-www-form-urlencoded" method="post" action="index.php?module=users&action=edit&object=<?=$pd[1][2]?>">
      <?php
        $key = array_search('uid', $pd[0]);
        echo "<div>".login."</div>";
        echo "<div class='not-editable-form-item'>".$pd[1][$key]."</div>";
        for ($x = 0; $x < sizeof($pd[0]); $x++) {
          if($pd[0][$x] != "uid"){
            echo "<div><label for=".$pd[0][$x].">".$pd[0][$x]."</label></div>";
            echo "<div><input name=".$pd[0][$x]." type='text' value=\"".$pd[1][$x]."\" /></div>";
          }
        }
        echo "<div><label for=\"groups\">".member_of.":</label></div>\n";
        echo "<div><select name=\"groups[]\" multiple>\n";
        //echo "        <option value=\"\"></option>\n";
        //get the groups the user is a member of
        $current_groups = getUserMembership($pd[1][2]);
        console($current_groups);
        //out of the total assignable groups
        $groups = getAssignableGroups();
        foreach ($groups as $group) {
          //is the group already assigned to the user?
          if (array_search($group,$current_groups) !== false) {
            echo "<option value=\"$group\" selected>$group</option>\n";
          }
          else {
            echo "<option value=\"$group\">$group</option>\n";
          }
        }
        echo "</select></div>\n";
        ?>    
        <input name="submit" type="submit" value="<?=save?>" />
        <?php
          printMessages($err);
          echo "</form>\n";
}
else {
  echo "<p>".user_does_not_exist."</p>";
}

?>
