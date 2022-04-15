<?php

$result = ldap_search($con,'ou=groups,'.LDAP_TREE,"(cn=*)",array('cn','gidnumber'));
$entries = ldap_get_entries($con,$result);
//sort alphabetically
usort($entries,"sortByName");
//prepare table
echo "			<h2>".group_list."</h2>\n";
echo "			<table>\n";
echo "				<tr>\n";
echo "					<th>".name."</th>\n";
echo "					<th>".id."</th>\n";
echo "					<th>".members."</th>\n";
echo "				</tr>\n";
for ($i = 1; $i < count($entries); $i++) {
	echo "				<tr>\n";
	echo '					<td width="130">'.$entries[$i]['cn'][0]."</td>\n";
	echo '					<td width="70">'.$entries[$i]['gidnumber'][0]."</td>\n";
	echo '					<td>'.implode(', ',getGroupMembers($entries[$i]['cn'][0]))."</td>\n";
	echo "				</tr>\n";
}
echo "			</table>\n";
echo "			<p>".there_are." ".ldap_count_entries($con,$result)." ".groups."</p>\n";

?>
