<?php session_start();
header("Content-type: application/json");
include("datos.php");


$term=$_GET["term"];

try{
	$bd=new PDO($dsnw, $userw, $passw, $optPDO);
	//sacar los campos para acerlo más autoámtico	
	$sqlTipo="SELECT 
		nombre as label,
		id_area
	FROM areas
	WHERE id_empresa=1 AND nombre LIKE '%$term%';";
	
	$res=$bd->query($sqlTipo);
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $i=>$v){
		$r[$i]=$v;
	}
	
}catch(PDOException $err){
	echo $err->getMessage();
}

echo json_encode($r);
?>