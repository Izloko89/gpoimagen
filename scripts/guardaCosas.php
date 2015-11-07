<?php
	include("datos.php");
	$dir = $_POST["dir"];
	$noInv = $_POST["noInv"];
	$tel = $_POST["tel"];
	try{
		$bd=new PDO($dsnw,$userw,$passw,$optPDO);
		$sql ="select MAX(id_cotizacion) from cotizaciones";
		$res = $bd->query($sql);
		$res = $res->fetchAll(PDO::FETCH_ASSOC);
		$id = $res[0]["id_cotizacion"];
		$sql ="update cotizaciones where id_cotizacion = $id";
		$bd->query($sql);
	}catch(PDOException $err){
		$r["continuar"]=false;
		$r["info"]="Error: ".$err->getMessage()." <br />";
	}
?>