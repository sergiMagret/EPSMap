<?php

/**
* Class to represent to list of Person in the database.
* Each Person has a unique ID, he/she and might be located in a Space and might belong to a Department
*/
class Person extends Basic_Info {

    private const table_name = "people";

    /** ID for the Space where the Person is located in
     * @var int */
    protected ?int $_space_id;
    
    /** Attribute to act as a cache for the Space object to not request it every time
     * @var Space */
    protected ?Space $_space_obj;
    
    /** ID for the Space where the Person is located in
     * @var int */
    protected ?int $_department_id;
    
    /** Attribute to act as a cache for the Department object to not request it every time
     * @var Department */
    protected ?Department $_department_obj;

    /**
     * Create a new Person instance
     *
     * @param integer $id Unique database ID for the Person
     * @param string $name Name for the Person
     * @param int|null $space_id ID for the Space where the Person is located in
     * @param int|null $department_id ID for the Department the Person belongs
     */
    public function __construct(int $id, string $name, ?int $space_id, ?int $department_id){
        parent::__construct($id, $name);
        $this->_space_id = $space_id;
        $this->_space_obj = null;
        $this->_department_id = $department_id;
        $this->_department_obj = null;
    }

    /**
     * Get the Space where this Person is assigned
     *
     * @param boolean $id Whether to return only the id of the space or the full object
     * 
     * @return Space|int|null|false The Space instance or the Space id depending on $id, null if this Person does not have a space assigned or false on error
     */
    public function getSpace(bool $id = false){
        if($this->_space_id === null) return null;

        if($id === true) return $this->_space_id;
        else{
            if($this->_space_obj === null) $this->_space_obj = $this->getEPSMap()->getSpace($this->_space_id);
            return $this->_space_obj;
        }
    }

    /**
     * Set the space assigned to this Person
     *
     * @param Space $space The new assigned space to the Person
     * 
     * @return boolean True on success, false otherwise
     */
    public function setSpace(Space $space): bool {
        $db = $this->getEPSMap()->getDB();

        $classname = self::getTableName();

        $db->startTransaction();
        $queryStr = "UPDATE `$classname` SET `space_id` = :space_id WHERE `id` = :id";

        $res = $db->getResultPrepared($queryStr, [":id" => $this->getID(), ":space_id" => $space->getID()]);
        if($res === false){
            var_dump($db->getErrorMsg());
            return false;
        }

        $db->commitTransaction();
        $this->_space_id = $space->getID();
        $this->_space_obj = $space;

        return true;
    }

    /**
     * Get the Department where this Person is assigned
     *
     * @param boolean $id Whether to return only the id of the Department or the full object
     * 
     * @return Department|int|null|false The Department instance or the Department id depending on $id, null if this Person does not have a department assigned or false on error
     */
    public function getDepartment(bool $id = false){
        if($this->_department_id === null) return null;

        if($id === true) return $this->_department_id;
        else{
            if($this->_department_obj === null) $this->_department_obj = $this->getEPSMap()->getDepartment($this->_department_id);
            return $this->_department_obj;
        }
    }

    /**
     * Set the Department assigned to this Person
     *
     * @param Department $department The new assigned Department to the Person
     * 
     * @return boolean True on success, false otherwise
     */
    public function setDepartment(Department $department): bool {
        $db = $this->getEPSMap()->getDB();

        $classname = self::getTableName();

        $db->startTransaction();
        $queryStr = "UPDATE `$classname` SET `department_id` = :department_id WHERE `id` = :id";

        $res = $db->getResultPrepared($queryStr, [":id" => $this->getID(), ":department_id" => $department->getID()]);
        if($res === false){
            var_dump($db->getErrorMsg());
            return false;
        }

        $db->commitTransaction();
        $this->_department_id = $department->getID();
        $this->_department_obj = $department;

        return true;
    }

    public static function getTableName(): string {
        return self::table_name;
    }

    /**
     * Get a Person instance by the Database id
     *
     * @param integer $id The unique ID for the Person
     * @param EPS_Map $eps_map Current EPS_Map instance
     * 
     * @return Person|null|false The Person instance, null if not found or false on error
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
     * Get a Person instance by a full database row
     *
     * @param array $resArr Database returned row
     * @param EPS_Map $eps_map Current EPS_Map instance
     * 
     * @return Person
     */
    public static function getInstanceByData(array $resArr, EPS_Map $eps_map): Person {
        $instance = new self($resArr['id'], $resArr['name'], $resArr['space_id'], $resArr['department_id']);
        $instance->setEPSMap($eps_map);
        
        return $instance;
    }
}

?>