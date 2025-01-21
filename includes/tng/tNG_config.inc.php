<?php
// Array definitions
  $tNG_login_config = array();
  $tNG_login_config_session = array();
  $tNG_login_config_redirect_success  = array();
  $tNG_login_config_redirect_failed  = array();

// Start Variable definitions
  $tNG_debug_mode = "DEVELOPMENT";
  $tNG_debug_log_type = "";
  $tNG_debug_email_to = "you@yoursite.com";
  $tNG_debug_email_subject = "[BUG] The site went down";
  $tNG_debug_email_from = "webserver@yoursite.com";
  $tNG_email_host = "";
  $tNG_email_user = "";
  $tNG_email_port = "25";
  $tNG_email_password = "";
  $tNG_email_defaultFrom = "nobody@nobody.com";
  $tNG_login_config["connection"] = "vacante";
  $tNG_login_config["table"] = "vac_usuarios";
  $tNG_login_config["pk_field"] = "IDusuario";
  $tNG_login_config["pk_type"] = "NUMERIC_TYPE";
  $tNG_login_config["email_field"] = "usuario_correo";
  $tNG_login_config["user_field"] = "usuario";
  $tNG_login_config["password_field"] = "password";
  $tNG_login_config["level_field"] = "nivel_acceso";
  $tNG_login_config["level_type"] = "NUMERIC_TYPE";
  $tNG_login_config["randomkey_field"] = "";
  $tNG_login_config["activation_field"] = "activo";
  $tNG_login_config["password_encrypt"] = "true";
  $tNG_login_config["autologin_expires"] = "30";
  $tNG_login_config["redirect_failed"] = "default.php?info=DENIED";
  $tNG_login_config["redirect_success"] = "panel.php";
  $tNG_login_config["login_page"] = "f_index.php";
  $tNG_login_config["max_tries"] = "";
  $tNG_login_config["max_tries_field"] = "";
  $tNG_login_config["max_tries_disableinterval"] = "";
  $tNG_login_config["max_tries_disabledate_field"] = "";
  $tNG_login_config["registration_date_field"] = "";
  $tNG_login_config["expiration_interval_field"] = "";
  $tNG_login_config["expiration_interval_default"] = "";
  $tNG_login_config["logger_pk"] = "id";
  $tNG_login_config["logger_table"] = "vac_login_stats";
  $tNG_login_config["logger_user_id"] = "id_log";
  $tNG_login_config["logger_ip"] = "ip_log";
  $tNG_login_config["logger_datein"] = "last_logindate_log";
  $tNG_login_config["logger_datelastactivity"] = "last_activitydate_log";
  $tNG_login_config["logger_session"] = "session_log";
  $tNG_login_config_redirect_success["1"] = "panel.php";
  $tNG_login_config_redirect_failed["1"] = "default.php?info=DENIED";
  $tNG_login_config_redirect_success["2"] = "panel.php";
  $tNG_login_config_redirect_failed["2"] = "default.php?info=DENIED";
  $tNG_login_config_redirect_success["3"] = "panel.php";
  $tNG_login_config_redirect_failed["3"] = "default.php?info=DENIED";
  $tNG_login_config_redirect_success["4"] = "panel.php";
  $tNG_login_config_redirect_failed["4"] = "default.php?info=DENIED";
  $tNG_login_config_redirect_success["5"] = "panel.php";
  $tNG_login_config_redirect_failed["5"] = "default.php?info=DENIED";
  $tNG_login_config_session["kt_login_id"] = "IDusuario";
  $tNG_login_config_session["kt_login_user"] = "usuario";
  $tNG_login_config_session["kt_login_level"] = "nivel_acceso";
  $tNG_login_config_session["kt_IDmatriz"] = "IDmatriz";
  $tNG_login_config_session["kt_IDsistema"] = "sistema";
// End Variable definitions
?>