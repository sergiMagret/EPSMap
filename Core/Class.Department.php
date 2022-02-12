<?php

/**
 * Class to represent an Department in the database.
 * An Department has a name and can have many people associated with it
 */
class Department extends Basic_Info {

    private const table_name = "departments";

    /**
     * Create a new Department instance
     *
     * @param integer $id Unique database ID for the Department
     * @param string $name Name for the Department
     */
    public function __construct(int $id, string $name){
        parent::__construct($id, $name);
    }

    public static function getTableName(): string {
        return self::table_name;
    }

    /**
     * Get a Department instance by the Database id
     *
     * @param integer $id The unique ID for the Department
     * @param EPS_Map $eps_map Current EPS_Map instance
     * 
     * @return Department|null|false The Department instance, null if not found or false on error
     */
    public static function getInstance(int $id, EPS_Map $eps_map){
        $db = $eps_map->getDB();

        $tablename = self::table_name;

        $queryStr = "SELECT * FROM `$tablename` WHERE id = :id"; // Each object will have a different table_name
        $resArr = $db->getResultArrayPrepared($queryStr, [":id" => $id]);
        if($resArr === false){
            var_dump($db->getErrorMsg());
            return false;
        }

        if(count($resArr) == 0) return null;

        return self::getInstanceByData($resArr[0], $eps_map);
    }

    /**
     * Get an Department instance by a full database row
     *
     * @param array $resArr Database returned row
     * @param EPS_Map $eps_map Current EPS_Map instance
     * 
     * @return Department
     */
    public static function getInstanceByData(array $resArr, EPS_Map $eps_map): Department {
        $instance = new self($resArr['id'], $resArr['name']);
        $instance->setEPSMap($eps_map);

        return $instance;
    }
}

?>