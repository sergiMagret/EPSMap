<?php

$formatter = new Monolog\Formatter\LineFormatter(null, "d/m/Y h:i:s", false, true);
$processor = new Monolog\Processor\IntrospectionProcessor();


// Create and set the error logger
$handler = new Monolog\Handler\StreamHandler(LOG_ERROR_FILE, Monolog\Logger::ERROR);
$handler->setFormatter($formatter);

$error_logger = new Monolog\Logger('error', [$handler], [$processor]);


// Create and set the debug logger
$handler = new Monolog\Handler\StreamHandler(LOG_DEBUG_FILE);
$handler->setFormatter($formatter);

$debug_logger = new Monolog\Logger('debug', [$handler], [$processor]);


?>