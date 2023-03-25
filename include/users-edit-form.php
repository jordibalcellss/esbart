<?php

if ($pd = getPersonalData($_GET['object'])) {
?>
      <h2><?=edit?> <?=user?></h2>
      <form id="user" enctype="application/x-www-form-urlencoded" method="post" action="index.php?module=users&action=edit&object=<?=$pd[2]?>">
        <div><?=username?></div>
        <div class="not-editable-form-item"><?=$pd[2]?></div>
        
        <div><label for="name"><?=name?></label></div>
        <div><input name="name" type="text" class="short" value="<?=$pd[1]?>" /></div>
        
        <div><label for="email"><?=email?></label></div>
        <div><input name="email" type="text" value="<?=$pd[3]?>" /></div>
        
        <div><label for="homedirectory"><?=home_directory?></label></div>
        <div><input name="homedirectory" type="text" class="short" value="<?=$pd[4]?>" /></div>
        
        <div><label for="loginshell"><?=shell?></label></div>
        <div><input name="loginshell" type="text" class="short" value="<?=$pd[5]?>" /></div>
<?php
echo "\n";
echo "        <div><label for=\"groups\">".member_of."</label></div>\n";
echo "        <div><select name=\"groups[]\" multiple>\n";
echo "          <option value=\"\"></option>\n";
//get the groups the user is a member of
$current_groups = getUserMembership($pd[2]);
//out of the total assignable groups
$groups = getAssignableGroups();
foreach ($groups as $group) {
  //is the group already assigned to the user?
  if (array_search($group,$current_groups) !== false) {
    echo "          <option value=\"$group\" selected>$group</option>\n";
  }
  else {
    echo "          <option value=\"$group\">$group</option>\n";
  }
}
echo "        </select></div>\n";
?>    
        <input name="submit" type="submit" value="<?=save?>" />
<?php
  printMessages($err);
  echo "      </form>\n";
}
else {
  echo "<p>".user_does_not_exist."</p>";
}

?>
