<?php
	include("datos.php");
	$empleado  = $_POST["empleado"];
	$evento  = $_POST["evento"];
	$direccion  = $_POST["direccion"];
	$nombre  = $_POST["nombre"];
	$telefono  = $_POST["telefono"];
	$fecha1  = $_POST["fecha1"];
	$fecha2  = $_POST["fecha2"];
	$fecha3  = $_POST["fecha3"];
	
try{
	
	 $f = $fecha1;
 $f = explode("/",$f);
 $hora = explode(" ",$f[2]);
 //print_r($hora);
 $fecha1 = $hora[0] . "-" . $f[1] . "-" . $f[0] . " " . $hora[1];
 unset($f);
 $f = $fecha2;
 unset($fecha2);
 $f = explode("/",$f);
 $hora = explode(" ",$f[2]);
 $fecha2 = $hora[0] . "-" . $f[1] . "-" . $f[0] . " " . $hora[1];
 //print_r($fecha2);
 unset($f);
 $f = $fecha3;
 unset($fecha3);
 $f = explode("/",$f);
 $hora = explode(" ",$f[2]);
 $fecha3 = $hora[0] . "-" . $f[1] . "-" . $f[0] . " " . $hora[1];
 //print_r($fecha3);
	
	
	
	$bd=new PDO($dsnw, $userw, $passw, $optPDO);
	$sql="select id_evento from eventos where nombre = '$evento'";
	
	$res1 = $bd->query($sql);
		$res1 = $res1->fetchAll(PDO::FETCH_ASSOC);
		
			$id_evento = $res1[0]["id_evento"];
		
	
	
	
	$bd=new PDO($dsnw, $userw, $passw, $optPDO);
	$sql = "insert into gastos_eventos(id_evento, empleado, fecha1, fecha2, fecha3, direccion, nombre, telefono)
				values($id_evento, '$empleado', '$fecha1', '$fecha2', '$fecha3', '$direccion', '$nombre', '$telefono')";
				$r["info"]= $sql;
	$bd->query($sql);
	$r["continuar"] = true;
}catch(PDOException $err){
	echo $err->getMessage();
	$r["continuar"] = false;
}
echo json_encode($r);
?>