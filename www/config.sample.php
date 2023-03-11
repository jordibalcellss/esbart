<?php

//esbart
define('URL','http://localhost/esbart/');
define('TITLE','esbart');
define('LOCALE','en');
define('MODE','dark');
define('LOGGING',true);
define('MIN_PASSWORD_LENGTH',10);
define('HIDE_SECOND_SURNAME',true);
define('TOKEN_EXPIRES_H',24);
define('FROM_NAME','Organisation');
define('FROM_ADDR','noreply@example.com');
define('FROM_REPLYTO','replyto@example.com');
define('FOOTER_IMAGE_P','include/template/images/logo.png');
define('FOOTER_PRIVACY_POLICY_URL','');

//MariaDB
define('DB_HOST','mysql.mydomain.com');
define('DB_NAME','esbart');
define('DB_PORT',3306);
define('DB_USER','esbart');
define('DB_PASS','mysecret');

//LDAP
define('LDAP_TREE','dc=mydomain,dc=com');
define('LDAP_SEARCH_DN','ou=users,dc=mydomain,dc=com');
define('LDAP_SEARCH_FILTER','(objectclass=*)');
define('LDAP_USER','cn=manager,'.LDAP_TREE);

define('LDAP_USER_NAME','cn');
define('LDAP_USER_ID','uid');
define('LDAP_USER_EMAIL_ATTR','mail');
define('LDAP_USER_PHONE_ATTR','telephoneNumber');
define('LDAP_USER_RADIUS_SIMULTANEOUSUSE','radiusSimultaneousUse');
define('LDAP_USER_RADIUS_TUNNELPRIVATEGROUP','radiusTunnelPrivateGroupId');
define('LDAP_USER_RADIUS_EXPIRATION','radiusExpiration');

//define('LDAP_USER_ATTRS', LDAP_USER_NAME.','.LDAP_USER_ID.','.LDAP_USER_EMAIL_ATTR.','.LDAP_USER_PHONE_ATTR.','.LDAP_USER_RADIUS_SIMULTANEOUSUSE.','.LDAP_USER_RADIUS_TUNNELPRIVATEGROUP.','.LDAP_USER_RADIUS_EXPIRATION);
define('LDAP_USER_ATTRS', 'givenname,cn,uid,mail,homedirectory,telephoneNumber');
define('LDAP_USER_OBJ_CLASSES', 'inetOrgPerson,posixAccount,top');

define('LDAP_HOST','ldap.mydomain.com');
define('LDAP_PASS','myldappassword');
define('LDAP_PRIMARY_GROUP_ID','1');
define('LDAP_GROUP_EXCLUSIONS','everybody,workshop');
define('LDAP_GROUP_ATTR','member');
define('LDAP_GROUP_FILTER','groupOfNames');
define('LDAP_GROUPS_DN','ou=groups,dc=mydomain,dc=com');
define('LDAP_AUTH_GROUP',"cn=admins,".LDAP_GROUPS_DN);
define('LDAP_SAMBA_SID','1');

//PHP
define('DISPLAY_ERRORS',true);
define('ERROR_REPORTING',E_ALL);
define('TIME_LIMIT',30);

?>
