			<h2><?=add?> <?=group?></h2>
			<p><?=group_naming_advice?></p>
			<form id="group" enctype="application/x-www-form-urlencoded" method="post" action="index.php?action=add&object=group">
				<div><label for="name"><?=name?>*</label></div>
				<div><input name="name" type="text" value="" /></div>
				<input name="submit" type="submit" value="<?=add?>" />
<?php
printMessages($err);
echo "			</form>\n";
?>
