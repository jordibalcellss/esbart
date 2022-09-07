<?php

define("username","login");
define("password","password");
define("login_submit","login");
define("both_fields_required","both fields are required");
define("both_fields_required_log","empty fields");
define("unauthorized","you have no permission");
define("unauthorized_log","unauthorized");
define("user_and_or_password_incorrect","user and/or password incorrect");
define("logged_in","logged in");

define("greeting","hi");
define("greeting_again","hi again");
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
define("was_disabled","was disabled");

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
define("privacy_policy","privacy policy");

define("user_add_assisted_name_surname_email_required","name, surname and email are required");
define("name_cannot_be_empty","name cannot be empty");
define("name_username_cannot_be_empty","name and username cannot be empty");
define("username_legal_characters","username can only have lowercase characters, numbers and dots");
define("verify_email","verify the email, please");
define("user_add_success","the account was successfully created");
define("user_edit_success","the account was edited successfully");
define("user_add_welcome_email_sent","the welcome email was sent to");
define("user_add_welcome_email_not_sent","an error occurred with the welcome email");
define("a_problem_occurred","an error ocurred");
define("group_add_success","the group was successfully created");
define("group_already_exists","this group already exists");
define("user_already_exists","this user already exists");
define("group_naming_advice","it is recommended to avoid spaces and special characters");

define("user_add_how_to_use","how to use this form");
define("user_add_advice_1","only the name and surname are required");
define("user_add_advice_2","an empty home directory sets a folder under /home automatically");
define("user_add_assisted_advice_1","fill in the name and the surname");
define("user_add_assisted_advice_2","double check to the email address: it will be sent the welcome setup");
define("user_add_assisted_advice_3","the groups allow multiple selection");

define("one_time_password_email_sent","a form has been sent to");
define("user_does_not_exist","this user can't seem to exist");
define("reactivate_pw_request_necessary","to reactivate a password reset is necessary");
define("reinvite","reinvite");

define("one_time_link_has_expired","this link has expired");
define("password_does_not_match","the password does not match");
define("password_must_contain_at_least","the password must be, at least,");
define("characters","characters");
define("num_symbol","a number or a symbol");
define("lowercase_letter","an uppercase letter");
define("uppercase_letter","a lowercase letter");

define("password_created_confirmation_sent","thanks for creating it, we've just sent you an email on how to use your new account");
define("new_password_ready","all good, the new password is ready");

define("set_password_header","set password");
define("set_reset_password_header","(re)set password");
define("set_password_requirements","it must be at least ".MIN_PASSWORD_LENGTH." characters long, contain a lowercase letter, an uppercase one, and a number or a symbol");

define("reset_password","Reset the password");
define("somebody_offered_reset_link","Somebody in ".FROM_NAME." offered you an account password reset. You can reset it following this link:");
define("if_unsolicited_ignore","If you haven't requested it, ignore this email.");
define("thank_you","Thank-you.");
define("cheers","Regards");

define("welcome_to","Welcome to");
define("welcome_set_password_via","Welcome to ".FROM_NAME.". Please, create a password for your new account following this link:");
define("welcome_access_advice","The link will expire past ".TOKEN_EXPIRES_H." hours.");

define("account_ready","Your new account is ready. Your user name is");
define("account_ready_advice","");
define("account_ready_server","");
define("account_ready_explanation","");
define("account_ready_help","If you need help with any service, you can reach us at ".FROM_REPLYTO.".");

?>
