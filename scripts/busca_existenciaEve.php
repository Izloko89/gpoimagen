<?php
	
	$art=$_GET["art"];
	$cant=$_GET["cant"];
	$cot=$_GET["cot"];
	include("datos.php");

	try{
		$bd=new PDO($dsnw, $userw, $passw, $optPDO);
		$sql = "select almacen.cantidad, articulos.nombre from almacen
				INNER JOIN articulos ON articulos.id_articulo = almacen.id_articulo
				where almacen.id_articulo = $art";
		$res = $bd->query($sql);
		$res = $res->fetchAll(PDO::FETCH_ASSOC);
		$cantidad =  $res[0]["cantidad"];
		//if($cant > $cantidad)
	//	{
			//$r = "La cantidad total en almacen es de " . $cantidad . " " . $res[0]["nombre"];
			//echo $r;
			//exit;
	//	}
		$sql = "select fechamontaje, fechadesmont from eventos where id_evento = $cot";
		$res = $bd->query($sql);
		$res = $res->fetchAll(PDO::FETCH_ASSOC);
		$fechamontaje = $res[0]["fechamontaje"];
		$fechadesmont = $res[0]["fechadesmont"];
		$sql = "select 'no disponible' as ndisponible, eventos.id_evento, cantidad from eventos
				INNER JOIN eventos_articulos ON eventos_articulos.id_evento =  eventos.id_evento
				where '$fechamontaje' BETWEEN fechamontaje AND fechadesmont AND eventos_articulos.id_articulo = $art ";
		$res = $bd->query($sql);
		$res = $res->fetchAll(PDO::FETCH_ASSOC);
		$sql = "select cantidad from almacen where id_item = $art";
		$res1 = $bd->query($sql);
		$res1 = $res1->fetchAll(PDO::FETCH_ASSOC);
		if(isset($res[0]["ndisponible"]))
		{
			$fianl = $res1[0]["cantidad"] - $res[0]["cantidad"];
			if($fianl < 0)
				$fianl = 0;
			$r = "";
			$r = "Total articulos " . $res1[0]["cantidad"] ."<br>Ocupados el mismo dia " . $res[0]["cantidad"] . "<br>Evento #Folio ".$res[0]["id_evento"]. "<br>Disponibles $fianl";
			echo $r;
			exit;
		}
		else
		{
			$fianl = $res1[0]["cantidad"];
			$r = "";
			$r = "Total articulos " . $res1[0]["cantidad"] ."<br>Ocupados el mismo dia 0 <br>Evento #Folio <br>Disponibles $fianl";
			echo $r;
		}
	}catch(PDOException $err){
		echo $err->getMessage();
	}
?>