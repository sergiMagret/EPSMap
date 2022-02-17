<?php

/**
 * Class to interact on a top level domain with all the objects on the database
 */
class EPS_Map extends Logging {
    /** Class to access the database
     * @var DB_Access */
    protected DB_Access $_db;

    /** List of available classnames
     * @var array */
    protected array $_classnames;

    /**
     * Get a new instance
     *
     * @param DB_Access $db The db object to access the underlying database
     */
    public function __construct(DB_Access $db){
        $this->_db = $db;
        $this->_classnames = [
            "door" => "Door",
            "building" => "Building",
            "edge" => "Edge",
            "node" => "Node",
            "person" => "Person",
            "space" => "Space",
            "destination_zone" => "Destination_Zone",
            "department" => "Department",
            "language" => "Language"
        ];

        parent::__construct();
    }

    /**
     * Get the DB_Access object to interact with the database
     *
     * @return DB_Access
     */
    public function getDB(): DB_Access {
        return $this->_db;
    }

    /**
     * Get the correct classname for a class  
     * 
     * List of available classes:
     * - building
     * - destination_zone
     * - door
     * - edge
     * - node_type
     * - node
     * - person
     * - space_type
     * - space
     * - department
     * - language
     *
     * @param string $class_name
     * 
     * @return string|null The classname as a string or null if not valid
     */
    public function getClassname(string $class_name) {
        return $this->_classnames[strtolower($class_name)] ?? null;
    }

    /**
     * Add a new door into the database
     *
     * @param string $name The name for the new door
     * 
     * @return Door|false
     */
    public function addDoor(string $name){
        $db = $this->getDB();
        $tablename = $this->getClassname("door")::getTableName();

        $db->startTransaction();
        $queryStr = "INSERT INTO `$tablename` (`name`) VALUES (:name)";

        $res = $db->getResultPrepared($queryStr, [":name" => $name]);
        if($res === false){
            var_dump($db->getErrorMsg());
            return false;
        }

        $door_id = $db->getInsertID();

        $db->commitTransaction();

        return $this->getDoor($door_id);
    }

    /**
     * Add a new building into the database
     *
     * @param string $name The name for the new building
     * 
     * @return Building|false
     */
    public function addBuilding(string $name){
        $db = $this->getDB();
        $tablename = $this->getClassname("building")::getTableName();

        $db->startTransaction();
        $queryStr = "INSERT INTO `$tablename` (`name`) VALUES (:name)";

        $res = $db->getResultPrepared($queryStr, [":name" => $name]);
        if($res === false){
            var_dump($db->getErrorMsg());
            return false;
        }

        $building_id = $db->getInsertID();

        $db->commitTransaction();

        return $this->getBuilding($building_id);
    }
    
    /**
     * Add a new Destination Zone into the database
     *
     * @param string $name The name for the new building
     * 
     * @return Destination_Zone|false
     */
    public function addDestinationZone(string $name, Node $main_node){
        $db = $this->getDB();
        $tablename = $this->getClassname("destination_zone")::getTableName();

        $db->startTransaction();
        $queryStr = "INSERT INTO `$tablename` (`name`, `main_node_id`) VALUES (:name, :main_node_id)";

        $res = $db->getResultPrepared($queryStr, [":name" => $name, ":main_node_id" => $main_node->getID()]);
        if($res === false){
            var_dump($db->getErrorMsg());
            return false;
        }

        $dz_id = $db->getInsertID();

        $db->commitTransaction();

        return $this->getDestinationZone($dz_id);
    }

    /**
     * Add a new node into the database
     *
     * @param int $node_type The type for the new node, available in Node_Type class
     * @param int|null $level In which level of the building the node is located
     * 
     * @return Node|false
     */
    public function addNode(int $node_type, int $level, ?Destination_Zone $destination_zone){
        $db = $this->getDB();
        $tablename = $this->getClassname("node")::getTableName();

        if(!Node_Type::isTypeValid($node_type)) throw new Invalid_NodeType_Exception("Invalid node type: $node_type");

        $destination_zone_id = ($destination_zone != null ? $destination_zone->getID() : null);

        $db->startTransaction();
        $queryStr = "INSERT INTO `$tablename` (`nodes_type_id`, `level`, `dest_zone_id`) VALUES (:type_id, :level, :dz_id)";

        $res = $db->getResultPrepared($queryStr, [":type_id" => $node_type, ":level" => $level, ":dz_id" => $destination_zone->getID()]);
        if($res === false){
            var_dump($db->getErrorMsg());
            return false;
        }

        $node_id = $db->getInsertID();

        $db->commitTransaction();

        return $this->getNode($node_id);
    }

