<?php

// TODO En aquest cas pot ser no s'ha de fer getters i setters, sino que amb els Nodes s'han de fer i modificar els
// segments.
// TODO O això o fer una nova classe Graph que gestioni tot el necessari per als Nodes i Edges

/**
 * Class to represent the list of Edges in the database.
 * The Edges connect Nodes in the graph, each Edge has a 2d direction and a 3d direction of Directions to know
 * in what direction the Edge is headed (North, South, East or West and Up, Down or Plain)
 */
class Edge extends DB_Object {

    private const table_name = "edges";

    /** Unique ID for the edge
     * @var int */
    protected int $_id;

    /** ID for the Node on the start of the Edge
     * @var int */
    protected int $_from_node_id;

    /** Attribute to act as a cache for the Node object to not request it every time
     * @var Node */
    protected ?Node $_from_node_obj;

    /** ID for the Node on the end of the Edge
     * @var int */
    protected int $_to_node_id;
    
    /** Attribute to act as a cache for the Node object to not request it every time
     * @var Node */
    protected ?Node $_to_node_obj;
    
    /** Weight or cost for this edge
     * @var int */
    protected int $_weight;

    /** Natural direction where the Edge is heading over a plain map (one of the Directions)
     * @var int */
    protected string $_direction_2d;
    
    /** Natural direction where the Edge is heading on the Z axis (one of the Directions)
     * @var int */
    protected string $_direction_3d;

    /**
     * Get an Edge instance, all Edges are considered bidirectionals, the directions you pass are the ones for the from-to,
     * for the reverse edge, the reverse direction will be taken.
     *
     * @param integer $id Unique ID for the Edge
     * @param integer $from_node_id ID for the node where the edge starts
     * @param integer $to_node_id ID for the node where the edge ends
     * @param integer $weight The weight/cost for this edge
     * @param string $direction_2d Direction over a map where the edge is directed
     * @param string $direction_3d On a map whether the edge would be going up or down
     */
    public function __construct(int $id, int $from_node_id, int $to_node_id, int $weight, string $direction_2d, string $direction_3d){
        $this->_id = $id;
        $this->_from_node_id = $from_node_id;
        $this->_from_node_obj = null;
        $this->_to_node_id = $to_node_id;
        $this->_to_node_obj = null;
        $this->_weight = $weight;
        $this->_direction_2d = $direction_2d;
        $this->_direction_3d = $direction_3d;
    }

    /**
     * Get the unique ID
     *
     * @return integer
     */
    public function getID(): int {
        return $this->_id;
    }

    /**
     * Get the Node where the Edge starts
     *
     * @param boolean $id Whether to return only the id of the Node or the full object
     * 
     * @return Node|int|null|false The Node instance or the Node id depending on $id or false on error
     */
    public function getEdgeStart(bool $id = false){
        if($id === true) return $this->_from_node_id;
        else{
            if($this->_from_node_obj === null) $this->_from_node_obj = $this->getEPSMap()->getNode($this->_from_node_id);
            return $this->_from_node_obj;
        }
    }
    
    /**
     * Set the Node where the Edge starts
     *
     * @param Node $node The new node where the edge starts
     * 
     * @return boolean True on success, false on error
     */
    public function setEdgeStart(Node $node): bool {
        $db = $this->getEPSMap()->getDB();
        $logger = $this->getEPSMap()->error_logger;

        $classname = self::getTableName();

        $db->startTransaction();
        $queryStr = "UPDATE `$classname` SET `from_node_id` = :from_node_id WHERE `id` = :id";

        $res = $db->getResultPrepared($queryStr, [":id" => $this->getID(), ":from_node_id" => $node->getID()]);
        if($res === false){
            $logger->error($db->getErrorMsg());
            return false;
        }

        $db->commitTransaction();
        $this->_from_node_id = $node->getID();
        $this->_from_node_obj = $node;

        return true;
    }
    
    /**
     * Get the Node where the Edge ends
     *
     * @param boolean $id Whether to return only the id of the Node or the full object
     * 
     * @return Node|int|null|false The Node instance or the Node id depending on $id or false on error
     */
    public function getEdgeEnd(bool $id = false){
        if($id === true) return $this->_to_node_id;
        else{
            if($this->_to_node_obj === null) $this->_to_node_obj = $this->getEPSMap()->getNode($this->_to_node_id);
            return $this->_to_node_obj;
        }
    }

