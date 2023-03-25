<?php

class DB extends PDO {
  public function __construct() {
    $dsn = "mysql:host=".DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME;
    parent::__construct($dsn, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_FOUND_ROWS => true));
  }
}

function LDAPconnect() {
  $con = ldap_connect(LDAP_HOST);
  ldap_set_option($con,LDAP_OPT_PROTOCOL_VERSION,3);
  if ($con) {
    $bind = ldap_bind($con,LDAP_USER,LDAP_PASS);
  }
  return array($con,$bind);
}

function printMessages($err) {
  //displays HTML form error messages
  if (count($err)) {
    echo '      <div id="messages">'."\n";
    echo '        '.implode('<br />',$err)."\n";
    echo "      </div>\n";
  }
}
  
function writeLog($filename,$message) {
  if (LOGGING) {
    $dir = 'log/';
    if (!is_dir($dir)) {
      mkdir($dir);
    }
    $file = fopen($dir.$filename,'a');
    $date = new DateTime(null, new DateTimeZone('UTC'));
    $data = $date->format('D M d H:i:s e Y').' '.$_SERVER['REMOTE_ADDR'].' '.$_POST['username'].": $message\n";
    fwrite($file,$data);
    fclose($file);
  }
}

function sortByName($a,$b) {
  return strtolower($a['cn'][0]) > strtolower($b['cn'][0]);
}

function getNextId($con,$entity) {
  //returns next available ID for either users or groups
  if ($entity == 'user') {
    $attr = 'uidnumber';
  }
  else if ($entity == 'group') {
    $attr = 'gidnumber';
  }
  $result = ldap_search($con,"ou=$entity"."s,".LDAP_TREE,"(cn=*)",array($attr));
  $entries = ldap_get_entries($con,$result);
  $numbers = array();
  for ($i = 0; $i < $entries['count']; $i++) {
    $numbers[] = $entries[$i][$attr][0];
  }
  if (count($numbers) > 0) {
    return max($numbers) + 1;
  }
  else {
    //not likely to happen, but using the same ID schema across entities
    return LDAP_PRIMARY_GROUP_ID + 1;
  }
}

function getSambaSID($uidnumber) {
  $samba_id = $uidnumber * 2 + 1000;
  return LDAP_SAMBA_SID.'-'.$samba_id;
}

function getPersonalData($uid) {
  /*
   * returns an array with the following attributes
   * 0 given name
   * 1 full name
   * 2 login
   * 3 email
   * 4 home directory
   * 5 shell
   * 
   * returns false on failure
   */
  $con = LDAPconnect();
  $result = @ldap_read($con[0],"uid=$uid,ou=users,".LDAP_TREE,"(cn=*)",array('cn','uid','email','homedirectory','loginshell'));
  if ($result) {
    $entries = ldap_get_entries($con[0],$result);
    $pd[0] = explode(' ',$entries[0]['cn'][0])[0];
    $pd[1] = $entries[0]['cn'][0];
    $pd[2] = $entries[0]['uid'][0];
    $pd[3] = $entries[0]['email'][0];
    $pd[4] = $entries[0]['homedirectory'][0];
    $pd[5] = $entries[0]['loginshell'][0];
    ldap_close($con[0]);
    return $pd;
  }
  else {
    ldap_close($con[0]);
    return false;
  }
}

function accountHasEmail($uid) {
  $con = LDAPconnect();
  $result = ldap_read($con[0],"uid=$uid,ou=users,".LDAP_TREE,"(cn=*)",array('email'));
  $entries = ldap_get_entries($con[0],$result);
  ldap_close($con[0]);
  if (strlen($entries[0]['email'][0]) > 0) {
    return true;
  }
  else {
    return false;
  }
}

function accountIsEnabled($uid) {
  //an account is considered active if either userpassword or sambantpassword are set
  $con = LDAPconnect();
  $result = ldap_read($con[0],"uid=$uid,ou=users,".LDAP_TREE,"(cn=*)",array('userpassword','sambantpassword'));
  $entries = ldap_get_entries($con[0],$result);
  ldap_close($con[0]);
  if (isset($entries[0]['userpassword'][0]) || isset($entries[0]['sambantpassword'][0])) {
    return true;
  }
  else if (!isset($entries[0]['userpassword'][0]) && !isset($entries[0]['sambantpassword'][0])){
    return false;
  }
}

