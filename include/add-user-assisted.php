<?php

if (strlen($_POST['name_1']) == 0 || strlen($_POST['name_2']) == 0 || strlen($_POST['email']) == 0) {
  $err[] = user_add_assisted_name_surname_email_required;
}
else {
  if (!filter_var(trim($_POST['email']),FILTER_VALIDATE_EMAIL)) {
    $err[] = verify_email;
  }
  else {
    $name_1_f = trimSplitFormatName($_POST['name_1'],0);
    $name_2_f = trimSplitFormatSurname($_POST['name_2'],1);
    
    if (isset($_POST['name_3'])) {
      if (strlen($_POST['name_3']) != 0) {
        $name_3_f = trimSplitFormatSurname($_POST['name_3'],1);
        $name_3 = ' '.trim($_POST['name_3']);
      }
      else {
        $name_3_f = '';
        $name_3 = '';
      }
    }
    else {
      $name_3_f = '';
      $name_3 = '';
    }
    
    if (getPersonalData($name_1_f.'.'.$name_2_f.$name_3_f)) {
      $err[] = user_already_exists;
    }
    if (!isset($err)) {
      //prepare entry
      $uidnumber = getNextId($con,'user');
      $entry['objectclass'][0] = 'top';
      $entry['objectclass'][1] = 'account';
      $entry['objectclass'][2] = 'posixAccount';
      $entry['objectclass'][3] = 'shadowAccount';
      $entry['objectclass'][4] = 'extensibleObject';
      $entry['objectclass'][5] = 'sambaSamAccount';
      $entry['cn'] = trim($_POST['name_1']).' '.trim($_POST['name_2']).$name_3;
      $entry['uid'] = $name_1_f.'.'.$name_2_f.$name_3_f;
      $entry['uidnumber'] = $uidnumber;
      $entry['gidnumber'] = LDAP_PRIMARY_GROUP_ID;
      $entry['homedirectory'] = '/home/'.$name_1_f.'.'.$name_2_f.$name_3_f;
      $entry['loginshell'] = '/bin/false';
      $entry['emailaddress'] = trim($_POST['email']);
      $entry['sambasid'] = getSambaSID($uidnumber);
      
      //add entry
      $res_entry = ldap_add($con,'uid='.$name_1_f.'.'.$name_2_f.$name_3_f.",ou=users,".LDAP_TREE,$entry);
      
      //add memberships
      if (isset($_POST["groups"])) {
        addUserToGroups($name_1_f.'.'.$name_2_f.$name_3_f,$_POST["groups"]);
      }
        
      if ($res_entry) {
        $err[] = user_add_success;
        if (sendOneTimeSetPasswordEmail($name_1_f.'.'.$name_2_f.$name_3_f,false)) {
          $err[] = user_add_welcome_email_sent;
        }
        else {
          $err[] = user_add_welcome_email_not_sent;
        }
      }
      else {
        $err[] = a_problem_occurred;
      }
    }
  }
}

?>
