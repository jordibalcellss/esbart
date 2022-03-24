<?php
if (strlen($_POST['name']) == 0) {
	$err[] = name_cannot_be_empty;
}
if (strlen(trim($_POST['email'])) > 0) {
	if (!filter_var(trim($_POST['email']),FILTER_VALIDATE_EMAIL)) {
		$err[] = verify_email;
	}
}
if (!isset($err)) {
	//prepare entry
	$entry['cn'] = trim($_POST['name']);
	$entry['emailaddress'] = trim($_POST['email']);
	$entry['homedirectory'] = trim($_POST['homedirectory']);
	$entry['loginshell'] = trim($_POST['loginshell']);
			
	//add entry
	$res_entry = ldap_mod_replace($con,'uid='.$_GET['object'].",ou=users,".LDAP_TREE,$entry);
			
	//edit memberships
	if (isset($_POST["groups"])) {
		//get the groups the user is a member of
		$current_groups = getUserMembership($_GET['object']);
		//obtain the new groups to assign (the ones missing in the latest assignation)
		$new_groups = array_diff($_POST["groups"],$current_groups);
		//add the memberships
		addUserToGroups($_GET['object'],$new_groups);
					
		//get the groups the user is not a member of (the ones missing in the latest assignation)
		$foreign_groups = array_diff(getAssignableGroups(),$_POST["groups"]);
		//get the groups the user must be removed from (the ones missing in the latest assignation
		//but present in the expired membership)
		$remaining_groups = array_intersect($foreign_groups,$current_groups);
		//remove de memberships
		removeUserFromGroups($_GET['object'],$remaining_groups);
	}
				
	if ($res_entry) {
		$err[] = user_edit_success;
	}
	else {
		$err[] = a_problem_occurred;
	}
}
?>
