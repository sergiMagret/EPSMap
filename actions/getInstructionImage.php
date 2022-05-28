<?php

require_once("../AppInit.php");
require_once("action_functions.php");

if(!isset($_GET['initial_edge_id'])) endWithErrorCode(400, "initial_edge_id not given");
if(!isset($_GET['destination_edge_id'])) endWithErrorCode(400, "destination_edge_id not given");

$initial_edge_id = intval($_GET['initial_edge_id']);
$destination_edge_id = intval($_GET['destination_edge_id']);

$initial_edge = $eps_map->getEdge($initial_edge_id);
if($initial_edge == false) endWithErrorCode(500, "Error getting edge $initial_edge_id");
if($initial_edge == null) endWithErrorCode(404, "Node $initial_edge_id not found");

$destination_edge = $eps_map->getEdge($destination_edge_id);
if($destination_edge == false) endWithErrorCode(500, "Error getting edge $destination_edge_id");
if($destination_edge == null) endWithErrorCode(404, "Node $destination_edge_id not found");

$instruction_ctrl = new Edge_Instructions_Controller($eps_map);
$image_path = $instruction_ctrl->getInstructionImageBetween($initial_edge, $destination_edge);
if($image_path === false) endWithErrorCode(500, "Error getting image");
if($image_path === null) endWithErrorCode(404, "Image not found");

$mime = mime_content_type($image_path);
if($mime === false) endWithErrorCode(500, "Error getting mime type");
header("Content-Type: $mime");
echo file_get_contents($image_path);

?>