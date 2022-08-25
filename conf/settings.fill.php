<?php

/**
 * DO NOT MODIFY THIS FILE
 * Instead, copy this file and rename it to settings.php, modify the values there
 */

/*****************************/
/** Folders for the project **/
/*****************************/

/** Directory where the application is installed.
 * For example "C:/xampp/htdocs/EPSMap" or "/home/epsmap/EPSMap" WITHOUT the trailing slash. */
define("BASE_FOLDER", "");
/** Directory where the images for the instructions will be saved
 * Usually within the same folder where the application is installed but nothing forbids you from
 * using any other directory. */
define("INSTRUCTION_IMAGE_FOLDER", BASE_FOLDER."/Core/images/");



/*****************************/
/********* Languages *********/
/*****************************/

/** Default language for the application if the user does not specify any language.
 * Must be one of the available languages in the database */
define("DEFAULT_LANGUAGE", "ca");



/*****************************/
/** Database configuration ***/
/*****************************/

/** Driver to use for the PDO */
define("DB_DRIVER", "mysql");
/** Host where the database is located
 * For example "localhost". If you need a socket to connect to the database you can put it
 * here as well: "localhost;unix_socket=/var/lib/mysql/mysql.sock" */
define("DB_HOST", "localhost");
/** Port to connect to the database */
define("DB_PORT", 3306);
/** Database name */
define("DB_NAME", "eps_map");
/** User for connecting to the database.
 * Make sure the permissions are correct */
define("DB_USER", "root");
/** Password for connecting to the database. */
define("DB_PASSWORD", null);



/*****************************/
/*** Monolog configuration ***/
/*****************************/

/** File where the errors will be written.
 * Make sure the apache user has write permission over the directory and file. */
define("LOG_ERROR_FILE", BASE_FOLDER."/Logs/error.log");
/** File where the debug messages will be written.
 * Make sure the apache user has write permission over the directory and file. */
define("LOG_DEBUG_FILE", BASE_FOLDER."/Logs/debug.log");

?>