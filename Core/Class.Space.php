<?php

/**
 * Class to represent a Space in the database.
 * A Space has a name, is of a type Space_Type, belongs to an Building, is associated with a Node,
 * and might be associated with a Door as well.
 */
class Space extends Basic_Info {

    private const table_name = "spaces";

    /** Short name (alias) for the Space
     * @var string|null */
    protected ?string $_alias;

    /** ID for the type this Space is
     * @var int */
    protected int $_space_type_id;

    /** ID for the Building where this Space is located in
     * @var int */
    protected int $_building_id;

    /** Attribute to act as a cache for the Building object to not request it every time
     * @var Building */
    protected ?Building $_building_obj;
    
    /** ID for the Node associated with this Space
     * @var int */
    protected int $_node_id;
    
    /** Attribute to act as a cache for the Node object to not request it every time
     * @var Node */
    protected ?Node $_node_obj;
    
    /** When the Node is any type of Space, then the Door ID where the 
     * @var int|null */
    protected ?int $_door_id;
    
    /** Attribute to act as a cache for the Door object to not request it every time
     * @var Door */
    protected ?Door $_door_obj;

    /**
     * Create a new Space instance
     *
     * @param integer $id Unique database ID for the Space
     * @param string $name Name for the Space
     * @param string|null $alias Short name (alias) for the Space
     * @param int $space_type_id ID for the type this Space is
     * @param int $building_id ID for the Building where this Space is located in
     * @param int $node_id ID for the Node associated with this Space
     * @param int|null $door_id ID for the Door associated with this Space
     */
    public function __construct(int $id, string $name, ?string $alias, int $space_type_id, int $building_id, int $node_id, ?int $door_id){
        parent::__construct($id, $name);
        $this->_alias = $alias;
        $this->_space_type_id = $space_type_id;
        $this->_building_id = $building_id;
        $this->_building_obj = null;
        $this->_node_id = $node_id;
        $this->_node_obj = null;
        $this->_door_id = $door_id;
        $this->_door_obj = null;
    }

    /**
     * Get the alias for this Space
     *
     * @return string|null
     */
    public function getAlias() {
        return $this->_alias;
    }

    /**
     * Set the new alias for this Space
     *
     * @param string|null $alias The new alias or null if you want to delete the alias
     * 
     * @return bool True on success, false on failure
     */
    public function setAlias(?string $alias): bool {
        $db = $this->getEPSMap()->getDB();
        $logger = $this->getEPSMap()->error_logger;
        $tablename = self::table_name;

        $db->startTransaction();
        $queryStr = "UPDATE `$tablename` SET `alias` = :alias WHERE `id` = :id";

        $res = $db->getResultPrepared($queryStr, [":id" => $this->getID(), ":name" => $alias]);
        if($res === false){
            $logger->error($db->getErrorMsg());
            return false;
        }

        $db->commitTransaction();
        $this->_alias = $alias;

        return true;
    }

    /**
     * Get the ID for the type this Space is, defined in Space_Type
     *
     * @return integer
     */
    public function getSpaceType(): int {
        return $this->_space_type_id;
    }

    /**
     * Set the ID for the type this Space is, defined in Space_Type
     *
     * @param string $type
     * 
     * @return bool True on success, false on failure
     */
    public function setSpaceType(int $type): bool {
        $db = $this->getEPSMap()->getDB();
        $logger = $this->getEPSMap()->error_logger;
        $tablename = self::table_name;

        $db->startTransaction();
        $queryStr = "UPDATE `$tablename` SET `space_type_id` = :space_type_id WHERE `id` = :id";

        $res = $db->getResultPrepared($queryStr, [":id" => $this->getID(), ":space_type_id" => $type]);
        if($res === false){
            $logger->error($db->getErrorMsg());
            return false;
        }

        $db->commitTransaction();
        $this->_space_type_id = $type;

        return true;
    }

    /**
     * Get the Building where this Space is located in
     *
     * @param boolean $id Whether to return only the id of the Building or the full object
     * 
     * @return Building|int|false The Building instance or the Building id depending on $id or false on error
     */
    public function getBuilding(bool $id = false){
        if($this->_building_id === null) return null;

        if($id === true) return $this->_building_id;
        else{
            if($this->_building_obj === null) $this->_building_obj = $this->getEPSMap()->getBuilding($this->_building_id);
            return $this->_building_obj;
        }
    }

    /**
     * Set the Building this Space is located in
     *
     * @param Building $building The new Building for the Space
     * 
     * @return bool True on success, false on failure
     */
    public function setBuilding(Building $building): bool {
        $db = $this->getEPSMap()->getDB();
        $logger = $this->getEPSMap()->error_logger;
        $tablename = self::table_name;

        $db->startTransaction();
        $queryStr = "UPDATE `$tablename` SET `building_id` = :building_id WHERE `id` = :id";

        $res = $db->getResultPrepared($queryStr, [":id" => $this->getID(), ":building_id" => $building->getID()]);
        if($res === false){
            $logger->error($db->getErrorMsg());
            return false;
        }

        $db->commitTransaction();
        $this->_building_id = $building->getID();
        $this->_building_obj = $building;

        return true;
    }

    /**
     * Get the Node associated with this Space
     *
     * @param boolean $id Whether to return only the id of the Node or the full object
     * 
     * @return Node|int|false The Node instance or the Node id depending on $id or false on error
     */
    public function getNode(bool $id = false){
        if($this->_node_id === null) return null;

        if($id === true) return $this->_node_id;
        else{
            if($this->_node_obj === null) $this->_node_obj = $this->getEPSMap()->getNode($this->_node_id);
            return $this->_node_obj;
        }
    }

