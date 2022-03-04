<?php

    // Mida dels identificadors
    // edge_id:             20
    // node_id:             10
    // porta_id:            8
    // za_id:               6
    // espai_id:            6
    // professor_id:        6
    // instruction_id:      5
    // department_id        3
    // tipus_node_id:       3
    // tipus_espai_id:      3
    // edifici_id:          2
    // lang_id:             2 (VARCHAR)

    $time_start = microtime(true);
    $current_lang = "es";
    require_once("AppInit.php");

    $graph = new Graph($eps_map->getAllNodes(), $eps_map->getAllEdges());
    echo "<pre>";

    $encode_json = function($object){
        var_dump(json_decode(json_encode($object), true));
    };

    $ini_node = $eps_map->getNode(36);
    $end_node = $eps_map->getNode(7);

    echo "Start: ".$ini_node->getID()."\n";
    echo "Finnish: ".$end_node->getID()."\n";

    /**
     * Extract the ordered list of Nodes from the $path
     *
     * @param Edge[] $path
     * @param Node $start
     * @param Node $finnish
     * 
     * @return Node[]
     */
    function getNodeListFromPath(array $path, Node $start, Node $finnish){
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
            $obj['text'] = $instruction_ctrl->getInstructionBetween($prev_edge, $current_edge, $lang)->getText();
            $instructions_list[] = $obj;

            $prev_edge = $current_edge;
        }

        return $instructions_list;
    }
    
    /**
     * @var Edge[] $path
     */
    list($path, $cost) = $graph->findShortestPath($ini_node, $end_node);

    var_dump(array_map(fn($n) => $n->getID(), getNodeListFromPath($path, $ini_node, $end_node)));

    $instructions_list = getInstructionsListFromPath($path, $eps_map, $eps_map->getLanguageByShortName($current_lang));
    foreach($instructions_list as $inst){
        echo "Weight: ".$inst['from']->getWeight()."<br>";
        echo $inst['text']."<br>";
    }
    echo $instructions_list[count($instructions_list)-1]['to']->getWeight()."<br>";

    // $encode_json($path);
    echo "<br><br><b>Final cost: $cost</b><br>";
    
    echo "</pre>";
    $time_end = microtime(true);

    echo "<br><br><br>Total time: ".($time_end-$time_start)." s";

?>