<?php
	include("datos.php");
	$usuario = $_GET["usuario"];
	$cliente = $_GET["cliente"];
	$clave = $_GET["clave"];
	$salon = $_GET["salon"];
	$nombre = $_GET["nombre"];
	$tipo = $_GET["tipo"];
	$fecha1 = $_GET["fecha1"];
	$fecha2 = $_GET["fecha2"];
	$fecha3 = $_GET["fecha3"];
	$fecha4 = $_GET["fecha4"];
	$dir = $_GET["dir"];
	$inv = $_GET["noInv"];
	$tel = $_GET["tel"];
	$nombrecontacto = $_GET["nombrecontacto"];
	$cargocontacto = $_GET["cargocontacto"];
	
	
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
	 unset($f);
 $f = $fecha4;
 unset($fecha4);
 $f = explode("/",$f);
 $hora = explode(" ",$f[2]);
 $fecha4 = $hora[0] . "-" . $f[1] . "-" . $f[0] . " " . $hora[1];
	
	
	try{
		$bd=new PDO($dsnw, $userw, $passw, $optPDO);
		$sql = "select * from cotizaciones where id_cotizacion = $clave";
		$res = $bd->query($sql);
		$res = $res->fetchAll(PDO::FETCH_ASSOC);
		if(!isset($res[0]))
		{
			$sql = "insert into cotizaciones (clave, id_empresa, id_usuario, id_cliente, salon, nombre, id_tipo, estatus, fechaevento, fechamontaje, fechadesmont,fechafinal, dirEvento, noinvitados, telefonoContacto,nombrecontacto,cargocontacto)
					values($clave, 1, $usuario, $cliente, '$salon', '$nombre', $tipo, 1, '$fecha1', '$fecha2', '$fecha3','$fecha4', '$dir', $inv, '$tel', '$nombrecontacto', '$cargocontacto')";
			$bd->query($sql);
			$r = "La cotizacion se añadio corectamente";
			echo $r;
		}
		else{
			$r = "La cotizacion ya existe";
			echo $r;
		}
	}catch(PDOException $err){
		$r = $err->getMessage();
		echo $r;
	}
?> 