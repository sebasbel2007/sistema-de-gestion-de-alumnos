<?php
$conexion = new mysqli("localhost","root","","sistema_escuela");
if ($conexion->connect_error) die("Error DB: ".$conexion->connect_error);
$res = $conexion->query("SHOW COLUMNS FROM alumnos");
$cols = [];
while($r = $res->fetch_assoc()) $cols[] = $r['Field'];
echo "<pre>".print_r($cols, true)."</pre>";
