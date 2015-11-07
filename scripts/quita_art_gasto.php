<?php //script para eliminar articulos desde la tabla de articulos
include("datos.php");
header("Content-type: application/json");
$id_item=$_POST["id_item"];
try{
	$bd=new PDO($dsnw, $userw, $passw, $optPDO);
	$sql="delete from gastos_art where id_gasto = $id_item;";
	$res = $bd->query($sql);

	$r["codigo"]=$sql;
	$r["continuar"]=true;
}catch(PDOException $err){
	$r["continuar"]=false;
	$r["info"]="Error encontrado: ".$err->getMessage();
}

echo json_encode($r);
?>