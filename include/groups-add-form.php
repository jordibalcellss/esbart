      <h3><?=add?> <?=group?></h3>
      <p><?=group_naming_advice?></p>
      <form id="group" enctype="application/x-www-form-urlencoded" method="post" action="index.php?module=groups&action=add">
        <div><label for="name"><?=name?>*</label></div>
        <div><input name="name" type="text" class="short" value="" /></div>
        <input name="submit" type="submit" value="<?=add?>" />
<?php
printMessages($err);
echo "      </form>\n";

?>
