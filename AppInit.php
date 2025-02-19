<?php

require __DIR__ . '/vendor/autoload.php';

require_once("conf/settings.php");
require_once("conf/LogInit.php");

// If the BASE_FOLDER is not in the include path, the program can't be executed
$include_path = ini_get("include_path");
if(strpos(ini_get("include_path"), BASE_FOLDER) === false){
    // If the BASE_FOLDER is not included in the include_path try to include here
    ini_set("include_path", $include_path.":".BASE_FOLDER);
    $include_path = ini_get("include_path");
    if(strpos(ini_get("include_path"), BASE_FOLDER) === false) // If it was not added, then die() the script
        die("You need to include the root path for the repository in the include path through Apache config, .htacces, php.ini, .user.ini, etc\ninclude_path: $include_path\nBASE_FOLDER: ".BASE_FOLDER);
}

require_once("Core/Class.Logging.php");
require_once("Core/Class.DB_Access.php");
require_once("Core/Class.EPS_Map.php");
require_once("Core/Class.Graph.php");
require_once("Core/Class.Exceptions.php");
require_once("Core/Abstract.Class.DB_Object.php");
require_once("Core/Abstract.Class.Basic_Info.php");

require_once("Core/Class.Building.php");
require_once("Core/Class.CapturePoint.php");
require_once("Core/Class.Department.php");
require_once("Core/Class.Destination_Zone.php");
require_once("Core/Class.Door.php");
require_once("Core/Class.Edge.php");
require_once("Core/Class.Instruction.php");
require_once("Core/Class.Language.php");
require_once("Core/Class.Node.php");
require_once("Core/Class.Person.php");
require_once("Core/Class.Space.php");

$db_access = new DB_Access(DB_DRIVER, DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASSWORD);
$db_access->setLoggers(["error" => $error_logger, "debug" => $debug_logger]);
$eps_map = new EPS_Map($db_access);
$eps_map->setLoggers(["error" => $error_logger, "debug" => $debug_logger]);


?>