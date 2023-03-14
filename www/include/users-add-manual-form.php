      <h2><?=add?> <?=user?></h2>
      <h3><?=user_add_how_to_use?></h3>
      <ul>
        <li><?=user_add_assisted_advice_1?></li>
        <li><?=user_add_assisted_advice_2?></li>
        <li><?=user_add_assisted_advice_3?></li>
      </ul>
      <form id="user" enctype="application/x-www-form-urlencoded" method="post" action="index.php?module=users&action=add&mode=manual">
        <div><label for="name_1"><?=name?>*</label></div>
        <div><input name="name_1" id="name_1" type="text" onfocusout="updateLogin()" value="" /></div>
        
        <div><label for="name_2"><?=surname?>*</label></div>
        <div><input name="name_2" id="name_2" type="text" onfocusout="updateLogin()" value="" /></div>

        <div><label for="login"><?=login?>*</label></div>
        <div><input name="login" id="login" type="text" value="" readonly="readonly"/></div>
      
        <div><label for="email"><?=email?>*</label></div>
        <div><input name="email" type="text" value="" /></div>
<?php
       
        $user_attr_array=preg_split ("/\,/", LDAP_USER_ATTRS);
        $common_attrs = array("mail", "email", "cn", "givenname", "sn", "uid");
        foreach ($user_attr_array as $attr) {
          if(!in_array($attr, $common_attrs)){
            echo "<div><label for='$attr'>$attr</label></div>";
            echo "<div><input name='$attr' id='$attr' type='text' value=''/></div>";
          }
        }

        echo "<div><label for='ou'>".ou.":</label></div>";
        echo "<div><select class='select-ous' name='ou'>";
        $ous = getOrganizationalUnits();
        
        foreach ($ous as $ou) {
          $o=$ou['dn'];
          if( strpos($o, LDAP_SEARCH_DN) !== false){          
            echo "<option value='$o'>$o</option>";
          }
        }
        echo "</select></div>";

        echo "<div><label for='groups'>".member_of.":</label></div>";
        echo "<div><select class='select-groups' name='groups[]' multiple>";
        $groups = getAssignableGroups();
        foreach ($groups as $group) {
          echo "<option value='$group'>$group</option>";
        }
        echo "</select></div>";
?>      
        <input name="submit" type="submit" value="<?=add?>" />
<?php
        printMessages($err);
        echo "</form>\n";
?>
        <script src="../scripts/users-add.js"></script>
