<?php

/**
 * Functions used in various actions
 */

$error_json = [
    "http_code" => 500,
    "reason" => ""
];

function endWithErrorCode(int $code, ?string $message=null): void {
    global $error_json;

    http_response_code($code);
    $error_json['http_code'] = $code;
    $error_json['reason'] = $message ?? "";
    
    header("Content-Type: application/json");
    echo json_encode($error_json);
    die();
}

/**
 * End the script with a JSON object
 *
 * @param JsonSerializable|JsonSerializable[] $object The object to encode
 * 
 * @return void
 */
function endWithJSON($object): void {
    http_response_code(200);
    header("Content-Type: application/json");
    echo json_encode($object);
    die();
}

function ensureRequestAllowed(string $method): bool {
    // S'ha de fer algun tipus de comprovació perque no tothom pugui executar aquestes accions
    if(strtolower($_SERVER['REQUEST_METHOD']) != strtolower($method)) endWithErrorCode(403, "Page not available with ".$_SERVER['REQUEST_METHOD']);


    return true;
}

?>