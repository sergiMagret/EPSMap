<?php

require_once("../AppInit.php");
require_once("action_functions.php");

// Use FILTER_VALIDATE_BOOLEAN instead of FILTER_VALIDATE_BOOL for PHP 7.4 since even though they are the same, the latter is only available from PHP 8.0
$include_space = filter_var($_GET['space'] ?? false, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
$include_people = filter_var($_GET['people'] ?? false, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
$include_destination_zone = filter_var($_GET['destination_zone'] ?? false, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
$include_department = filter_var($_GET['department'] ?? false, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

$limit = intval($_GET['limit'] ?? 20);

$response = [];
if($include_space) $response['space'] = $eps_map->searchSpace($_GET['text_search'], $limit, 0);
if($include_people) $response['people'] = $eps_map->searchPerson($_GET['text_search'], $limit, 0);
if($include_destination_zone) $response['destination_zone'] = $eps_map->searchDestinationZone($_GET['text_search'], $limit, 0);
if($include_department) $response['department'] = $eps_map->searchDepartment($_GET['text_search'], $limit, 0);

endWithJSON($response);

?>