    /**
     * Add a new person into the database
     *
     * @param string $name The name for the new person
     * @param Space|null $space The space where the person is located (if any)
     * 
     * @return Person|false
     */
    public function addPerson(string $name, ?Space $space=null){
        $db = $this->getDB();
        $tablename = $this->getClassname("person")::getTableName();

        $space_id = ($space != null ? $space->getID() : null);

        $db->startTransaction();
        $queryStr = "INSERT INTO `$tablename` (`name`, `space_id`) VALUES (:name, :space_id)";

        $res = $db->getResultPrepared($queryStr, [":name" => $name, ":space_id" => $space_id]);
        if($res === false){
            var_dump($db->getErrorMsg());
            return false;
        }

        $person_id = $db->getInsertID();

        $db->commitTransaction();

        return $this->getPerson($person_id);
    }

    /**
     * Add a new space into the database
     *
     * @param string $name The full name for the space
     * @param string $alias A short alias for the space
     * @param int $space_type The type of space
     * @param Building $building In what building the space is in
     * @param Door $entrance_door The door used to ented into the space
     * @param Node $associated_node The associated node for the space, this node is where the graph will lead to
     * 
     * @return Space|false
     */
    public function addSpace(string $name, string $alias, int $space_type, Building $building, Door $entrance_door, Node $associated_node){
        $db = $this->getDB();
        $tablename = $this->getClassname("space")::getTableName();

        if(!Space_Type::isTypeValid($space_type)) throw new Invalid_SpaceType_Exception("Invalid space type: $space_type");

        $db->startTransaction();
        $queryStr = "INSERT INTO `$tablename` (`name`, `alias`, `spaces_type_id`, `building_id`, `door_id`, `node_id`) VALUES (:name, :alias, :space_type_id, :building_id, :door_id, :node_id)";

        $substitutions = [":name" => $name, ":alias" => $alias, ":space_type_id" => $space_type, ":building_id" => $building->getID(), ":door_id" => $entrance_door->getID(), ":node_id" => $associated_node->getID()];
        $res = $db->getResultPrepared($queryStr, $substitutions);
        if($res === false){
            var_dump($db->getErrorMsg());
            return false;
        }

        $person_id = $db->getInsertID();

        $db->commitTransaction();

        return $this->getSpace($person_id);
    }
    

    /**
     * Get a door by its database ID
     *
     * @param int $id
     * 
     * @return Door|null|false The Door instance, null if not found or false on error
     */
    public function getDoor(int $id){
        return $this->getClassname("door")::getInstance($id, $this);
    }

    /**
     * Get a building by its database ID
     *
     * @param int $id
     * 
     * @return Building|null|false The Building instance, null if not found or false on error
     */
    public function getBuilding(int $id){
        return $this->getClassname("building")::getInstance($id, $this);
    }
    
    /**
     * Get a destination zone by its database ID
     *
     * @param int $id
     * 
     * @return Destination_Zone|null|false The Destination_Zone instance, null if not found or false on error
     */
    public function getDestinationZone(int $id){
        return $this->getClassname("destination_zone")::getInstance($id, $this);
    }

    /**
     * Get a node by its database ID
     *
     * @param int $id
     * 
     * @return Node|null|false The Node instance, null if not found or false on error
     */
    public function getNode(int $id){
        return $this->getClassname("node")::getInstance($id, $this);
    }

    /**
     * Get a person by its database ID
     *
     * @param int $id
     * 
     * @return Person|null|false The Person instance, null if not found or false on error
     */
    public function getPerson(int $id){
        return $this->getClassname("person")::getInstance($id, $this);
    }
    
    /**
     * Get a space by its database ID
     *
     * @param int $id
     * 
     * @return Space|null|false The Space instance, null if not found or false on error
     */
    public function getSpace(int $id){
        return $this->getClassname("space")::getInstance($id, $this);
    }
    
    /**
     * Get a department by its database ID
     *
     * @param int $id
     * 
     * @return Department|null|false The Department instance, null if not found or false on error
     */
    public function getDepartment(int $id){
        return $this->getClassname("department")::getInstance($id, $this);
    }
    
    /**
     * Get a department by its database ID
     *
     * @param int $id
     * 
     * @return Language|null|false The Language instance, null if not found or false on error
     */
    public function getLanguage(int $id){
        return $this->getClassname("language")::getInstance($id, $this);
    }

    // TODO De fet quasi que només es necessita un mètode principal: vull anar d'aqui a aqui, com hi vaig? I ha de retornar el camí
    // TODO Clarament també els altres mètodes per fer gets de professors, etc, etc
    // TODO Els edges (i pot ser nodes) s'haurien de tractar a part, pot ser fer una classe graph??


    public function findPath(Node $from, Node $to){

    }

}

?>