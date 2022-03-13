<?php

/**
 * Class to represent a Destination_Zone in the database.
 * Each Destination_Zone has a unique ID, a name to easily identify it and an
 * associated Node for the graph
 */
class Destination_Zone extends Basic_Info {

    private const table_name = "destination_zones";

    /** ID for the Node associated with this Destination_Zone
     * @var int */
    protected $_main_node_id;

    /** Attribute to act as a cache for the Node object to not request it every time
     * @var Node */
    protected ?Node $_main_node_obj;

    /**
     * Create a new Destination_Zone instance
     *
     * @param integer $id Unique database ID for the Destination_Zone
     * @param string $name Name for the Destination_Zone
     * @param int|null $espai_id ID for the Node associated with this Destination_Zone
     */
    public function __construct(int $id, string $name, int $main_node_id){
        parent::__construct($id, $name);
        $this->_main_node_id = $main_node_id;
        $this->_main_node_obj = null;
    }

    /**
     * Get the main Node associated with this Destination_Zone
     *
     * @param boolean $id Whether to return only the id of the Node or the full object
     * 
     * @return Node|int|false The Node instance or the Node id depending on $id or false on error
     */
    public function getMainNode(bool $id = false) {
        if($id === true) return $this->_main_node_id;
        else{
            if($this->_main_node_obj === null) $this->_main_node_obj = $this->getEPSMap()->getNode($this->_main_node_id);
            return $this->_main_node_obj;
        }
    }
    
    /**
     * Set the main Node associated with this Destination_Zone
     * 
     * @return boolean True on success, false on error
     */
    public function setMainNode(Node $node): bool {
        $db = $this->getEPSMap()->getDB();
        $logger = $this->getEPSMap()->error_logger;

        $classname = self::getTableName();

        $db->startTransaction();
        $queryStr = "UPDATE `$classname` SET `main_node_id` = :main_node_id WHERE `id` = :id";

        $res = $db->getResultPrepared($queryStr, [":id" => $this->getID(), ":main_node_id" => $node->getID()]);
        if($res === false){
            $logger->error($db->getErrorMsg());
            return false;
        }

        $db->commitTransaction();
        $this->_main_node_id = $node->getID();
        $this->_main_node_obj = $node;

        return true;
    }

    public static function getTableName(): string {
        return self::table_name;
    }

    /**
     * Get a Destination_Zone instance by the Database id
     *
     * @param integer $id The unique ID for the Destination_Zone
     * @param EPS_Map $eps_map Current EPS_Map instance
     * 
     * @return Destination_Zone|null|false The Destination_Zone instance, null if not found or false on error
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
     * Get a Destination_Zone instance by a full database row
     *
     * @param array $resArr Database returned row
     * @param EPS_Map $eps_map Current EPS_Map instance
     * 
     * @return Destination_Zone
     */
    public static function getInstanceByData(array $resArr, EPS_Map $eps_map): Destination_Zone {
        $instance = new self($resArr['id'], $resArr['name'], $resArr['main_node_id']);
        $instance->setEPSMap($eps_map);
        
        return $instance;
    }
}

?>