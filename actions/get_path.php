<?php

require_once("../AppInit.php");
require_once("action_functions.php");

if(!isset($_GET['initial_node_id'])) endWithErrorCode(400, "initial_node_id not given");
if(!isset($_GET['destination_node_id'])) endWithErrorCode(400, "destination_node_id not given");

$initial_node_id = intval($_GET['initial_node_id']);
$destination_node_id = intval($_GET['destination_node_id']);
$lang = isset($_GET['lang']) ? strtolower(strval($_GET['lang'])) : "es";

if($lang != "es" && $lang != "ca" && $lang != "en") $lang = "es";

$initial_node = $eps_map->getNode($initial_node_id);
if($initial_node == false) endWithErrorCode(500, "Error getting node $initial_node_id");
if($initial_node == null) endWithErrorCode(404, "Node $initial_node_id not found");

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
        $obj['to'] = $current_edge;
        $instr = $instruction_ctrl->getInstructionBetween($prev_edge, $current_edge, $lang);
        // $obj['instruction']['instruction_translation'] = 
        //     $instr == null ? 
        //     "Missing instruction from ".$prev_edge->getID()." (".$prev_edge->getEdgeStart(true).", ".$prev_edge->getEdgeEnd(true).") to ".$current_edge->getID()." (".$current_edge->getEdgeStart(true).", ".$current_edge->getEdgeEnd(true).")" : 
        //     $instr->getText();
        $obj['instruction_translation'] = $instr;
        $instructions_list[] = $obj;

        $prev_edge = $current_edge;
    }

    return $instructions_list;
}

$instructions = getInstructionsListFromPath($path, $eps_map, $language);

endWithJSON([
    "instructions" => $instructions,
    "total_cost" => $cost
]);


?>