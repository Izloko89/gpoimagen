<?php 
include("datos.php");
	unset($r);
	$nombre = $_POST["nombre"];	
	$bd=new PDO($dsnw, $userw, $passw, $optPDO);
	try
	{
		$sqlAfs="insert into areas (id_empresa,clave,nombre)values(1,'$nombre','$nombre');";
		$res=$bd->query($sqlAfs);
		echo true;
	}
		catch(PDOException $err){
		echo false;
	}
echo json_encode($r);
?>