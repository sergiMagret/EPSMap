<?php

abstract class Translations_Manager {
    public abstract function getInstructionFor(Edge $from, Edge $to, Language $lang);
    public abstract function addInstruction(Edge $from, Edge $to, Language $lang, string $text): bool;
    public abstract function addTranslationFor(Instruction $instruction, Language $lang, string $text): bool;
    public abstract function getTranslationFor(Instruction $instruction, Language $lang);
}

/**
 * An Instruction between Edges, this class only contains the basic info,
 * its unique ID and unique name to easily identify it, to translate an Instruction check 
 * Translations_Manager and Instruction_Translation.
 */
class Instruction extends Basic_Info {
    
    private const table_name = "instructions";

    protected int $_id;
    protected string $_name;

    public function __construct(int $id, string $name){
        $this->_id = $id;
        $this->_name = $name;
    }

    public static function getTableName(): string {
        return self::table_name;
    }

    /**
     * Get an Instruction instance by the Database id
     *
     * @param integer $id The unique ID for the Instruction
     * @param EPS_Map $eps_map Current EPS_Map instance
     * 
     * @return Instruction|null|false The Instruction instance, null if not found or false on error
     */
    public static function getInstance(int $id, EPS_Map $eps_map){
        $db = $eps_map->getDB();
        $logger = $eps_map->error_logger;

        $tablename = self::table_name;

        $queryStr = "SELECT * FROM `$tablename` WHERE id = :id";
        $resArr = $db->getResultArrayPrepared($queryStr, [":id" => $id]);
        if($resArr === false){
            $logger->error($db->getErrorMsg());
            return false;
        }

        if(count($resArr) == 0) return null;

        return self::getInstanceByData($resArr[0], $eps_map);
    }

    /**
     * Get an Instruction instance by a full database row
     *
     * @param array $resArr Database returned row
     * @param EPS_Map $eps_map Current EPS_Map instance
     * 
     * @return Instruction
     */
    public static function getInstanceByData(array $resArr, EPS_Map $eps_map): Instruction {
        $instance = new self($resArr['id'], $resArr['lang_id'], $resArr['text']);
        $instance->setEPSMap($eps_map);

        return $instance;
    }
}

/**
 * An Instruction translated to a certain Language
 */
class Instruction_Translation implements JsonSerializable {

    protected Instruction $_instruction;
    protected Language $_language;
    protected string $_text;

    public function __construct(Instruction $instruction, Language $language, string $text){
        $this->_instruction = $instruction;
        $this->_language = $language;
        $this->_text = $text;
    }

    /**
     * Get the Language this instruction is translated to
     *
     * @param boolean $id Whether to return only the id of the Language or the full object
     * 
     * @return Language|int The Language instance or the Language id depending on $id
     */
    public function getLanguage(bool $id = false){
        if($id === true) return $this->_language->getID();
        else return $this->_language;
    }
    
    /**
     * Get the original Instruction for this translation
     *
     * @param boolean $id Whether to return only the id of the Instruction or the full object
     * 
     * @return Instruction|int The Instruction instance or the Instruction id depending on $id
     */
    public function getInstruction(bool $id = false){
        if($id === true) return $this->_language->getID();
        else return $this->_language;
    }

    /**
     * Get the Instruction text translated to Language
     *
     * @return string
     */
    public function getText(): string {
        return $this->_text;
    }

    public function jsonSerialize(): mixed {
        return [
            "language" => $this->_language->jsonSerialize(),
            "instruction" => $this->_instruction->jsonSerialize(),
            "text" => $this->_text
        ];
    }
}


?>