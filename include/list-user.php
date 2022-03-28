<?php
$result = ldap_search($con,'ou=users,'.LDAP_TREE,"(cn=*)",array('cn','email','uidnumber','uid'));
$entries = ldap_get_entries($con,$result);
//sort alphabetically
usort($entries,"sortByName");
//prepare table
echo "			<h2>".user_list."</h2>\n";
echo "			<table>\n";
echo "				<tr>\n";
echo "					<th>".name."</th>\n";
echo "					<th>".login."</th>\n";
echo "					<th>".email."</th>\n";
echo "					<th>".id."</th>\n";
echo "					<th>".member_of."</th>\n";
echo "					<th align=\"right\">".actions."</th>\n";
echo "				</tr>\n";
for ($i = 1; $i < count($entries); $i++) {
	if (accountHasEmail($entries[$i]['uid'][0])) {
		$password = '<a href="?action=password&object='.$entries[$i]['uid'][0].'">'.password.'</a>';
	}
	else {
		$password = '<a href="?action=password&object='.$entries[$i]['uid'][0].'&noemail">'.password.'</a>';
	}
	if (accountIsEnabled($entries[$i]['uid'][0])) {
		$disable = '&nbsp;&nbsp;<a href="?action=disable&object='.$entries[$i]['uid'][0].'">'.disable.'</a>';
	}
	else {
		$disable = '';
	}
	$edit = '&nbsp;&nbsp;<a href="?action=edit&object='.$entries[$i]['uid'][0].'">'.edit.'</a>';
	echo "				<tr>\n";
	echo '					<td width="200">'.$entries[$i]['cn'][0]."</td>\n";
	echo '					<td width="100">'.$entries[$i]['uid'][0]."</td>\n";
	echo '					<td width="300">'.$entries[$i]['email'][0]."</td>\n";
	echo '					<td width="70">'.$entries[$i]['uidnumber'][0]."</td>\n";
	echo '					<td>'.implode(', ',getUserMembership($entries[$i]['uid'][0]))."</td>\n";
	echo '					<td align="right" width="200">'.$password.$disable.$edit."</td>\n";
	echo "				</tr>\n";
}
echo "			</table>\n";
echo "			<p>".there_are." ".ldap_count_entries($con,$result)." ".users."</p>\n";
?>
