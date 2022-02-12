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
        $db = $this->getEPSMap()->getDB();

        $classname = self::getTableName();

        $db->startTransaction();
        $queryStr = "UPDATE `$classname` SET `name` = :name WHERE `id` = :id"; // Each object will have a different table_name

        $res = $db->getResultPrepared($queryStr, [":id" => $this->getID(), ":name" => $name]);
        if($res === false){
            var_dump($db->getErrorMsg());
            return false;
        }

        $db->commitTransaction();
        $this->_name = $name;

        return true;
    }

    public abstract static function getTableName(): string;
    public static abstract function getInstanceByData(array $resArr, EPS_Map $eps_map): Basic_Info;
}

?>