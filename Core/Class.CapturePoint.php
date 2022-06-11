<?php

class CapturePoint extends DB_Object {
    private const table_name = "capture_points";

    /** Unique ID for the CapturePoint
     * @var int */
    protected int $_id;

    /** Node ID where the CapturePoint is attached to
     * @var int */
    protected int $_node_id;

    /** Attribute to act as a cache for the Node object to not request it every time
     * @var Node|null */
    protected ?Node $_node_obj;

    /** Direction the user is facing when capturing the point (one of the Directions)
     * @var string */
    protected string $_face_direction;
    
    /**
     * Get an instance of a CapturePoint
     *
     * @param integer $id Unique ID for the CapturePoint
     * @param integer $node_id Node ID where the CapturePoint is attached to
     * @param string $face_direction Direction the user is facing when capturing the point (one of the Directions)
     */
    public function __construct(int $id, int $node_id, string $face_direction){
        $this->_id = $id;
        $this->_node_id = $node_id;
        $this->_node_obj = null;
        $this->_face_direction = $face_direction;
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
     * Get the Node where this CapturePoint is attached to
     *
     * @param boolean $id
     * @return Node|int|null|false The Node instance or ID depending on $id or false on error
     */
    public function getNode($id = false) {
        if($id === true) return $this->_node_id;
        else{
            if($this->_node_obj === null) $this->_node_obj = $this->getEPSMap()->getNode($this->_node_id);
            return $this->_node_obj;
        }
    }

    /**
     * Get the direction the user is facing when capturing the point (one of the Directions)
     *
     * @return string
     */
    public function getFaceDirection(): string {
        return $this->_face_direction;
    }
    
    protected function jsonSerializeIDs(): array {
        return [
            "id" => $this->getID(),
            "node" => $this->getNode(true),
            "face_direction" => $this->getFaceDirection()
        ];
    }
    
    public function jsonSerialize(): array{
        return [
            "id" => $this->getID(),
            "node" => $this->getNode(false)->jsonSerializeIDs(),
            "face_direction" => $this->getFaceDirection()
        ];
    }
    
    public static function getTableName(): string {
        return self::table_name;
    }

    /**
     * Get a CapturePoint instance by the Database id
     *
     * @param integer $id The unique ID for the CapturePoint
     * @param EPS_Map $eps_map Current EPS_Map instance
     * 
     * @return CapturePoint|null|false The CapturePoint instance, null if not found or false on error
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
     * Get a CapturePoint instance by a full database row
     *
     * @param array $resArr Database returned row
     * @param EPS_Map $eps_map Current EPS_Map instance
     * 
     * @return CapturePoint
     */
    public static function getInstanceByData(array $resArr, EPS_Map $eps_map): CapturePoint {
        $instance = new self($resArr['id'], $resArr['node_id'], $resArr['face_direction']);
        $instance->setEPSMap($eps_map);
        
        return $instance;
    }
}

?>