<?php

/**
 * Class to extend when you want your new class to have to loggers of the application,
 * you should construct this with the loggers or set them on creating the extended class.
 */
class Logging {
    /** The logger to use when an error happens
     * @var Monolog\Logger|null */
    public ?Monolog\Logger $error_logger;
    
    /** The logger to use for debugging propuses
     * @var Monolog\Logger|null */
    public ?Monolog\Logger $debug_logger;


    /**
     * Create a new Logging class with the corresponding loggers
     * The array should contain the following keys with the corresponding loggers:
     * - "error" -> The logger to use when an error happens
     * - "debug" -> The logger to use for debugging propuses
     * 
     * @param Monolog\Logger[] $loggers
     */
    public function __construct(array $loggers = []) {
        $this->error_logger = $loggers['error'] ?? null;
        $this->debug_logger = $loggers['debug'] ?? null;
    }
    
    /**
     * Set the necessary loggers.
     * The array must contain the following keys with the corresponding loggers:
     * - "error"
     * - "debug"
     *
     * @param Monolog\Logger[] $loggers
     * 
     * @return void
     */
    public function setLoggers(array $loggers): void {
        $this->error_logger = $loggers['error'] ?? null;
        $this->debug_logger = $loggers['debug'] ?? null;
    }
}

?>