    /**
     * Set the Node associated with this Space
     *
     * @param Node $node The new Node for the Space
     * 
     * @return bool True on success, false on failure
     */
    public function setNode(Node $node): bool {
        $db = $this->getEPSMap()->getDB();
        $logger = $this->getEPSMap()->error_logger;
        $tablename = self::table_name;

        $db->startTransaction();
        $queryStr = "UPDATE `$tablename` SET `node_id` = :node_id WHERE `id` = :id";

        $res = $db->getResultPrepared($queryStr, [":id" => $this->getID(), ":node_id" => $node->getID()]);
        if($res === false){
            $logger->error($db->getErrorMsg());
            return false;
        }

        $db->commitTransaction();
        $this->_node_id = $node->getID();
        $this->_node_obj = $node;

        return true;
    }

    /**
     * Get the Door associated with this Space
     *
     * @param boolean $id Whether to return only the id of the Door or the full object
     * 
     * @return Door|int|false The Door instance or the Door id depending on $id or false on error
     */
    public function getDoor(bool $id = false){
        if($this->_door_id === null) return null;

        if($id === true) return $this->_door_id;
        else{
            if($this->_door_obj === null) $this->_door_obj = $this->getEPSMap()->getDoor($this->_door_id);
            return $this->_door_obj;
        }
    }

    /**
     * Set the Door associated with this Space
     *
     * @param Door $door The new Door for the Space
     * 
     * @return bool True on success, false on failure
     */
    public function setDoor(Door $door): bool {
        $db = $this->getEPSMap()->getDB();
        $logger = $this->getEPSMap()->error_logger;
        $tablename = self::table_name;

        $db->startTransaction();
        $queryStr = "UPDATE `$tablename` SET `door_id` = :door_id WHERE `id` = :id";

        $res = $db->getResultPrepared($queryStr, [":id" => $this->getID(), ":door_id" => $door->getID()]);
        if($res === false){
            $logger->error($db->getErrorMsg());
            return false;
        }

        $db->commitTransaction();
        $this->_door_id = $door->getID();
        $this->_door_obj = $door;

        return true;
    }

    /**
     * Get the destination zone where this space belongs
     *
     * @param boolean $id Whether to return the full instance for the Destination Zone or just the ID
     * 
     * @return Destination_Zone|integer|false The Destination Zone instance or its ID depending on $id, false on error
     */
    public function getDestinationZone(bool $id = false){
        return $this->getNode()->getDestinationZone($id);
    }

    public static function searchByName(string $name, EPS_Map $eps_map, int $limit=20, int $offset=0){
        // Notice the use of static instead of self to use the implemented method in the extended class
        $db = $eps_map->getDB();
        $logger = $eps_map->error_logger;
        $tablename = static::getTableName();

        $queryStr = "SELECT * FROM `$tablename` WHERE LOWER(`name`) LIKE CONCAT(LOWER(:name), \"%\") OR LOWER(`alias`) LIKE CONCAT(LOWER(:name2), \"%\") ORDER BY `name` LIMIT :limit OFFSET :offset";
        $substitutions = [":name" => $name, ":name2" => $name, ":limit" => $limit, ":offset" => $offset];
        $resArr = $db->getResultArrayPrepared($queryStr, $substitutions);
        if($resArr === false){
            $logger->error("Error searching by name or alias", ["queryStr" => $queryStr, "substitutions" => $substitutions]);
            $logger->error($db->getErrorMsg());
            return false;
        }

        $basic_info = [];
        foreach($resArr as $row) $basic_info[] = static::getInstanceByData($row, $eps_map);

        return $basic_info;
    }

    public function jsonSerializeIDs(): array {
        $json_res = parent::jsonSerializeIDs();
        $json_res['alias'] = $this->getAlias();
        $json_res['space_type_id'] = $this->getSpaceType();
        $json_res['building'] = $this->getBuilding(true);
        $json_res['door'] = $this->getDoor(true);
        $json_res['node'] = $this->getNode(true);
        
        return $json_res;
    }

    public function jsonSerialize(): array {
        $json_res = parent::jsonSerialize();
        $json_res['alias'] = $this->getAlias();
        $json_res['space_type_id'] = $this->getSpaceType();
        $json_res['building'] = $this->getBuilding()->jsonSerializeIDs();
        $json_res['door'] = $this->getDoor()->jsonSerializeIDs();
        $json_res['node'] = $this->getNode()->jsonSerializeIDs();
        
        return $json_res;
    }

    public static function getTableName(): string {
        return self::table_name;
    }

    /**
     * Get a Space instance by the Database id
     *
     * @param integer $id The unique ID for the Space
     * @param EPS_Map $eps_map Current EPS_Map instance
     * 
     * @return Space|null|false The Space instance, null if not found or false on error
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
     * Get a Space instance by a full database row
     *
     * @param array $resArr Database returned row
     * @param EPS_Map $eps_map Current EPS_Map instance
     * 
     * @return Space
     */
    public static function getInstanceByData(array $resArr, EPS_Map $eps_map): Space {
        $instance = new self($resArr['id'], $resArr['name'], $resArr['alias'], $resArr['spaces_type_id'], $resArr['building_id'], $resArr['node_id'], $resArr['door_id']);
        $instance->setEPSMap($eps_map);
        
        return $instance;
    }
}

/**
 * Class to represent the list of possible Space_Types 
 */
class Space_Type {

    public const type_class =       1;
    public const type_toilet =      2;
    public const type_office =      3;
    public const type_lab =         4;
    public const type_class_comp =  5;
    public const type_greenhouse =  6;
    public const type_auditorium =  7;

    private const _available_types = [self::type_class, self::type_toilet, self::type_office, self::type_lab, self::type_class_comp, self::type_greenhouse, self::type_auditorium];

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