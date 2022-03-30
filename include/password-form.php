<?php
if ($pd = getPersonalData($_GET['object'])) {
	if (isset($_GET['noemail'])) {
?>
			<h2><?=set_reset_password_header?></h2>
			<p><?=set_password_requirements?></p>
			<form id="password" enctype="application/x-www-form-urlencoded" method="post" action="index.php?action=password&object=<?=$_GET['object']?>&noemail">
				<div><?=username?></div>
				<div class="not-editable-form-item"><?=$_GET['object']?></div>
				
				<div><label for="password"><?=password?></label></div>
				<div><input name="password" type="password" class="short" value="" /></div>
				
				<div><label for="password_c"><?=repeat?> <?=password?></label></div>
				<div><input name="password_c" type="password" class="short" value="" /></div>
			
				<input name="submit" type="submit" value="<?=create?>" />
<?php
		printMessages($err);
		echo "			</form>\n";
	}
	else {
		if (sendOneTimeSetPasswordEmail($_GET['object'],true)) {
			echo "<p>".one_time_password_email_sent." ".$_GET['object']."</p>";
		}
	}
}
else {
	echo "<p>".user_does_not_exist."</p>";
}
?>
