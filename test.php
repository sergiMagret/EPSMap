<?php

    // Mida dels identificadors
    // edge_id:             20
    // node_id:             10
    // porta_id:            8
    // za_id:               6
    // espai_id:            6
    // professor_id:        6
    // instruction_id:      5
    // department_id        3
    // tipus_node_id:       3
    // tipus_espai_id:      3
    // edifici_id:          2
    // lang_id:             2 (VARCHAR)

    // TODO Com sempre, no s'han de passar ni retornar mai IDs, només moure els objectes
    // més endevant també s'haurien de fer "caches" per guardar els objectes carregats

    // TODO Si CLI és 5.6 i web és 7.4, el més probable és que els scripts no funcionin pq no entendrà les coses de 7.4.
    // per tant o tirar-ho enrere o fer accés web, efectivament, no es pot

    require_once("AppInit.php");

    echo "<pre>";
    $res = $eps_map->addDoor("test porta!"); /// FUNCIONA!!!
    var_dump($res);
    echo "<br><hr><br>";
    $res = $eps_map->addBuilding("test edifici!");
    var_dump($res);
    echo "</pre>";


?>