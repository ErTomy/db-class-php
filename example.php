<?php

include 'class/db.php';


// inserción
for ($i=0; $i < 20; $i++) {
    db::insert('tabla', array('field1'=>'value'.$i, 'field2'=>'value2_'.$i));
}

// actualización
db::update('tabla', array('field2'=>'actualizado'), 'id = 2');

// borrado
db::delete('tabla', 'id > 5');


// consulta
$resultado = db::query('SELECT * FROM tabla');
 var_dump($resultado);
