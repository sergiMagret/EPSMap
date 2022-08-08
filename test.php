<?php

    $time_start = microtime(true);
    $current_lang = "es";
    require_once("AppInit.php");

    $graph = new Graph($eps_map->getAllNodes(), $eps_map->getAllEdges());
    echo "<pre>";

    $ini_node = $eps_map->getNode(20);
    $end_space = $eps_map->getSpace(34);
    $end_node = $end_space->getDestinationZone()->getMainNode();
    // $end_node = $eps_map->getNode(7);

    echo "Start: ".$ini_node->getID()."\n";
    echo "Finnish: ".$end_space->getName()." (".$end_space->getNode(true).") belonging to destination zone ".$end_space->getDestinationZone()->getName()." (".$end_space->getDestinationZone(true).")";
    echo " assigned to node ".$end_node->getID()."\n";

    /**
     * Extract the ordered list of Nodes from the $path
     *
     * @param Edge[] $path
     * @param Node $start
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
     * Get the instructions to navigate the edges of the $path
     *
     * @param Edge[] $path
     * @param EPS_Map $eps_map
     * @param Language $lang
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
            $obj['text'] = $instr == null ? "<em>Missing instruction from ".$prev_edge->getID()." (".$prev_edge->getEdgeStart(true).", ".$prev_edge->getEdgeEnd(true).") to ".$current_edge->getID()." (".$current_edge->getEdgeStart(true).", ".$current_edge->getEdgeEnd(true).")"."</em>" : $instr->getText();
            $instructions_list[] = $obj;

            $prev_edge = $current_edge;
        }

        return $instructions_list;
    }
    
    /**
     * @var Edge[] $path
     */
    list($path, $cost) = $graph->findShortestPath($ini_node, $end_node);

    echo "\nList of nodes to go by:\n";
    var_dump(array_map(fn($n) => $n->getID(), getNodeListFromPath($path, $ini_node)));
    echo "\n";

    echo "Instructions to follow:\n";
    $instructions_list = getInstructionsListFromPath($path, $eps_map, $eps_map->getLanguageByShortName($current_lang));
    foreach($instructions_list as $inst){
        echo "From ".$inst['from']->getID()." (".$inst['from']->getEdgeStart(true).", ".$inst['from']->getEdgeEnd(true).") to ".$inst['to']->getID()." (".$inst['to']->getEdgeStart(true).", ".$inst['to']->getEdgeEnd(true)."): ";
        echo $inst['from']->getWeight()." meters -> ";
        echo $inst['text']."<br>";
    }
    echo $instructions_list[count($instructions_list)-1]['to']->getWeight()." meters <br>";

    echo "<br><br><b>Final cost: $cost meters</b><br>";
    
    echo "</pre>";
    $time_end = microtime(true);

    echo "<br><br><br>Total time: ".($time_end-$time_start)." s";

?>