    /**
     * Set the Node where the Edge ends
     *
     * @param Node $node The new node where the edge starts
     * 
     * @return boolean True on success, false on error
     */
    public function setEdgeEnd(Node $node){
        $db = $this->getEPSMap()->getDB();
        $logger = $this->getEPSMap()->error_logger;

        $classname = self::getTableName();

        $db->startTransaction();
        $queryStr = "UPDATE `$classname` SET `to_node_id` = :to_node_id WHERE `id` = :id";

        $res = $db->getResultPrepared($queryStr, [":id" => $this->getID(), ":to_node_id" => $node->getID()]);
        if($res === false){
            $logger->error($db->getErrorMsg());
            return false;
        }

        $db->commitTransaction();
        $this->_to_node_id = $node->getID();
        $this->_to_node_obj = $node;

        return true;
    }

    /**
     * Get the weight/cost for this edge
     *
     * @return integer
     */
    public function getWeight(): int {
        return $this->_weight;
    }

    /**
     * Set the weight/cost for this edge
     *
     * @param integer $weight The new weight
     * 
     * @return boolean True on success, false on failure
     */
    public function setWeight(int $weight): bool {
        $db = $this->getEPSMap()->getDB();
        $logger = $this->getEPSMap()->error_logger;

        $classname = self::getTableName();

        $db->startTransaction();
        $queryStr = "UPDATE `$classname` SET `weight` = :weight WHERE `id` = :id";

        $res = $db->getResultPrepared($queryStr, [":id" => $this->getID(), ":weight" => $weight]);
        if($res === false){
            $logger->error($db->getErrorMsg());
            return false;
        }

        $db->commitTransaction();
        $this->_weight = $weight;

        return true;
    }
    
    /**
     * Get the direction over a map where the Edge is directed
     * 
     * @return string One of the possible directions defined in Directions
     */
    public function get2dDirection(): string {
        return $this->_direction_2d;
    }

    /**
     * Set the direction over a map where the Edge is directed
     * 
     * @param string $direction The new direction
     * 
     * @return boolean True on success, false on failure
     */
    public function set2dDirection(string $direction): bool {
        $db = $this->getEPSMap()->getDB();
        $logger = $this->getEPSMap()->error_logger;

        $classname = self::getTableName();

        $db->startTransaction();
        $queryStr = "UPDATE `$classname` SET `direction_2d` = :direction_2d WHERE `id` = :id";

        $res = $db->getResultPrepared($queryStr, [":id" => $this->getID(), ":direction_2d" => $direction]);
        if($res === false){
            $logger->error($db->getErrorMsg());
            return false;
        }

        $db->commitTransaction();
        $this->_direction_2d = $direction;

        return true;
    }
    
    /**
     * Get the direction over a map whether the edge would be going up or down
     * 
     * @return string One of the possible directions defined in Directions
     */
    public function get3dDirection(): string {
        return $this->_direction_3d;
    }

    /**
     * Set the direction over a map whether the edge would be going up or down
     * 
     * @param string $direction The new direction
     * 
     * @return boolean True on success, false on failure
     */
    public function set3dDirection(string $direction): bool {
        $db = $this->getEPSMap()->getDB();
        $logger = $this->getEPSMap()->error_logger;

        $classname = self::getTableName();

        $db->startTransaction();
        $queryStr = "UPDATE `$classname` SET `direction_3d` = :direction_3d WHERE `id` = :id";

        $res = $db->getResultPrepared($queryStr, [":id" => $this->getID(), ":direction_3d" => $direction]);
        if($res === false){
            $logger->error($db->getErrorMsg());
            return false;
        }

        $db->commitTransaction();
        $this->_direction_3d = $direction;

        return true;
    }
    
    /**
     * Get the opposite node for one of the two nodes.  
     * E.g. If the Edge goes from A to B and you request getOppositeNode(A) => B and getOppositeNode(B) => A
     *
     * @param Node $start_node
     * 
     * @return Node
     */
    public function getOppositeNode(Node $start_node): Node{
        if($this->getEdgeStart(true) == $start_node->getID()) return $this->getEdgeEnd(false);
        else return $this->getEdgeStart(false);
    }

