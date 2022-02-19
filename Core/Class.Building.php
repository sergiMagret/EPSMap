<?php

/**
 * Class to represent an Building in the database.
 * An Building has a name and can have many Spaces
 */
class Building extends Basic_Info {

    private const table_name = "buildings";

    /**
     * Create a new Building instance
     *
     * @param integer $id Unique database ID for the Building
     * @param string $name Name for the Building
     */
    public function __construct(int $id, string $name){
        parent::__construct($id, $name);
    }

    public static function getTableName(): string {
        return self::table_name;
    }

    /**
     * Get a Building instance by the Database id
     *
     * @param integer $id The unique ID for the Building
     * @param EPS_Map $eps_map Current EPS_Map instance
     * 
     * @return Building|null|false The Building instance, null if not found or false on error
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
     * Get an Building instance by a full database row
     *
     * @param array $resArr Database returned row
     * @param EPS_Map $eps_map Current EPS_Map instance
     * 
     * @return Building
     */
    public static function getInstanceByData(array $resArr, EPS_Map $eps_map): Building {
        $instance = new self($resArr['id'], $resArr['name']);
        $instance->setEPSMap($eps_map);

        return $instance;
    }
}

?>