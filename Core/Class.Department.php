<?php

/**
 * Class to represent an Department in the database.
 * An Department has a name, an alias and can have many people associated with it
 */
class Department extends Basic_Info {

    private const table_name = "departments";

    /** Short name (alias) for the Department
     * @var string|null */
    protected ?string $_alias;

    /**
     * Create a new Department instance
     *
     * @param integer $id Unique database ID for the Department
     * @param string $name Name for the Department
     * @param string|null $alias Short name (alias) for the Space
     */
    public function __construct(int $id, string $name, ?string $alias){
        parent::__construct($id, $name);
        $this->_alias = $alias;
    }

    /**
     * Get the alias for this Department
     *
     * @return string|null
     */
    public function getAlias() {
        return $this->_alias;
    }

    /**
     * Set the new alias for this Department
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

    public static function searchByName(string $name, EPS_Map $eps_map, int $limit=20, int $offset=0){
        // Notice the use of static instead of self to use the implemented method in the extended class
        $db = $eps_map->getDB();
        $logger = $eps_map->error_logger;
        $tablename = static::getTableName();

        $queryStr = "SELECT * FROM `$tablename` WHERE LOWER(`name`) LIKE CONCAT(\"%\", LOWER(:name), \"%\") OR LOWER(`alias`) LIKE CONCAT(\"%\", LOWER(:name2), \"%\") ORDER BY `name` LIMIT :limit OFFSET :offset";
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

    public function jsonSerialize(): array {
        $json_res = parent::jsonSerialize();
        $json_res['alias'] = $this->getAlias();

        return $json_res;
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
     * Get an Department instance by a full database row
     *
     * @param array $resArr Database returned row
     * @param EPS_Map $eps_map Current EPS_Map instance
     * 
     * @return Department
     */
    public static function getInstanceByData(array $resArr, EPS_Map $eps_map): Department {
        $instance = new self($resArr['id'], $resArr['name'], $resArr['alias']);
        $instance->setEPSMap($eps_map);

        return $instance;
    }
}

?>