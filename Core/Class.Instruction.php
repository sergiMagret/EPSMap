<?php

class Edge_Instructions_Controller {

    protected EPS_Map $_eps_map;

    protected const view_name = "edge_instruction_translation_view";
    protected const edge_instructions_table_name = "edge_instructions";
    protected const instructions_lang_table_name = "instructions_lang";

    /**
     * Create a new Edge_Instructions_Controller instance
     *
     * @param EPS_Map $eps_map
     */
    public function __construct(EPS_Map $eps_map){
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
     * Get an Instruction between edges $from and $to translated to the language $lang
     *
     * @param Edge $from
     * @param Edge $to
     * @param Language $lang
     * 
     * @return Instruction_Translation|null|false The Instruction Translation instance, null if not found or false on error
     */
    public function getInstructionBetween(Edge $from, Edge $to, Language $lang){
        $db = $this->getEPSMap()->getDB();
        $logger = $this->getEPSMap()->error_logger;

        $viewname = self::view_name;

        $queryStr = "SELECT `instruction_id` AS `id`, `instruction_name` AS `name`, `text` FROM `$viewname` WHERE `from_edge_id` = :from_id AND `to_edge_id` = :to_id AND `lang_id` = :lang_id";
        $substitutions = [":from_id" => $from->getID(), ":to_id" => $to->getID(), ":lang_id" => $lang->getID()];
        $resArr = $db->getResultArrayPrepared($queryStr, $substitutions);
        if($resArr === false){
            $logger->error("Error getting instruction translation between edges ".$from->getID()." and ".$to->getID(), ["queryStr" => $queryStr, "substitutions" => $substitutions]);
            $logger->error($db->getErrorMsg());
            return false;
        }

        if(count($resArr) === 0) return null;

        $classname = $this->getEPSMap()->getClassname("instruction");
        $instruction = $classname::getInstanceByData($resArr[0], $this->getEPSMap());
        $ins_trans = new Instruction_Translation($instruction, $lang, $resArr[0]['text']);

        return $ins_trans;
    }

    /**
     * Set the instruction assigned to the transition from $from to $to, if there was already an instruction
     * assigned, the instruction is updated to the new one, if there was NOT any instruction, then it is added
     *
     * @param Edge $from
     * @param Edge $to
     * @param Instruction $instruction The new instruction to set
     * 
     * @return boolean True on success, false on error
     */
    public function setInstructionBetween(Edge $from, Edge $to, Instruction $instruction): bool {
        $db = $this->getEPSMap()->getDB();
        $logger = $this->getEPSMap()->error_logger;

        $tablename = self::edge_instructions_table_name;

        $queryStr = "SELECT * FROM `$tablename` WHERE `from_edge_id` = :from_id AND `to_edge_id` = :to_id";
        $substitutions = [":from_id" => $from->getID(), ":to_id" => $to->getID()];
        $resArr = $db->getResultArrayPrepared($queryStr, $substitutions);
        if($resArr === false){
            $logger->error("Error checking if instruction exists between edges ".$from->getID()." and ".$to->getID(), ["queryStr" => $queryStr, "substitutions" => $substitutions]);
            $logger->error($db->getErrorMsg());
            return false;
        }

        $db->startTransaction();
        
        // Insert or update depending on if there was already an existing instruction assigned to this pair of edges
        if(count($resArr) >= 1) "UPDATE `$tablename` SET `instruction_id` = :ins_id WHERE `from_edge_id` = :from_id AND `to_edge_id` = :to_id";
        else $queryStr = "INSERT INTO `$tablename` (`from_edge_id`, `to_edge_id`, `instruction_id`) VALUES (:from_id, :to_id, :ins_id)";

        $substitutions = [":from_id" => $from->getID(), ":to_id" => $to->getID(), ":ins_id" => $instruction->getID()];
        $res = $db->getResultPrepared($queryStr, $substitutions);
        if($res === false){
            $logger->error("Error ".((count($resArr) > 1) ? "updating" : "adding")." instruction between ".$from->getID()." and ".$to->getID(), ["queryStr" => $queryStr, "substitutions" => $substitutions]);
            $logger->error($db->getErrorMsg());
            $db->rollbackTransaction();
            return false;
        }
        
        $db->commitTransaction();

        return true;
    }

    /**
     * Set the text assigned to the $instruction translated to $lang, if there was already a translation
     * assigned, the text is updated, else is added
     *
     * @param Instruction $instruction
     * @param Language $lang
     * @param string $text The new text to set
     * 
     * @return boolean True on success, false on error
     */
    public function setTranslationFor(Instruction $instruction, Language $lang, string $text): bool {
        $db = $this->getEPSMap()->getDB();
        $logger = $this->getEPSMap()->error_logger;

        $tablename = self::instructions_lang_table_name;

        $queryStr = "SELECT * FROM `$tablename` WHERE `instruction_id` = :ins_id AND `lang_id` = :lang_id";
        $substitutions = [":instruction_id" => $instruction->getID(), ":lang_id" => $lang->getID()];
        $resArr = $db->getResultArrayPrepared($queryStr, $substitutions);
        if($resArr === false){
            $logger->error("Error checking if a translation already exists for instruction ".$instruction->getID()." and ".$lang->getID(), ["queryStr" => $queryStr, "substitutions" => $substitutions]);
            $logger->error($db->getErrorMsg());
            return false;
        }

        $db->startTransaction();
        
        // Insert or update depending on if there was already an existing translation for the instruction and language
        if(count($resArr) >= 1) "UPDATE `$tablename` SET `text` = :new_text WHERE `instruction_id` = :ins_id AND `lang_id` = :lang_id";
        else $queryStr = "INSERT INTO `$tablename` (`instruction_id`, `lang_id`, `text`) VALUES (:ins_id, :lang_id, :new_text)";

        $substitutions = [":ins_id" => $instruction->getID(), ":lang_id" => $lang->getID(), ":new_text" => $text];
        $res = $db->getResultPrepared($queryStr, $substitutions);
        if($res === false){
            $logger->error("Error ".((count($resArr) > 1) ? "updating" : "adding")." translation for instruction ".$instruction->getID()." and ".$lang->getID(), ["queryStr" => $queryStr, "substitutions" => $substitutions]);
            $logger->error($db->getErrorMsg());
            $db->rollbackTransaction();
            return false;
        }
        
        $db->commitTransaction();

        return true;
    }

    /**
     * Get the translation(s) for an $instruction, if given for $lang, if not given, then 
     * returns an array with the available translations.
     *
     * @param Instruction $instruction The instruction to the get the translation from
     * @param Language|null $lang An optional language to filter, if not given all the available translations will be taken
     * 
     * @return Instruction_Translation|Instruction_Translation[]|null|false The only translation 
     * or the list of them according to $lang, null if no translations where found or false on error
     */
    public function getTranslationFor(Instruction $instruction, ?Language $lang=null){
        $db = $this->getEPSMap()->getDB();
        $logger = $this->getEPSMap()->error_logger;

        if($lang == null){
            $classname = $this->getEPSMap()->getClassname("language");
            $tablename_ins_lang = self::instructions_lang_table_name;
            $tablename_lang = $classname::getTableName();

            $queryStr = "SELECT lang.*, ins_lang.`text`
                         FROM `$tablename_ins_lang` ins_lang
                         JOIN `$tablename_lang` lang
                             ON lang.`id` = ins_lang.`lang_id`
                         WHERE ins_lang.`instruction_id` = :ins_id";

            $resArr = $db->getResultArrayPrepared($queryStr, [":ins_id" => $instruction->getID()]);
            if($resArr === false){
                $logger->error("Error getting translations for instruction ".$instruction->getID(), ["queryStr" => $queryStr]);
                $logger->error($db->getErrorMsg());
                return false;
            }

            if(count($resArr) === 0) return null;

            // In this case we have to create the instances of the language for each translation, the $row['text'] does not affect when getting the instance
            // because it will be omitted in the language since the language does not have any attribute with that name
            $ins_trans = [];
            foreach($resArr as $row) $ins_trans[] = new Instruction_Translation($instruction, $classname::getInstanceByData($row, $this->getEPSMap()), $row['text']);

            return $ins_trans;
        }else{
            $tablename = self::instructions_lang_table_name;

            $queryStr = "SELECT `text` FROM `$tablename` WHERE `instruction_id` = :ins_id AND `lang_id` = :lang_id";
            $substitutions = [":ins_id" => $instruction->getID(), ":lang_id" => $lang->getID()];
    
            $resArr = $db->getResultArrayPrepared($queryStr, $substitutions);
            if($resArr === false){
                $logger->error("Error getting translation for instruction ".$instruction->getID()." and ".$lang->getID(), ["queryStr" => $queryStr, "substitutions" => $substitutions]);
                $logger->error($db->getErrorMsg());
                return false;
            }

            if(count($resArr) === 0) return null;
    
            return new Instruction_Translation($instruction, $lang, $resArr[0]['text']);
        }
        
        return false;
    }
}

/**
 * An Instruction between Edges, this class only contains the basic info,
 * its unique ID and unique name to easily identify it, to translate an Instruction 
 * check Instruction_Translation.
 */
class Instruction extends Basic_Info {
    
    private const table_name = "instructions";

    public function __construct(int $id, string $name){
        parent::__construct($id, $name);
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
        $instance = new self($resArr['id'], $resArr['name']);
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

    public function jsonSerialize(): array {
        return [
            "language" => $this->_language->jsonSerialize(),
            "instruction" => $this->_instruction->jsonSerialize(),
            "text" => $this->_text
        ];
    }
}


?>