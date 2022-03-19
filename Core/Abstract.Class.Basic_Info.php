<?php

/**
 * Class with the basic info for an object, ID and name
 */
abstract class Basic_Info extends DB_Object {

    /** Unique ID for the resource
     * @var int */
    protected int $_id;

    /** Name for the resource
     * @var string */
    protected string $_name;

    /**
     * Create a new Basic_Info instance with the basic information for an object
     *
     * @param integer $id Unique database ID for the resource
     * @param string $name Name for the resource
     */
    public function __construct(int $id, string $name) {
        $this->_id = $id;
        $this->_name = $name;
    }

    /**
     * Get the unique ID
     *
     * @return integer
     */
    public function getID(): string {
        return $this->_id;
    }

    /**
     * Get the name for this resource
     *
     * @return string
     */
    public function getName(): string {
        return $this->_name;
    }

    /**
     * Set the new name
     *
     * @param string $name
     * 
     * @return boolean True on success, false on failure
     */
    public function setName(string $name): bool {
        // Notice the use of static instead of self to use the implemented method in the extended class
        $db = $this->getEPSMap()->getDB();
        $logger = $this->getEPSMap()->error_logger;

        $classname = static::getTableName();

        $db->startTransaction();
        $queryStr = "UPDATE `$classname` SET `name` = :name WHERE `id` = :id"; // Each object will have a different table_name

        $res = $db->getResultPrepared($queryStr, [":id" => $this->getID(), ":name" => $name]);
        if($res === false){
            $this->error_logger->error($db->getErrorMsg());
            $logger->error($db->getErrorMsg());
            return false;
        }

        $db->commitTransaction();
        $this->_name = $name;

        return true;
    }

    /**
     * Search in the database for an object with name $name
     *
     * @param string $name
     * @param EPS_Map $eps_map
     * @param integer $limit
     * @param integer $offset
     * @return Basic_Info[]|false The list of found objects or false on error
     */
    public static function searchByName(string $name, EPS_Map $eps_map, int $limit=20, int $offset=0){
        // Notice the use of static instead of self to use the implemented method in the extended class
        $db = $eps_map->getDB();
        $logger = $eps_map->error_logger;
        $tablename = static::getTableName();

        $queryStr = "SELECT * FROM `$tablename` WHERE LOWER(`name`) LIKE CONCAT(LOWER(:name), \"%\") ORDER BY `name` LIMIT :limit OFFSET :offset";
        $substitutions = [":name" => $name, ":limit" => $limit, ":offset" => $offset];
        $resArr = $db->getResultArrayPrepared($queryStr, $substitutions);
        if($resArr === false){
            $logger->error("Error searching by name", ["queryStr" => $queryStr, "substitutions" => $substitutions]);
            $logger->error($db->getErrorMsg());
            return false;
        }

        $basic_info = [];
        foreach($resArr as $row) $basic_info[] = static::getInstanceByData($row, $eps_map);

        return $basic_info;
    }

    public function jsonSerialize(): array{
        return [
            "id" => $this->getID(),
            "name" => $this->getName()
        ];
    }

    public static abstract function getTableName(): string;
    public static abstract function getInstanceByData(array $resArr, EPS_Map $eps_map): Basic_Info;
}

?>