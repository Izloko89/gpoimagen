<?php session_start();
include("datos.php");
include("func_form.php");
$eve=$_POST["id_cliente"];

try{
	$sql=	"SELECT DISTINCT id_evento
			FROM eventos_pagos
			WHERE id_cliente =$eve";
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	$eventos=$bd->query($sql);
	
	$string_return = "";
	
	$tabla="<center><table class=table>";
	
	foreach($eventos->fetchAll(PDO::FETCH_ASSOC) as $evento){
		$id_evento=$evento["id_evento"];
		
		$rest = substr($id_evento, -1);
		
		$sql=	"SELECT DISTINCT eventos_pagos.fecha, eventos_pagos.cantidad, eventos_pagos.id_pago
				FROM eventos_pagos
				WHERE eventos_pagos.id_cliente = $eve AND eventos_pagos.id_evento = '$id_evento'";
				
		$bd=new PDO($dsnw,$userw,$passw,$optPDO);
		
		$res=$bd->query($sql);
		
		$sql=	"SELECT DISTINCT nombre
				FROM eventos
				WHERE id_evento = $rest";
				
		$bd=new PDO($dsnw,$userw,$passw,$optPDO);
		
		$result=$bd->query($sql);
		
		$result=$result->fetchAll(PDO::FETCH_ASSOC);
		$nombre_evento=$result[0]["nombre"];
		
		$tabla.="<tr><th>$nombre_evento</th></tr>";
		$tabla.='<tr>
			<td style="padding-left: 20px;padding-right: 20px;">Fecha</td>
			<td style="padding-left: 20px;padding-right: 20px;">Evento</td>
			<td style="padding-left: 20px;padding-right: 20px;">Ingreso</td>
			<td style="padding-left: 20px;padding-right: 50px;">Saldo</td>
        </tr>';
		$id=1;
		$total=0;
		foreach($res->fetchAll(PDO::FETCH_ASSOC) as $d){
			$total = $total + $d["cantidad"];
			$tabla.='<tr>';
			$tabla.="<td>".varFechaExtensa($d["fecha"]).'</td>';
			$tabla.="<td>".$nombre_evento.'</td>';
			$tabla.='<td>'.$d["cantidad"].'</td>';
			if($total<0){
				$tabla.='<td><font color="red">' . $total . '</font></td>';
			} else{
				$tabla.="<td>".$total."</td>";
			}
			$tabla.='</tr>';
			$id++;
			$total+=$d["cantidad"];
		}
		//$tabla.='<tr><td></td><td style="text-align:right;">Total=</td><td>'.$total.'</td></tr>';
		//$tabla.="</table></center>";
		
		$string_return.=$tabla;
	}
		$tabla.="</table></center>";
	echo $string_return;
}catch(PDOException $err){
	echo "Error: ".$err->getMessage();
}
?>