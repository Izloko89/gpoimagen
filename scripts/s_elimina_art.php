<?php 
include("datos.php");

$bd=new PDO($dsnw,$userw,$passw,$optPDO);
$id_art=$_POST["aidi"];


try{
	$sql= "delete from articulos where id_articulo = $id_art";
			$bd->query($sql);
			$r["continuar"] = true;
		}
		catch(PDOException $err)
		{
			$r["continuar"]=false;
			$r["info"]="Error: ".$err->getMessage();
		}
		
	echo json_encode($r);
?>