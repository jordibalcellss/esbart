<?php
if (strlen($_POST['name']) == 0) {
	$err[] = name_cannot_be_empty;
}
else {
	//prepare entry
	$entry['objectclass'][0] = 'top';
	$entry['objectclass'][1] = 'posixGroup';
	$entry['gidnumber'] = LDAPgetNextId($con,'group');
			
	//add entry
	$res_entry = @ldap_add($con,'cn='.trim($_POST['name']).",ou=groups,".LDAP_TREE,$entry);
			
	if ($res_entry) {
		$err[] = group_add_success;
	}
	else {
		if (ldap_errno($con) == 68) {
			$err[] = group_already_exists;
		}
		else {
			$err[] = a_problem_occurred;
		}
	}
}
?>
