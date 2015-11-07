<?php
	include("datos.php");
	$bd=new PDO($dsnw, $userw, $passw, $optPDO);
	$id = $_GET["id"];
	try{	
	
		$sql = "DESCRIBE usuario_permisos";
		$res = $bd->query($sql);
	$campos=array();
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $a=>$c){
		$campos[$a]=$c["Field"];
	}
		$sql = "select * from usuario_permisos where id_usuario = $id";
		$res = $bd->query($sql);
		$res = $res->fetchAll(PDO::FETCH_ASSOC);
			$count = 0;
		foreach($res[0] as $v)
		{
			$r[$campos[$count]] = $v;
			$count++;
		}
		echo json_encode($r);
	}catch(PDOException $err){
		echo $err->getMessage();
	}
?>