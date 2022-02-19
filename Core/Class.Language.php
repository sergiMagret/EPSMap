<?php

/**
 * Class to represent an Language in the database.
 * An Language has a name and can have many people associated with it
 */
class Language extends Basic_Info {

    private const table_name = "languages";

    /** Short name identifying the language (2 characters), e.g. "es", "en", "ca", "ru", "pt", "fr", etc
     * In this case there is no setter because once you have created a language, you cannot change the short_name for it
     * @var string */
    protected string $_short_name;

    /**
     * Create a new Language instance
     *
     * @param integer $id Unique database ID for the Language
     * @param string $short_name Short name identifying the language (2 characters)
     * @param string $name Name for the Language
     */
    public function __construct(int $id, string $short_name, string $name){
        parent::__construct($id, $name);
        $this->_short_name = $short_name;
    }

    /**
     * Short name identifying the language (2 characters)
     *
     * @return string
     */
    public function getShortName(): string {
        return $this->_short_name;
    }

    public static function getTableName(): string {
        return self::table_name;
    }

    /**
     * Get a Language instance by the Database id
     *
     * @param integer $id The unique ID for the Language
     * @param EPS_Map $eps_map Current EPS_Map instance
     * 
     * @return Language|null|false The Language instance, null if not found or false on error
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
     * Get a Language instance by a full database row
     *
     * @param array $resArr Database returned row
     * @param EPS_Map $eps_map Current EPS_Map instance
     * 
     * @return Language
     */
    public static function getInstanceByData(array $resArr, EPS_Map $eps_map): Language {
        $instance = new self($resArr['id'], $resArr['short_name'], $resArr['name']);
        $instance->setEPSMap($eps_map);

        return $instance;
    }
}

?>