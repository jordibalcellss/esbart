<?php

//esbart
define('URL','http://localhost/esbart/');
define('TITLE','esbart');
define('LOCALE','en');
define('MODE','dark');
define('LOGGING',false);
define('MIN_PASSWORD_LENGTH',10);
define('HIDE_SECOND_SURNAME',true);
define('TOKEN_EXPIRES_H',24);
define('FROM_NAME','Organisation');
define('FROM_ADDR','noreply@example.com');
define('FROM_REPLYTO','replyto@example.com');
define('FOOTER_IMAGE','include/template/images/logo.png');
define('FOOTER_PRIVACY_POLICY_URL','');

//MariaDB
define('DB_HOST','localhost');
define('DB_NAME','esbart');
define('DB_PORT',3306);
define('DB_USER','mysql');
define('DB_PASS','mysql');

//LDAP
define('LDAP_TREE','dc=laptop,dc=local');
define('LDAP_USER','cn=ldapadm,'.LDAP_TREE);
define('LDAP_HOST','localhost');
define('LDAP_PASS','testldap');
define('LDAP_AUTH_GROUP','cn=tech,ou=groups,'.LDAP_TREE);
define('LDAP_PRIMARY_GROUP_ID','2001');
define('LDAP_GROUP_EXCLUSIONS','everybody,workshop');
define('LDAP_SAMBA_SID','S-1-5-21-1123581321-1123581321-1123581321');

//PHP
define('DISPLAY_ERRORS',true);
define('ERROR_REPORTING',E_ALL);
define('TIME_LIMIT',30);

?>
