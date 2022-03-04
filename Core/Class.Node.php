<?php

/**
 * Class to represent a Node in the database.
 * A node has a unique ID, has a Node_Type to know what type the node is,
 * the level in the building where the node is located, and it might be inside a Destination_Zone
 */
class Node extends DB_Object {

    private const table_name = "nodes";

    /** Unique ID for this Node
     * @var int */
    protected int $_id;

    /** ID for the Node_Type this Node is
     * @var int */
    protected int $_node_type_id;

    /** In what level the node is located in the building
     * @var int */
    protected int $_level;

    /** When the Node belongs to a Destination_Zone, the ID for that Destination_Zone
     * @var int */
    protected ?int $_dest_zone_id;

    /** Attribute to act as a cache for the Destination_Zone object to not request it every time
     * @var Destination_Zone */
    protected ?Destination_Zone $_dest_zone_obj;

    /**
     * Create a new Node instance
     *
     * @param integer $id Unique ID for this Node
     * @param integer $node_type_id ID for the Node_Type this Node is
     * @param integer|null $level In what level the node is located in the building
     * @param integer|null $dest_zone_id When the Node belongs to a Destination_Zone, the ID for that Destination_Zone
     */
    public function __construct(int $id, int $node_type_id, ?int $level, ?int $dest_zone_id){
        $this->_id = $id;
        $this->_node_type_id = $node_type_id;
        $this->_level = $level;
        $this->_dest_zone_id = $dest_zone_id;
    }

    /**
     * Get the unique ID for this Node
     *
     * @return integer
     */
    public function getID(): int {
        return $this->_id;
    }

    /**
     * Get the Node_Type for this node
     *
     * @return integer
     */
    public function getNodeType(): int {
        return $this->_node_type_id;
    }

    /**
     * Set the Node_Type for this node
     *
     * @param integer $type The new type, defined in Node_Type
     * 
     * @return boolean True on sucess, false on failure
     */
    public function setNodeType(int $type): bool {
        $db = $this->getEPSMap()->getDB();
        $logger = $this->getEPSMap()->error_logger;

        $classname = self::getTableName();

        $db->startTransaction();
        $queryStr = "UPDATE `$classname` SET `nodes_type_id` = :type WHERE `id` = :id";

        $res = $db->getResultPrepared($queryStr, [":id" => $this->getID(), ":type" => $type]);
        if($res === false){
            $logger->error($db->getErrorMsg());
            return false;
        }

        $db->commitTransaction();
        $this->_node_type_id = $type;

        return true;
    }

    /**
     * Get the level this node is located in
     *
     * @return integer
     */
    public function getLevel(): int {
        return $this->_level;
    }

    /**
     * Set the level this node is located in
     *
     * @param integer $level The new level for this node
     * 
     * @return boolean True on sucess, false on failure
     */
    public function setLevel(int $level): bool {
        $db = $this->getEPSMap()->getDB();
        $logger = $this->getEPSMap()->error_logger;

        $classname = self::getTableName();

        $db->startTransaction();
        $queryStr = "UPDATE `$classname` SET `level` = :level WHERE `id` = :id";

        $res = $db->getResultPrepared($queryStr, [":id" => $this->getID(), ":level" => $level]);
        if($res === false){
            $logger->error($db->getErrorMsg());
            return false;
        }

        $db->commitTransaction();
        $this->_level = $level;

        return true;
    }

    /**
     * Get the Destination_Zone where this Node is inside
     *
     * @param boolean $id Whether to return only the id of the Destination_Zone or the full object
     * 
     * @return Destination_Zone|int|null|false The Destination_Zone instance or the Destination_Zone id depending on $id, null if this Node is not inside any Destination_Zone or false on error
     */
    public function getDestinationZone(bool $id = false){
        if($this->_dest_zone_id === null) return null;

        if($id === true) return $this->_dest_zone_id;
        else{
            if($this->_dest_zone_obj === null) $this->_dest_zone_obj = $this->getEPSMap()->getDestinationZone($this->_dest_zone_id);
            return $this->_dest_zone_obj;
        }
    }

    /**
     * Set the Destination_Zone this Node is located in
     *
     * @param Destination_Zone $zone The new Destination_Zone the Node is inside
     * 
     * @return boolean True on success, false otherwise
     */
    public function setDestination_Zone(Destination_Zone $zone){
        $db = $this->getEPSMap()->getDB();
        $logger = $this->getEPSMap()->error_logger;

        $classname = self::getTableName();

        $db->startTransaction();
        $queryStr = "UPDATE `$classname` SET `dest_zone_id` = :zone_id WHERE `id` = :id";

        $res = $db->getResultPrepared($queryStr, [":id" => $this->getID(), ":zone_id" => $zone->getID()]);
        if($res === false){
            $logger->error($db->getErrorMsg());
            return false;
        }

        $db->commitTransaction();
        $this->_dest_zone_id = $zone->getID();
        $this->_dest_zone_obj = $zone;

        return true;
    }

    public function jsonSerialize(): array {
        return [
            "id" => $this->getID(),
            "type_id" => $this->getNodeType(),
            "level" => $this->getLevel(),
            "destination_zone" => ($this->getDestinationZone() != null ? $this->getDestinationZone()->jsonSerialize() : null)
        ];
    }

    public static function getTableName(): string {
        return self::table_name;
    }

    /**
     * Get a Node instance by the Database id
     *
     * @param integer $id The unique ID for the Node
     * @param EPS_Map $eps_map Current EPS_Map instance
     * 
     * @return Node|null|false The Node instance, null if not found or false on error
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
     * Get a Node instance by a full database row
     *
     * @param array $resArr Database returned row
     * @param EPS_Map $eps_map Current EPS_Map instance
     * 
     * @return Node
     */
    public static function getInstanceByData(array $resArr, EPS_Map $eps_map): Node {
        $instance = new self($resArr['id'], $resArr['nodes_type_id'], $resArr['level'], $resArr['dest_zone_id']);
        $instance->setEPSMap($eps_map);
        
        return $instance;
    }
}

/**
 * Class to represent the list of Node_Types
 */
class Node_Type {

    /** The node is a stair */
    public const type_stair =       1;
    /** The node is a space, in the space there is a more specific type */
    public const type_space =       2;
    /** The node is an internal node for the graph */
    public const type_graph_node =  3;
    /** The node does not have a specific type, you should not use this type unless
     *  completely necessary, is preferred to add a new node type than using this type. 
     *  @deprecated */
    public const type_other =       100;

    private const _available_types = [self::type_stair, self::type_space, self::type_graph_node, self::type_other];

    /**
     * Check if a given $type is valid
     *
     * @param integer $type
     * 
     * @return boolean
     */
    public static function isTypeValid(int $type): bool {
        return in_array($type, self::_available_types);
    }
}

?>