<?php
define("username","login");
define("password","password");
define("login_submit","login");
define("all_fields_required","no deixeu cap camp buit");
define("unauthorized","no teniu pas permís");
define("user_and_or_password_incorrect","usuari i/o contrasenya incorrecta");

define("greeting","hi");
define("greeting_again","hola de nou");
define("logout","log out");

define("user","user");
define("group","group");
define("users","users");
define("groups","groups");
define("user_list","user list");
define("group_list","group list");

define("assisted_mode","assisted mode");
define("add","create");
define("list_action","list");
define("disable","disable");
define("edit","edit");
define("repeat","repeat");
define("create","set");
define("save","save");
define("was_disabled","s'ha desactivat");

define("name","name");
define("surname","surname");
define("surname_2","mother's surname");
define("login","login");
define("email","email");
define("id","id");
define("home_directory","home directory");
define("shell","shell");
define("member_of","member of");
define("members","members");
define("actions","actions");
define("there_are","there are");

define("user_add_assisted_name_surname_email_required","el nom, primer cognom i correu-e són necessaris");
define("name_cannot_be_empty","el nom no pot ser pas buit");
define("name_username_cannot_be_empty","el nom i nom d'usuari no poden ser pas buits");
define("username_legal_characters","el nom d'usuari només pot tenir minúscules, números i punts");
define("verify_email","verifiqueu el correu-e, si us plau");
define("user_add_success","s'ha creat el compte correctament");
define("user_edit_success","s'ha editat el compte amb èxit");
define("user_add_welcome_email_sent","hem enviat el missatge de benvinguda");
define("user_add_welcome_email_not_sent","hi ha hagut algun problema amb el missatge de benvinguda");
define("a_problem_occurred","hi ha hagut algun problema");
define("group_add_success","s'ha creat el grup correctament");
define("group_already_exists","aquest grup ja existeix");
define("user_already_exists","aquest usuari ja existeix");
define("group_naming_advice","it is recommended to avoid spaces and special characters");

define("user_add_how_to_use","how to use this form");
define("user_add_advice_1","only the name and surname are required");
define("user_add_advice_2","an empty home directory sets a folder under /home automatically");
define("user_add_assisted_advice_1","fill in the name and the surname");
define("user_add_assisted_advice_2","double check to the email address: it will be sent the welcome setup");
define("user_add_assisted_advice_3","the groups allow multiple selection");

define("one_time_password_email_sent","s'ha enviat un formulari a");
define("user_does_not_exist","no sembla que aquest usuari existeixi");
define("reactivate_pw_request_necessary","per a reactivar-lo, cal sol·licitar una nova contrasenya");

define("password_does_not_match","la contrasenya no coincideix");
define("password_must_contain_at_least","la contrasenya ha de tenir, almenys,");
define("characters","caràcters");
define("num_symbol","un número o un símbol");
define("lowercase_letter","una lletra minúscula");
define("uppercase_letter","una lletra majúscula");

define("password_created_confirmation_sent","gràcies per crear-la, tot just t'hem enviat un altre correu-e explicant com usar el teu nou compte");
define("new_password_ready","tot bé, ja es pot usar la nova contrasenya");

define("set_password_header","set password");
define("set_password_requirements","it must be at least ".MIN_PASSWORD_LENGTH." characters long, contain a lowercase letter, an uppercase one, and a number or a symbol");

define("reset_password","Restabliu la contrasenya");
define("somebody_offered_reset_link","Algú a ".FROM_NAME." us està oferint canviar la contrasenya del vostre compte. Podeu restablir-la a través d'aquest enllaç:");
define("if_unsolicited_ignore","Si no ho heu demanat, ignoreu aquest missatge.");
define("thank_you","Gràcies.");
define("cheers","Salut!");

define("welcome_to","Benvingut a");
define("welcome_set_password_via","Benvingut/da a ".FROM_NAME.". Si us plau, crea una contrasenya pel teu nou compte a través d'aquest enllaç:");
define("welcome_access_advice","Et sortirà un avís quan intentis entrar-hi, sobre el certificat SSL. Pots ignorar-lo afegint l'excepció de manera permanent, el certificat és segur.");

define("account_ready","El teu compte ja està preparat. El teu nom d'usuari és");
define("account_ready_advice","Si necessites accedir a ownCloud, ho pots fer a través de l'<a href=\"https://owncloud.eixamcoop.cat\">aplicatiu web</a>, o bé per mitjà de l'aplicació d'escriptori, que et permet sincronitzar automàticament els fitxers. La pots descarregar en <a href=\"https://owncloud.com/desktop-app/\">aquest enllaç</a>. Una vegada instal·lat, has d'afegir un compte nou:");
define("account_ready_server","Servidor: https://owncloud.eixamcoop.cat");
define("account_ready_explanation","El programa crearà la carpeta <em>ownCloud</em> al directori d'usuari del teu ordinador. Et recomanem que usis fitxers virtuals ja que fan un ús raonable de l'espai de disc. També pots seleccionar la carpeta (o carpetes) que vols sincronitzar. Això depèn del projecte en el qual treballis.");
define("account_ready_help","Si tens algun problema amb algun dels serveis, pots enviar un correu a ".FROM_REPLYTO.".");
?>
