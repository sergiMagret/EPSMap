<?php

require_once("settings.php");

// If the BASE_FOLDER is not in the include path, the program can't be executed
$include_path = ini_get("include_path");
if(strpos(ini_get("include_path"), BASE_FOLDER) === false){
    die("You need to include the root path for the repository in the include path through Apache config, .htacces, php.ini, .user.ini, etc");
}

require_once("Core/Class.DB_Access.php");
require_once("Core/Class.EPS_Map.php");
require_once("Core/Class.Exceptions.php");
require_once("Core/Abstract.Class.DB_Object.php");
require_once("Core/Abstract.Class.Basic_Info.php");
require_once("Core/Class.Building.php");
require_once("Core/Class.Department.php");
require_once("Core/Class.Space.php");
require_once("Core/Class.Language.php");
require_once("Core/Class.Node.php");
require_once("Core/Class.Door.php");
require_once("Core/Class.Person.php");
require_once("Core/Class.Edge.php");
require_once("Core/Class.Destination_Zone.php");

$db_access = new DB_Access(DB_DRIVER, DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASSWORD);
$eps_map = new EPS_Map($db_access);

// TODO S'ha de posar el Monolog per poder loggejar els errors!!

?>