    public function jsonSerialize(): array {
        return [
            "id" => $this->getID(),
            "from_node" => $this->getEdgeStart()->jsonSerialize(),
            "to_node" => $this->getEdgeEnd()->jsonSerialize(),
            "weight" => $this->getWeight(),
            "direction_2d" => $this->get2dDirection(),
            "direction_3d" => $this->get3dDirection()
        ];
    }

    public static function getTableName(): string {
        return self::table_name;
    }

    /**
     * Get an Edge instance by the Database id
     *
     * @param integer $id The unique ID for the Edge
     * @param EPS_Map $eps_map Current EPS_Map instance
     * 
     * @return Edge|null|false The Edge instance, null if not found or false on error
     */
    public static function getInstance(int $id, EPS_Map $eps_map){
        $db = $eps_map->getDB();
        $logger = $eps_map->error_logger;

        $tablename = self::table_name;

        $queryStr = "SELECT * FROM `$tablename` WHERE id = :id"; // Each object will have a different table_name
        $resArr = $db->getResultArrayPrepared($queryStr, [":id" => $id]);
        if($resArr === false){
            $logger->error($db->getErrorMsg());
            return false;
        }

        if(count($resArr) == 0) return null;

        return self::getInstanceByData($resArr[0], $eps_map);
    }

    /**
     * Get an Edge instance by a full database row
     *
     * @param array $resArr Database returned row
     * @param EPS_Map $eps_map Current EPS_Map instance
     * 
     * @return Edge
     */
    public static function getInstanceByData(array $resArr, EPS_Map $eps_map): Edge {
        $instance = new self($resArr['id'], $resArr['from_node_id'], $resArr['to_node_id'], $resArr['weight'], $resArr['direction_2d'], $resArr['direction_3d']);
        $instance->setEPSMap($eps_map);
        
        return $instance;
    }
}

/**
 * Auxiliar class to represent and perform operations over the natural directions an Edge can go
 */
class Directions {

    ///         N
    ///         ^
    ///     W <   > E
    ///         v
    ///         S
    
    // Possible 2D directions (plain directions over a map X and Y)
    public const direction_north =  "N";
    public const direction_south =  "S";
    public const direction_east =   "E";
    public const direction_west =   "W";
    
    // Possible 3D directions (moving up on down Z)
    public const direction_up =     "U";
    public const direction_down =   "D";
    public const direction_plain =  "P";

    // Possible turns
    public const turn_right =       "R";
    public const turn_left =        "L";
    public const turn_forward =     "F";
    public const turn_backward =    "B";

    public const turn_up =          "U";
    public const turn_down =        "D";

    public static function turnDirection2D($from_edge, $to_edge): string {
        if($from_edge == self::direction_north){ // North
            if($to_edge == self::direction_north) return self::turn_forward;
            else if($to_edge == self::direction_south) return self::turn_backward;
            else if($to_edge == self::direction_east) return self::turn_right;
            else if($to_edge == self::direction_west) return self::turn_left;
        }else if($from_edge == self::direction_south){ // South
            if($to_edge == self::direction_north) return self::turn_backward;
            else if($to_edge == self::direction_south) return self::turn_forward;
            else if($to_edge == self::direction_east) return self::turn_left;
            else if($to_edge == self::direction_west) return self::turn_right;
        }else if($from_edge == self::direction_east){ // East
            if($to_edge == self::direction_north) return self::turn_left;
            else if($to_edge == self::direction_south) return self::turn_right;
            else if($to_edge == self::direction_east) return self::turn_forward;
            else if($to_edge == self::direction_west) return self::turn_backward;
        }else if($from_edge == self::direction_west){ // West
            if($to_edge == self::direction_north) return self::turn_right;
            else if($to_edge == self::direction_south) return self::turn_left;
            else if($to_edge == self::direction_east) return self::turn_backward;
            else if($to_edge == self::direction_west) return self::turn_forward;
        }
    }

    public static function turnDirection3D($from_edge, $to_edge): string {
        if($from_edge == self::direction_up){
            if($to_edge == self::direction_up) return self::turn_forward;
            else if($to_edge == self::direction_down) return self::turn_backward;
            // else if($to_edge == self::direction_plain) return self::
        }
        // TODO Passar arestes o passar nodes? Si estas pujant unes escales com posar que ha de deixar de pujar?
    }
}

?>