function disableAccount($uid) {
  //removes userpassword and/or sambantpassword attributes
  if (getPersonalData($uid)) {
    $con = LDAPconnect();
    $result = ldap_read($con[0],"uid=$uid,ou=users,".LDAP_TREE,"(cn=*)",array('userpassword','sambantpassword'));
    $entries = ldap_get_entries($con[0],$result);
    if (isset($entries[0]['userpassword'][0])) {
      ldap_mod_del($con[0],"uid=$uid,ou=users,".LDAP_TREE,array('userpassword' => array()));
    }
    if (isset($entries[0]['sambantpassword'][0])) {
      ldap_mod_del($con[0],"uid=$uid,ou=users,".LDAP_TREE,array('sambantpassword' => array()));
    }
    ldap_close($con[0]);
    return true;
  }
  else {
    return false;
  }
}

function addUserToGroups($uid,$groups) {
  //expects an array of group names
  $con = LDAPconnect();
  $entry['memberuid'] = $uid;
  $fail = false;
  foreach ($groups as $group) {
    //the select html tag may send an empty string
    if (strlen($group) > 0) {
      $result = ldap_mod_add($con[0],"cn=$group,ou=groups,".LDAP_TREE,$entry);
        if (!$result) {
          $fail = true;
      }
    }
  }
  ldap_close($con[0]);
  return !$fail;
}

function removeUserFromGroups($uid,$groups) {
  //expects an array of group names
  $con = LDAPconnect();
  $entry['memberuid'] = $uid;
  $fail = false;
  foreach ($groups as $group) {
    $result = ldap_mod_del($con[0],"cn=$group,ou=groups,".LDAP_TREE,$entry);
    if (!$result) {
      $fail = true;
    }
  }
  ldap_close($con[0]);
  return !$fail;
}

function getAssignableGroups() {
  //returns an array of group names
  $con = LDAPconnect();
  $result = ldap_search($con[0],'ou=groups,'.LDAP_TREE,"(cn=*)",array('cn'));
  $entries = ldap_get_entries($con[0],$result);
  ldap_close($con[0]);
  $groups = array();
  for ($i = 0; $i < $entries['count']; $i++) {
    if (array_search($entries[$i]['cn'][0],explode(',',LDAP_GROUP_EXCLUSIONS)) === false) {
      $groups[] = $entries[$i]['cn'][0];
    }
  }
  return $groups;
}

function getGroupMembers($cn) {
  //returns an array of uids
  $con = LDAPconnect();
  $result = ldap_search($con[0],"cn=$cn,ou=groups,".LDAP_TREE,"(cn=*)",array('memberuid'));
  $entries = ldap_get_entries($con[0],$result);
  ldap_close($con[0]);
  $members = array();
  if (isset($entries[0]['memberuid'])) {
    for ($i = 0; $i < $entries[0]['memberuid']['count']; $i++) {
      $members[] = $entries[0]['memberuid'][$i];
    }
  }
  return $members;
}

function getUserMembership($uid) {
  //returns an array of group names
  $con = LDAPconnect();
  $result = ldap_search($con[0],'ou=groups,'.LDAP_TREE,"(cn=*)",array('cn','memberuid'));
  $entries = ldap_get_entries($con[0],$result);
  ldap_close($con[0]);
  $groups = array();
  for ($i = 0; $i < $entries['count']; $i++) {
    if (isset($entries[$i]['memberuid'])) {
      for ($j = 0; $j < $entries[$i]['memberuid']['count']; $j++) {
        if ($entries[$i]['memberuid'][$j] == $uid) {
          $groups[] = $entries[$i]['cn'][0];
          break;
        }
      }
    }
  }
  return $groups;
}

function trimSplitFormatName($name) {
  /* 
   * some european naming customs (like the catalan one) may include composite two-word
   * names or even composite surnames with a preposition in front of them. We'll select
   * only one word to form up the username.
   */
  if (strpos($name,' ') !== false) {
    $name_a = explode(' ',trim($name));
    //special characters are transliterated to ASCII
    //any other non alphabetic character is replaced by an underscore
    return preg_replace('/[^a-zA-Z]/','_',strtolower(iconv('UTF-8','ASCII//TRANSLIT',$name_a[0])));
  }
  else {
    return preg_replace('/[^a-zA-Z]/','_',strtolower(iconv('UTF-8','ASCII//TRANSLIT',trim($name))));
  } 
}

function trimSplitFormatSurname($name) {
  if (strpos($name,' ') !== false) {
    $name_a = explode(' ',trim($name));
    return preg_replace('/[^a-zA-Z]/','_',strtolower(iconv('UTF-8','ASCII//TRANSLIT',mb_substr($name_a[1],0,1))));
  }
  else {
    return preg_replace('/[^a-zA-Z]/','_',strtolower(iconv('UTF-8','ASCII//TRANSLIT',mb_substr(trim($name),0,1))));
  } 
}

