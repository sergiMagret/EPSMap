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

    /** Latitude with 2 numbers and 5 decimals where this node is located on a map
     * @var float */
    protected float $_latitude;
    
    /** Longitude with 2 numbers and 5 decimals where this node is located on a map
     * @var float */
    protected float $_longitude;

    /**
     * Create a new Node instance
     *
     * @param integer $id Unique ID for this Node
     * @param integer $node_type_id ID for the Node_Type this Node is
     * @param integer|null $level In what level the node is located in the building
     * @param integer|null $dest_zone_id When the Node belongs to a Destination_Zone, the ID for that Destination_Zone
     * @param float $latitude Latitude with 2 numbers and 5 decimals where this node is located on a map
     * @param float $longitude Longitude with 2 numbers and 5 decimals where this node is located on a map
     */
    public function __construct(int $id, int $node_type_id, ?int $level, ?int $dest_zone_id, float $latitude, float $longitude){
        $this->_id = $id;
        $this->_node_type_id = $node_type_id;
        $this->_level = $level;
        $this->_dest_zone_id = $dest_zone_id;
        $this->_dest_zone_obj = null;
        $this->_latitude = $latitude;
        $this->_longitude = $longitude;
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

        $tablename = self::getTableName();

        $db->startTransaction();
        $queryStr = "UPDATE `$tablename` SET `nodes_type_id` = :type WHERE `id` = :id";

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

        $tablename = self::getTableName();

        $db->startTransaction();
        $queryStr = "UPDATE `$tablename` SET `level` = :level WHERE `id` = :id";

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

        $tablename = self::getTableName();

        $db->startTransaction();
        $queryStr = "UPDATE `$tablename` SET `dest_zone_id` = :zone_id WHERE `id` = :id";

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

    /**
     * Get the latitude where this node is located on a map
     *
     * @return float
     */
    public function getLatitude(): float {
        return $this->_latitude;
    }

    /**
     * Set the latitude where this node is located on a map
     *
     * @param float $latitude The new latitude for this node with up to 5 decimals
     * 
     * @return boolean True on sucess, false on failure
     */
    public function setLatitude(float $latitude): bool {
        $db = $this->getEPSMap()->getDB();
        $logger = $this->getEPSMap()->error_logger;

        $tablename = self::getTableName();

        $db->startTransaction();
        $queryStr = "UPDATE `$tablename` SET `latitude` = :latitude WHERE `id` = :id";

        $res = $db->getResultPrepared($queryStr, [":id" => $this->getID(), ":latitude" => $latitude]);
        if($res === false){
            $logger->error($db->getErrorMsg());
            return false;
        }

        $db->commitTransaction();
        $this->_level = $latitude;

        return true;
    }
    
    /**
     * Get the longitude where this node is located on a map
     *
     * @return float
     */
    public function getLongitude(): float {
        return $this->_longitude;
    }

    /**
     * Set the longitude where this node is located on a map
     *
     * @param float $longitude The new longitude for this node with up to 5 decimals
     * 
     * @return boolean True on sucess, false on failure
     */
    public function setLongitude(float $longitude): bool {
        $db = $this->getEPSMap()->getDB();
        $logger = $this->getEPSMap()->error_logger;

        $tablename = self::getTableName();

        $db->startTransaction();
        $queryStr = "UPDATE `$tablename` SET `longitude` = :longitude WHERE `id` = :id";

        $res = $db->getResultPrepared($queryStr, [":id" => $this->getID(), ":longitude" => $longitude]);
        if($res === false){
            $logger->error($db->getErrorMsg());
            return false;
        }

        $db->commitTransaction();
        $this->_level = $longitude;

        return true;
    }

    protected function jsonSerializeIDs(): array {
        return [
            "id" => $this->getID(),
            "type_id" => $this->getNodeType(),
            "level" => $this->getLevel(),
            "latitude" => $this->getLatitude(),
            "longitude" => $this->getLongitude(),
            "destination_zone" => ($this->getDestinationZone(true) != null ? $this->getDestinationZone(true) : null)
        ];
    }

    public function jsonSerialize(): array {
        return [
            "id" => $this->getID(),
            "type_id" => $this->getNodeType(),
            "level" => $this->getLevel(),
            "latitude" => $this->getLatitude(),
            "longitude" => $this->getLongitude(),
            "destination_zone" => ($this->getDestinationZone() != null ? $this->getDestinationZone()->jsonSerializeIDs() : null)
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
        $instance = new self($resArr['id'], $resArr['nodes_type_id'], $resArr['level'], $resArr['dest_zone_id'], $resArr['latitude'], $resArr['longitude']);
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
    /** The node is a start point where a QR code can be scanned */
    public const type_qr_start =    4;
    /** The node does not have a specific type, you should not use this type unless
     *  completely necessary, is preferred to add a new node type than using this type. 
     *  @deprecated */
    public const type_other =       100;

    private const _available_types = [self::type_stair, self::type_space, self::type_graph_node, self::type_qr_start, self::type_other];

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