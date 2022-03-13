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
            "building" => "Building",
            "department" => "Department",
            "destination_zone" => "Destination_Zone",
            "door" => "Door",
            "edge" => "Edge",
            "instruction" => "Instruction",
            "language" => "Language",
            "node" => "Node",
            "person" => "Person",
            "space" => "Space",
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
     * - department
     * - destination_zone
     * - door
     * - edge
     * - instruction
     * - language
     * - node
     * - person
     * - space
     *
     * @param string $class_name
     * 
     * @return string|null The classname as a string or null if not valid
     */
    public function getClassname(string $class_name) {
        return $this->_classnames[strtolower($class_name)] ?? null;
    }

    /**********************************************************/
    /* START OF THE FUNCTIONS TO CREATE NEW OBJECTS IN THE DB */
    /**********************************************************/

    /**
     * Add a new door into the database
     *
     * @param string $name The name for the new door
     * 
     * @return Door|false
     */
    public function addDoor(string $name){
        $db = $this->getDB();
        $logger = $this->error_logger;
        $tablename = $this->getClassname("door")::getTableName();

        $db->startTransaction();
        $queryStr = "INSERT INTO `$tablename` (`name`) VALUES (:name)";

        $res = $db->getResultPrepared($queryStr, [":name" => $name]);
        if($res === false){
            $logger->error("Error adding door with name $name", ["queryStr" => $queryStr]);
            $logger->error($db->getErrorMsg());
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
        $logger = $this->error_logger;
        $tablename = $this->getClassname("building")::getTableName();

        $db->startTransaction();
        $queryStr = "INSERT INTO `$tablename` (`name`) VALUES (:name)";

        $res = $db->getResultPrepared($queryStr, [":name" => $name]);
        if($res === false){
            $logger->error("Error adding building with name $name", ["queryStr" => $queryStr]);
            $logger->error($db->getErrorMsg());
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
        $logger = $this->error_logger;
        $tablename = $this->getClassname("destination_zone")::getTableName();

        $db->startTransaction();
        $queryStr = "INSERT INTO `$tablename` (`name`, `main_node_id`) VALUES (:name, :main_node_id)";
        $substitutions = [":name" => $name, ":main_node_id" => $main_node->getID()];

        $res = $db->getResultPrepared($queryStr, $substitutions);
        if($res === false){
            $logger->error("Error adding destination zone", ["queryStr" => $queryStr, "substitutions" => $substitutions]);
            $logger->error($db->getErrorMsg());
            return false;
        }

        $dz_id = $db->getInsertID();

        $db->commitTransaction();

        return $this->getDestinationZone($dz_id);
    }
    
    /**
     * Add a new Department into the database
     *
     * @param string $name The name for the new Department
     * @param string|null $alias The alias for the new Department
     * 
     * @return Department|false
     */
    public function addDepartment(string $name, ?string $alias=null){
        $db = $this->getDB();
        $logger = $this->error_logger;
        $tablename = $this->getClassname("department")::getTableName();

        $db->startTransaction();
        $queryStr = "INSERT INTO `$tablename` (`name`, `alias`) VALUES (:name, :alias)";
        $substitutions = [":name" => $name, ":alias" => $alias];

        $res = $db->getResultPrepared($queryStr, $substitutions);
        if($res === false){
            $logger->error("Error adding alias", ["queryStr" => $queryStr, "substitutions" => $substitutions]);
            $logger->error($db->getErrorMsg());
            return false;
        }

        $alias_id = $db->getInsertID();

        $db->commitTransaction();

        return $this->getDepartment($alias_id);
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
        $logger = $this->error_logger;
        $tablename = $this->getClassname("node")::getTableName();

        if(!Node_Type::isTypeValid($node_type)) throw new Invalid_NodeType_Exception("Invalid node type: $node_type");

        $destination_zone_id = ($destination_zone != null ? $destination_zone->getID() : null);

        $db->startTransaction();
        $queryStr = "INSERT INTO `$tablename` (`nodes_type_id`, `level`, `dest_zone_id`) VALUES (:type_id, :level, :dz_id)";
        $substitutions = [":type_id" => $node_type, ":level" => $level, ":dz_id" => $destination_zone_id];

        $res = $db->getResultPrepared($queryStr, $substitutions);
        if($res === false){
            $logger->error("Error adding node", ["queryStr" => $queryStr, "substitutions" => $substitutions]);
            $logger->error($db->getErrorMsg());
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
        $logger = $this->error_logger;
        $tablename = $this->getClassname("person")::getTableName();

        $space_id = ($space != null ? $space->getID() : null);

        $db->startTransaction();
        $queryStr = "INSERT INTO `$tablename` (`name`, `space_id`) VALUES (:name, :space_id)";
        $substitutions = [":name" => $name, ":space_id" => $space_id];

        $res = $db->getResultPrepared($queryStr, $substitutions);
        if($res === false){
            $logger->error("Error adding person", ["queryStr" => $queryStr, "substitutions" => $substitutions]);
            $logger->error($db->getErrorMsg());
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
        $logger = $this->error_logger;
        $tablename = $this->getClassname("space")::getTableName();

        if(!Space_Type::isTypeValid($space_type)) throw new Invalid_SpaceType_Exception("Invalid space type: $space_type");

        $db->startTransaction();
        $queryStr = "INSERT INTO `$tablename` (`name`, `alias`, `spaces_type_id`, `building_id`, `door_id`, `node_id`) VALUES (:name, :alias, :space_type_id, :building_id, :door_id, :node_id)";

        $substitutions = [":name" => $name, ":alias" => $alias, ":space_type_id" => $space_type, ":building_id" => $building->getID(), ":door_id" => $entrance_door->getID(), ":node_id" => $associated_node->getID()];
        $res = $db->getResultPrepared($queryStr, $substitutions);
        if($res === false){
            $logger->error("Error adding space", ["queryStr" => $queryStr, "substitutions" => $substitutions]);
            $logger->error($db->getErrorMsg());
            return false;
        }

        $person_id = $db->getInsertID();

        $db->commitTransaction();

        return $this->getSpace($person_id);
    }

    /**********************************************************/
    /** END OF THE FUNCTIONS TO CREATE NEW OBJECTS IN THE DB **/
    /**********************************************************/

    /**********************************************************/
    /****************** START OF THE GETTERS ******************/
    /**********************************************************/

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
     * Get a language by its database ID
     *
     * @param int $id
     * 
     * @return Language|null|false The Language instance, null if not found or false on error
     */
    public function getLanguage(int $id){
        return $this->getClassname("language")::getInstance($id, $this);
    }
    
    /**
     * Get a language by its short name
     *
     * @param string $short_name
     * 
     * @return Language|null|false The Language instance, null if not found or false on error
     */
    public function getLanguageByShortName(string $short_name){
        return $this->getClassname("language")::getInstanceByShortName($short_name, $this);
    }
    
    /**
     * Get an instruction by its database ID
     *
     * @param int $id
     * 
     * @return Instruction|null|false The Instruction instance, null if not found or false on error
     */
    public function getInstruction(int $id){
        return $this->getClassname("instruction")::getInstance($id, $this);
    }

    /**********************************************************/
    /******************* END OF THE GETTERS *******************/
    /**********************************************************/
    
    /**********************************************************/
    /************* START OF THE SEARCH FUNCTIONS **************/
    /**********************************************************/

    /**
     * Get a list of doors whose name start with $search_name
     *
     * @param string $search_name
     * @return Door[]|false The list of Doors (empty is none) or false on error
     */
    public function searchDoor(string $search_name){
        return $this->getClassname("door")::searchByName($search_name, $this);
    }
    
    /**
     * Get a list of buildings whose name start with $search_name
     *
     * @param string $search_name
     * @return Building[]|false The list of Buildings (empty is none) or false on error
     */
    public function searchBuilding(string $search_name){
        return $this->getClassname("building")::searchByName($search_name, $this);
    }
    
    /**
     * Get a list of destination zones whose name start with $search_name
     *
     * @param string $search_name
     * @return Destination_Zone[]|false The list of Destination_Zones (empty is none) or false on error
     */
    public function searchDestinationZone(string $search_name){
        return $this->getClassname("destination_zone")::searchByName($search_name, $this);
    }
    
    /**
     * Get a list of persons whose name start with $search_name
     *
     * @param string $search_name
     * @return Person[]|false The list of Persons (empty is none) or false on error
     */
    public function searchPerson(string $search_name){
        return $this->getClassname("person")::searchByName($search_name, $this);
    }
    
    /**
     * Get a list of spaces whose name start with $search_name
     *
     * @param string $search_name
     * @return Space[]|false The list of Spaces (empty is none) or false on error
     */
    public function searchSpace(string $search_name){
        return $this->getClassname("space")::searchByName($search_name, $this);
    }
    
    /**
     * Get a list of departments whose name start with $search_name
     *
     * @param string $search_name
     * @return Department[]|false The list of Departments (empty is none) or false on error
     */
    public function searchDepartment(string $search_name){
        return $this->getClassname("department")::searchByName($search_name, $this);
    }
    
    /**
     * Get a list of languages whose name start with $search_name
     *
     * @param string $search_name
     * @return Language[]|false The list of Languages (empty is none) or false on error
     */
    public function searchLanguage(string $search_name){
        return $this->getClassname("language")::searchByName($search_name, $this);
    }
    
    /**
     * Get a list of instructions whose name start with $search_name
     *
     * @param string $search_name
     * @return Instruction[]|false The list of Instructions (empty is none) or false on error
     */
    public function searchInstruction(string $search_name){
        return $this->getClassname("instruction")::searchByName($search_name, $this);
    }

    // TODO De fet quasi que només es necessita un mètode principal: vull anar d'aqui a aqui, com hi vaig? I ha de retornar el camí
    // TODO Clarament també els altres mètodes per fer gets de professors, etc, etc
    // TODO Els edges (i pot ser nodes) s'haurien de tractar a part, pot ser fer una classe graph??


    /**
     * Get all the Nodes
     *
     * @return Node[]|false The list of Nodes or false on error
     */
    public function getAllNodes(){
        $db = $this->getDB();
        $logger = $this->error_logger;
        $classname = $this->getClassname("node");
        $tablename = $classname::getTableName();

        $queryStr = "SELECT * FROM `$tablename`";
        $resArr = $db->getResultArrayPrepared($queryStr);
        if($resArr === false){
            $logger->error("Error getting all nodes", ["queryStr" => $queryStr]);
            $logger->error($db->getErrorMsg());
            return false;
        }

        $nodes = [];
        foreach($resArr as $row) $nodes[] = $classname::getInstanceByData($row, $this);

        return $nodes;
    }
    
    /**
     * Get all the Edges
     *
     * @return Edge[]|false The list of Edges or false on error
     */
    public function getAllEdges(){
        $db = $this->getDB();
        $logger = $this->error_logger;
        $classname = $this->getClassname("edge");
        $tablename = $classname::getTableName();

        $queryStr = "SELECT * FROM `$tablename`";
        $resArr = $db->getResultArrayPrepared($queryStr);
        if($resArr === false){
            $logger->error("Error getting all edges", ["queryStr" => $queryStr]);
            $logger->error($db->getErrorMsg());
            return false;
        }

        $edges = [];
        foreach($resArr as $row) $edges[] = $classname::getInstanceByData($row, $this);

        return $edges;
    }


    public function findPath(Node $from, Node $to){

    }

}

?>