function sendOneTimeSetPasswordEmail($uid,$manual) {
  /*
   * sends an email with a link to reset the password
   * if 'manual' GET parameter is set, the welcome email template is not used
   * and the user is presented with a generic password reset email instead
   * 
   * returns false on failure
   */
  if ($pd = getPersonalData($uid)) {
    $headers[] = 'From: '.FROM_NAME.' <'.FROM_ADDR.'>';
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=utf-8';
    $headers[] = 'X-Mailer: '.TITLE;
    $headers[] = 'X-Mailer: PHP/'.phpversion();
    $headers[] = 'X-PHP-Originating-Script: '.TITLE;
  
    //create a random string token
    $pass = bin2hex(openssl_random_pseudo_bytes(8));
    $url = URL."/set.php?p=$pass";
  
    if ($manual) {
      $url = $url."&man";
      $subject = reset_password;
      $message = "<html><p>".ucfirst(greeting)." $pd[0],</p>
<p>".somebody_offered_reset_link."</p>
<p><a href=\"$url\">$url</a></p>
<p>".if_unsolicited_ignore."</p>
<p>".thank_you."</p>
<img src=\"".URL."/".FOOTER_IMAGE."\" alt=\"".FROM_NAME."\" />
<p style=\"font-size: 12px;\"><a href=\"".FOOTER_PRIVACY_POLICY_URL."\">".FROM_NAME." - ".privacy_policy."</a></p>
</html>";
  }
  else {
      $subject = welcome_to." ".FROM_NAME;
      $message = "<html><p>".ucfirst(greeting)." $pd[0],</p>
<p>".welcome_set_password_via."</p>
<p><a href=\"$url\">$url</a></p>
<p>".welcome_access_advice."</p>
<p>".thank_you."</p>
<img src=\"".URL."/".FOOTER_IMAGE."\" alt=\"".FROM_NAME."\" />
<p style=\"font-size: 12px;\"><a href=\"".FOOTER_PRIVACY_POLICY_URL."\">".FROM_NAME." - ".privacy_policy."</a></p>
</html>";
  }

    $result_mail = mail($pd[3],$subject,$message,implode("\r\n",$headers));

    /*
     * insert the token into the database
     * the token will be tied to a username and expired by cron.php
     * in case it is overlooked or rejected
     */
    $db = new DB();
    $stmt = $db->prepare('INSERT INTO pw_set_requests (pass,user_id) VALUES (:pass,:user_id)');
    $result_db = $stmt->execute(array(':pass' => $pass,':user_id' => $uid));
  
    if ($result_mail && $result_db) {
      return true;
    }
    else {
      return false;
    }
  }
  else {
    return false;
  }
}

function sendWelcomeEmail($uid) {
  /*
   * sends the welcome email
   * called after the password is successfully set in the last steps
   * of the user creation process
   * 
   * returns false on failure
   */
  $pd = getPersonalData($uid);
  $subject = 'El teu compte a '.FROM_NAME.' ja és actiu';
  
  $headers[] = 'From: '.FROM_NAME.' <'.FROM_ADDR.'>';
  $headers[] = 'Reply-To: '.FROM_REPLYTO;
  $headers[] = 'MIME-Version: 1.0';
  $headers[] = 'Content-type: text/html; charset=utf-8';
  $headers[] = 'X-Mailer: '.TITLE;
  $headers[] = 'X-Mailer: PHP/'.phpversion();
  $headers[] = 'X-PHP-Originating-Script: '.TITLE;
  
  $message = "<html><p>".ucfirst(greeting_again).",</p>
<p>".account_ready." <strong>$pd[2]</strong>. ".account_ready_advice."</p>
<ul>
<li>".account_ready_server."</li>
<li>".ucfirst(username).": $pd[2]</li>
</ul>
<p>".account_ready_explanation."</p>
<p></p>
<p>".cheers."</p>
<img src=\"".URL."/".FOOTER_IMAGE."\" alt=\"".FROM_NAME."\" />
<p style=\"font-size: 12px;\"><a href=\"".FOOTER_PRIVACY_POLICY_URL."\">".FROM_NAME." - ".privacy_policy."</a></p>
</html>";

  if (mail($pd[3],$subject,$message,implode("\r\n",$headers))) {
    return true;
  }
  else {
    return false;
  }
}

?>
