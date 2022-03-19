<?php

/**
 * Every database object MUST extend this class and overwrite the table_name constant
 * with its database table name.
 */
abstract class DB_Object implements JsonSerializable {
    
    /** Main controller class
     * @var EPS_Map */
    protected EPS_Map $_eps_map;

    public function __construct(){}

    /**
     * Set the EPS_Map object
     * 
     * @param EPS_Map $eps_map
     *
     * @return void
     */
    public function setEPSMap(EPS_Map $eps_map): void {
        $this->_eps_map = $eps_map;
    }

    /**
     * Get the EPS_Map main controller object
     *
     * @return EPS_Map
     */
    public function getEPSMap(): EPS_Map {
        return $this->_eps_map;
    }

    /**
     * This method should be called instead of jsonSerialize when serializing objects that extend this class.  
     * This is done to avoid infinite recursivity between objects.  
     * This method will only get the IDs of the objects instead of the whole JSON object.
     *
     * @return array
     */
    protected abstract function jsonSerializeIDs(): array;

    /**
     * Get the database table name for the current object
     *
     * @return string
     */
    public abstract static function getTableName(): string;

    /**
     * Get a DB_Object instance by the Database id
     *
     * @param integer $id The unique ID for the DB_Object
     * @param EPS_Map $eps_map Current EPS_Map instance
     * 
     * @return DB_Object|null|false The DB_Object instance, null if not found or false on error
     */
    public abstract static function getInstance(int $id, EPS_Map $eps_map);

    /**
     * Get an DB_Object instance by a full database row
     *
     * @param array $resArr Database returned row
     * @param EPS_Map $eps_map Current EPS_Map instance
     * 
     * @return DB_Object
     */
    public abstract static function getInstanceByData(array $resArr, EPS_Map $eps_map): DB_Object;
}

?>