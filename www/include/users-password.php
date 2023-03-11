<?php

if (strlen($_POST['password']) == 0 || strlen($_POST['password_c']) == 0) {
  $err[] = both_fields_required;
}
else {
  if ($_POST['password'] != $_POST['password_c']) {
    $err[] = password_does_not_match;
  }
  else {
    if (strlen($_POST['password']) < MIN_PASSWORD_LENGTH) {
      $err[] = password_must_contain_at_least." ".MIN_PASSWORD_LENGTH." ".characters;
    }
    if (!preg_match("/[^a-zA-Z]/",$_POST['password'])) {
      $err[] = password_must_contain_at_least." ".num_symbol;
    }
    if (!preg_match("#[a-z]+#",$_POST['password'])) {
      $err[] = password_must_contain_at_least." ".lowercase_letter;
    }
    if (!preg_match("#[A-Z]+#",$_POST['password'])) {
      $err[] = password_must_contain_at_least." ".uppercase_letter;
    }
    if (!isset($err)) {
      //prepare entry
      $password = $_POST['password'];
      $uid = $_GET['object'];
      
      //set the samba password
      $entry['sambantpassword'] = strtoupper(hash('md4',iconv('UTF-8','UTF-16LE',$password)));
      $entry['sambapasswordhistory'] = '0000000000000000000000000000000000000000000000000000000000000000';
      $entry['sambapwdlastset'] = time();
      $entry['sambaacctflags'] = '[U          ]';
      
      //set the password
      $entry['userpassword'] = "{SHA}".base64_encode(pack("H*",sha1($password)));
      $res = ldap_mod_replace($con,'uid='.$uid.",".LDAP_SEARCH_DN,$entry);
      
      if (!$res) {
        $err_des = ldap_error($con);
        $err_num = ldap_errno($con);
        $err[] = "Error: LDAP $err_num - $error_desc.";
      }
      else {
        $err[] = new_password_ready;
      }
    }
  }
}

?>
