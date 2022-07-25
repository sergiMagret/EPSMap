<?php

require_once("../AppInit.php");
require_once("action_functions.php");

if(!isset($_GET['capture_point_id']) && !isset($_GET['initial_node_id'])) endWithErrorCode(400, "You either need to give capture_point_id or initial_node_id");
if(!isset($_GET['destination_node_id'])) endWithErrorCode(400, "destination_node_id not given");

$initial_node = null;
$capture_point = null;
if(isset($_GET['capture_point_id'])){
    $capture_point_id = intval($_GET['capture_point_id']);
    $capture_point = $eps_map->getCapturePoint($capture_point_id);
    if($capture_point == false) endWithErrorCode(500, "Error getting node $capture_point_id");
    if($capture_point == null) endWithErrorCode(404, "Node $capture_point_id not found");

    $initial_node = $capture_point->getNode(false);
    if($initial_node == false) endWithErrorCode(500, "Error getting node $initial_node_id");
    if($initial_node == null) endWithErrorCode(404, "Node $initial_node_id not found");
}else if(isset($_GET['initial_node_id'])){
    $initial_node_id = intval($_GET['initial_node_id']);
    $initial_node = $eps_map->getNode($initial_node_id);
    if($initial_node == false) endWithErrorCode(500, "Error getting node $initial_node_id");
    if($initial_node == null) endWithErrorCode(404, "Node $initial_node_id not found");
}

$destination_node_id = intval($_GET['destination_node_id']);
$lang = isset($_GET['lang']) ? strtolower(strval($_GET['lang'])) : "es";

if($lang != "es" && $lang != "ca" && $lang != "en") $lang = "es";

$destination_node = $eps_map->getNode($destination_node_id);
if($destination_node == false) endWithErrorCode(500, "Error getting node $destination_node_id");
if($destination_node == null) endWithErrorCode(404, "Node $destination_node_id not found");

$language = $eps_map->getLanguageByShortName($lang);
if($language == false) endWithErrorCode(500, "Error getting language $lang");
if($language == null) endWithErrorCode(404, "Language $lang not found");

$graph = new Graph($eps_map->getAllNodes(), $eps_map->getAllEdges());

list($path, $cost) = $graph->findShortestPath($initial_node, $destination_node);

/**
 * Extract the ordered list of Nodes from the $path
 *
 * @param Edge[] $path
 * @param Node $start The start node for the first Edge
 * 
 * @return Node[]
 */
function getNodeListFromPath(array $path, Node $start){
    $prev_node = $start;
    $nodes_list = [$prev_node];
    foreach($path as $edge){
        $current_node = $edge->getOppositeNode($prev_node);
        $nodes_list[] = $current_node;
        $prev_node = $current_node;
    }

    return $nodes_list;
}

/**
 * Get the Instructions to navigate the Edges of the $path in $lang language
 *
 * @param Edge[] $path The path to translate
 * @param EPS_Map $eps_map Current instance of the EPS_Map
 * @param Language $lang Language to translate
 * 
 * @return array[]
 */
function getInstructionsListFromPath(array $path, EPS_Map $eps_map, Language $lang){
    $instruction_ctrl = new Edge_Instructions_Controller($eps_map);
    $instructions_list = [];

    $prev_edge = $path[0];
    
    for($i=1; $i < count($path); $i++){
        $current_edge = $path[$i];

        $obj = null;
        $obj['from'] = $prev_edge;
        $prev_edge->getEdgeStart();
        $obj['to'] = $current_edge;

        $instr = $instruction_ctrl->getInstructionBetween($prev_edge, $current_edge, $lang);
        $obj['instruction_translation'] = $instr;
        
        $obj['has_image'] = $instruction_ctrl->getInstructionImageBetween($prev_edge, $current_edge) != null;
        $instructions_list[] = $obj;

        $prev_edge = $current_edge;
    }

    return $instructions_list;
}

if(empty($path)){ // The user has requested for the same destination as the starting position
    endWithJSON([
        "in_place" => true,
        "only_edge" => null,
        "instructions" => null,
        "destination_zone" => $destination_node->getDestinationZone(),
        "initial_turn" => null,
        "total_cost" => $cost
    ]);
}

$instructions = getInstructionsListFromPath($path, $eps_map, $language);

if(empty($instructions)){ // In this case the user is only one edge away from the final destination, so there is no instruction
    $direction_initial_edge = null;
    if($capture_point->getNode(true) == $path[0]->getEdgeStart(true)) $direction_initial_edge = $path[0]->get2dDirection();
    else if($capture_point->getNode(true) == $path[0]->getEdgeEnd(true)) $direction_initial_edge = Directions::getOppositeDirection($path[0]->get2dDirection());

    endWithJSON([
        "in_place" => false,
        "only_edge" => $path[0],
        "instructions" => null,
        "destination_zone" => $destination_node->getDestinationZone(),
        "initial_turn" => $capture_point != null ? Directions::turnDirection2D($capture_point->getFaceDirection(), $direction_initial_edge) : null,
        "total_cost" => $cost
    ]);
}else{
    $direction_initial_edge = null;
    if($capture_point->getNode(true) == $instructions[0]['from']->getEdgeStart(true)) $direction_initial_edge = $instructions[0]['from']->get2dDirection();
    else if($capture_point->getNode(true) == $instructions[0]['from']->getEdgeEnd(true)) $direction_initial_edge = Directions::getOppositeDirection($instructions[0]['from']->get2dDirection());
    
    endWithJSON([
        "in_place" => false,
        "only_edge" => null,
        "instructions" => $instructions,
        "destination_zone" => $destination_node->getDestinationZone(),
        "initial_turn" => $capture_point != null ? Directions::turnDirection2D($capture_point->getFaceDirection(), $direction_initial_edge) : null,
        "total_cost" => $cost
    ]);
}


?>