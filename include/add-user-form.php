			<h2><?=add?> <?=user?></h2>
			<h3><?=user_add_how_to_use?></h3>
			<ul>
				<li><?=user_add_advice_1?></li>
				<li><?=user_add_advice_2?></li>
			</ul>
			<form id="user" enctype="application/x-www-form-urlencoded" method="post" action="index.php?action=add&object=user&mode=manual">
				<div><label for="name"><?=name?>*</label></div>
				<div><input name="name" type="text" class="short" value="" /></div>
				
				<div><label for="username"><?=username?>*</label></div>
				<div><input name="username" type="text" class="short" value="" /></div>
				
				<div><label for="email"><?=email?></label></div>
				<div><input name="email" type="text" value="" /></div>
				
				<div><label for="homedirectory"><?=home_directory?></label></div>
				<div><input name="homedirectory" type="text" class="short" value="" /></div>
				
				<div><label for="loginshell"><?=shell?></label></div>
				<div><input name="loginshell" type="text" class="short" value="/bin/false" /></div>
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
