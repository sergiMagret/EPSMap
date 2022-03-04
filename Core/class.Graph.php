<?php

/**
 * Class to store a Graph and perform operations over it.
 */
class Graph {
    
    /** List of Nodes indexed by their ID
     * @var Node[] */
    protected array $_nodes;
    
    /** List of Edges indexed by their ID
     * @var Edge[] */
    protected array $_edges;
    
    /** List of Edges indexed by [node_id_start][node_id_end]
     * @var Edge[] */
    public array $_graph_as_array;

    /**
     * Create a Graph to operate over, and find paths on it
     *
     * @param Node[] $nodes List of Nodes for the Graph
     * @param Edge[] $edges List of Edges for the Graph
     */
    public function __construct(array $nodes, array $edges) {
        $this->_nodes = [];
        foreach($nodes as $n) $this->_nodes[$n->getID()] = $n;
        
        $this->_edges = [];
        foreach($edges as $e) $this->_edges[$e->getID()] = $e;

        $this->_graph_as_array = [];
        foreach($this->_edges as $e) $this->_graph_as_array[$e->getEdgeStart(true)][$e->getEdgeEnd(true)] = $e;
    }

    /**
     * Get all the edges from the graph
     *
     * @return Edge[]
     */
    public function getEdges(): array {
        return $this->_edges;
    }
    
    /**
     * Get all the nodes from the graph
     *
     * @return Node[]
     */
    public function getNodes(): array {
        return $this->_nodes;
    }

    /**
     * Get the adjecent nodes for a given node
     *
     * @param Node $node
     * @return Node[]
     */
    public function getAdjecentNodes(Node $node): array {
        // TODO Hi haurà un moment en que serà més eficient fer una query SQL amb el WHERE que toca que no fer el recorregut per tots els nodes
        $nodes = [];
        
        foreach($this->_nodes as $n){
            if(isset($this->_graph_as_array[$n->getID()][$node->getID()])){ // From other nodes to $node
                $nodes[] = $this->_graph_as_array[$n->getID()][$node->getID()]->getEdgeStart();
            }else if(isset($this->_graph_as_array[$node->getID()][$n->getID()])){ // From $node to other nodes
                $nodes[] = $this->_graph_as_array[$node->getID()][$n->getID()]->getEdgeEnd();
            }
        }
        

        return $nodes;
    }

    /**
     * Get the Edge between $start and $finish
     *
     * @param Node $start
     * @param Node $finish
     * @return Edge|null The Edge or null if there is no Edge between $start and $finish
     */
    public function getEdgeBetween(Node $start, Node $finish): ?Edge {
        return $this->_graph_as_array[$start->getID()][$finish->getID()] ?? $this->_graph_as_array[$finish->getID()][$start->getID()] ?? null;
    }

    /**
     * Find the shortest path between $from and $to Nodes using Dijkstra.
     * 
     * The returned result is a list of the Edges the user has to go by.  
     * The Edges are considered bidirectional.  
     * 
     * You should call this method with list($edges_path, $cost) = Graph->findShortestPath(Node, Node)
     *
     * @param Node $from
     * @param Node $to
     * @return array|null The returned array contains the list of Edges the user has to go by in the first position and the total cost in the second position
     */
    public function findShortestPath(Node $from, Node $to): ?array {
        $dist = []; // Stores the distances between the node $from and the node <i>
        $prev = []; // $prev[i] sotres the previous node to <i>
        $def = [];  // Stores whether the node <i> has the definitive distance in $dist[i]
        $nodes = $this->getNodes();

        // Initialize all the arrays
        foreach($nodes as $n){
            $dist[$n->getID()] = INF;
            $prev[$n->getID()] = null;
            $def[$n->getID()] = false;
        }
        
        // Initialize the distance for the initial node
        $dist[$from->getID()] = 0;

        // Current node
        $vert_act = $from;

        while($def[$to->getID()] == false && $vert_act != null){
            $min_dist = INF; $vert_act = null;
            foreach($nodes as $n){ // Buscar el vertex amb distància minima
                // echo '1 $def['.$n->getID().']: '.intval($def[$n->getID()])." == false? <br>";
                // echo '2 $dist['.$n->getID().']: '.$dist[$n->getID()]." < $min_dist?<br>";
                // echo '3 $min_dist: '.$min_dist."<br>";
                if($def[$n->getID()] == false && $dist[$n->getID()] < $min_dist){
                    $min_dist = $dist[$n->getID()];
                    $vert_act = $n;
                    // echo $n->getID()." compleix"."<br>";
                    // echo "nova \$min_dist: $min_dist"."<br>";
                }
                // echo "<br>";
            }

            // echo "Finalment \$min_dist: $min_dist i \$vert_act: {$vert_act->getID()}<br><br>";

            if($vert_act != null){
                $def[$vert_act->getID()] = true;
                // echo "Marcant {$vert_act->getID()} com a definitiu<br>";
                // echo "Nodes adjecents a {$vert_act->getID()}: ".implode(", ", array_map(fn($e) => $e->getID(), $this->getAdjecentNodes($vert_act)))."<br>";
                foreach($this->getAdjecentNodes($vert_act) as $adj){ // Agafar els nodes adjacents
                    // echo '4 $def['.$adj->getID().']: '.intval($def[$adj->getID()])." == false? <br>";
                    $tmp_weight = $dist[$vert_act->getID()] + $this->getEdgeBetween($vert_act, $adj)->getWeight();
                    // echo '5 $dist['.$vert_act->getID().']: '.$dist[$vert_act->getID()]."<br>";
                    // echo '6 $this->getEdgeBetween('.$vert_act->getID().', '.$adj->getID().')->getWeight(): '.$this->getEdgeBetween($vert_act, $adj)->getWeight()."<br>";
                    // echo '7 $dist['.$adj->getID().']: '.$dist[$adj->getID()]." > $tmp_weight? <br>";
                    if($def[$adj->getID()] == false && $dist[$adj->getID()] > $dist[$vert_act->getID()] + $this->getEdgeBetween($vert_act, $adj)->getWeight()){
                        // echo "Node ".$adj->getID()." compleix <br>";
                        // echo 'posant $prev['.$adj->getID().'] = '.$vert_act->getID().'<br>';
                        $dist[$adj->getID()] = $dist[$vert_act->getID()] + $this->getEdgeBetween($vert_act, $adj)->getWeight();
                        $prev[$adj->getID()] = $vert_act;
                    }
                    // echo "<br>";
                }
            }

            // echo "<br><br>=============<br><br><br>";
        }

        if($def[$to->getID()] == null) return null;

        $path = [$to];

        $vert_act = $to;
        while($vert_act->getID() != $from->getID()){
            $vert_act = $prev[$vert_act->getID()];
            $path[] = $vert_act;
        }

        // Es pot retornar aqui directament els nodes o passar per les arestes
        // return [array_reverse($path), $dist[$to->getID()]];

        $edges_path = [];
        $prev_n = $path[0];
        $act_n = null;
        for($i=1; $i < count($path); $i++){
            $act_n = $path[$i];
            $edges_path[] = $this->getEdgeBetween($prev_n, $act_n);
            $prev_n = $act_n;
        }


        return [array_reverse($edges_path), $dist[$to->getID()]];
    }
}

?>