			<h2><?=add?> <?=user?> - <?=assisted_mode?></h2>
			<h3><?=user_add_how_to_use?></h3>
			<ul>
				<li><?=user_add_assisted_advice_1?></li>
				<li><?=user_add_assisted_advice_2?></li>
				<li><?=user_add_assisted_advice_3?></li>
			</ul>
			<form id="user" enctype="application/x-www-form-urlencoded" method="post" action="index.php?action=add&object=user&mode=assisted">
				<div><label for="name_1"><?=name?>*</label></div>
				<div><input name="name_1" type="text" class="short" value="" /></div>
				
				<div><label for="name_2"><?=surname?>*</label></div>
				<div><input name="name_2" type="text" class="short" value="" /></div>
<?php
if (!HIDE_SECOND_SURNAME) {
	echo "				<div><label for=\"name_3\">".surname_2."</label></div>\n";
	echo "				<div><input name=\"name_3\" type=\"text\" class=\"short\" value=\"\" /></div>\n";
}
?>			
				<div><label for="email"><?=email?>*</label></div>
				<div><input name="email" type="text" value="" /></div>
<?php
echo "\n";
echo "				<div><label for=\"groups\">".member_of.":</label></div>\n";
echo "				<div><select name=\"groups[]\" multiple>\n";
echo "					<option value=\"\"></option>\n";
$groups = getAssignableGroups();
foreach ($groups as $group) {
	echo "					<option value=\"$group\">$group</option>\n";
}
echo "				</select></div>\n";
?>			
				<input name="submit" type="submit" value="<?=add?>" />
<?php
printMessages($err);
echo "			</form>\n";

?>
