<?php session_start();
setlocale(LC_ALL,"");
setlocale(LC_ALL,"es_MX");
include_once("datos.php");
require_once('../clases/html2pdf.class.php');
include_once("func_form.php");
$emp=$_SESSION["id_empresa"];

//funciones para usarse dentro de los pdfs
function mmtopx($d){
	$fc=96/25.4;
	$n=$d*$fc;
	return $n."px";
}
function pxtomm($d){
	$fc=96/25.4;
	$n=$d/$fc;
	return $n."mm";
}
function checkmark(){
	$url="http://".$_SERVER["HTTP_HOST"]."/img/checkmark.png";
	$s='<img src="'.$url.'" style="height:10px;" />';
	return $s;
}
function folio($digitos,$folio){
	$usado=strlen($folio);
	$salida="";
	for($i=0;$i<($digitos-$usado);$i++){
		$salida.="0";
	}
	$salida.=$folio;
	return $salida;
}
//tamaño carta alto:279.4 ancho:215.9
$heightCarta=960;
$widthCarta=660;
$celdas=12;
$widthCell=$widthCarta/$celdas;
$mmCartaH=pxtomm($heightCarta);
$mmCartaW=pxtomm($widthCarta);
ob_start();

//sacar los datos del cliente
$error="";
if(isset($_GET["id_evento"])){
	$obs=$_GET["obs"];
	$eve=$_GET["id_evento"];
	try{
		$bd=new PDO($dsnw,$userw,$passw,$optPDO);
		// para saber los datos del cliente
		$sql="SELECT
			t1.id_evento,
			t1.nombre AS nombreEvento,
			t1.fechaevento,
			t1.fechamontaje,
			t1.fechadesmont,
			t1.id_cliente,
			t2.nombre,
			t3.direccion,
			t3.colonia,
			t3.ciudad,
			t3.estado,
			t3.cp,
			t3.telefono
		FROM eventos t1
		LEFT JOIN clientes t2 ON t1.id_cliente=t2.id_cliente
		LEFT JOIN clientes_contacto t3 ON t1.id_cliente=t3.id_cliente
		WHERE id_evento=$eve;";
		$res=$bd->query($sql);
		$res=$res->fetchAll(PDO::FETCH_ASSOC);
		$evento=$res[0];
		$cliente=$evento["nombre"];
		$telCliente=$evento["telefono"];
		$nombreEve=$evento["nombreEvento"];
		$domicilio=$evento["direccion"]." ".$evento["colonia"]." ".$evento["ciudad"]." ".$evento["estado"]." ".$evento["cp"];
		$fechaEve=$evento["fechaevento"];

		//para saber los articulos y paquetes
		$sql="SELECT
			t1.*,
			t2.nombre,
			t2.descripcion
		FROM eventos_articulos t1
		LEFT JOIN articulos t2 ON t1.id_articulo=t2.id_articulo
		WHERE t1.id_evento=$eve;";
		$res=$bd->query($sql);
		$articulos=array();
		foreach($res->fetchAll(PDO::FETCH_ASSOC) as $d){
			if($d["id_articulo"]!=""){
				$art=$d["id_item"];
				unset($d["id_item"]);
				$articulos[$art]=$d;
			}else{
				$art=$d["id_item"];
				unset($d["id_item"]);
				$articulos[$art]=$d;
				$paq=$d["id_paquete"];

				//nombre del paquete
				$sql="SELECT nombre FROM paquetes WHERE id_paquete=$paq;";
				$res3=$bd->query($sql);
				$res3=$res3->fetchAll(PDO::FETCH_ASSOC);
				$articulos[$art]["nombre"]="PAQ. ".$res3[0]["nombre"];

				$sql="SELECT
					t1.cantidad,
					t2.nombre,
					t2.descripcion
				FROM paquetes_articulos t1
				INNER JOIN articulos t2 ON t1.id_articulo=t2.id_articulo
				WHERE id_paquete=$paq AND t2.perece=0;";
				$res2=$bd->query($sql);

				foreach($res2->fetchAll(PDO::FETCH_ASSOC) as $dd){
					$dd["precio"]="";
					$dd["total"]="";
					$dd["nombre"]=$dd["cantidad"]." ".$dd["nombre"];
					$dd["cantidad"]="";
					$articulos[]=$dd;
				}
			}
		}
		//para saber el anticipo
		$emp_eve=$emp."_".$eve;
		$sql="SELECT SUM(cantidad) as pagado FROM eventos_pagos WHERE id_evento='$emp_eve';";
		$res=$bd->query($sql);
		$res=$res->fetchAll(PDO::FETCH_ASSOC);
		$pagado=$res[0]["pagado"];
		
		$sql1 = "select salones.nombre, salones.direccion from salones inner join eventos on eventos.salon = salones.nombre where eventos.id_evento = $eve";
		$res1 = $bd->query($sql1);
		$res1 = $res1->fetchAll(PDO::FETCH_ASSOC);
		$salon = $res1[0];
	}catch(PDOException $err){
		$error= $err->getMessage();
	}
}

?>
<?php if($error==""){ $html='
<page backbottom="15px">
<page_footer> 
<table border="0" cellpadding="0" cellspacing="0" style="font-size:13px; width:100%; margin-top:30px; padding:0 20px;">
	<tr>
		<td style="width:100%;vertical-align:top; text-align:center;">
			<p style="width:100%; text-align:center; margin:5px auto; font-size:10px;">Oficina en Eulogio Parra # 2714 Col. Providencia. Guadalajara, Jalisco, México. Tel: 52 (33) 3642 0913,
3642 0904, 3832 5933 </p>
        </td>
    </tr>
</table>
</page_footer>
<style>
span{
	display:inline-block;
	padding:10px;
}
h1{
	font-size:20px;
}
.spacer{
	display:inline-block;
	height:1px;
}
td{
	background-color:#FFF;
}
th{
	color:#FFF;
	text-align:center;
}
.celda_color1
{
    background-color:#BDBDBD;
    color:#000;
}
.celda_color
{
    background-color:#FC9;
    color:#000;
}
</style>
<table style="width:100%;" cellpadding="0" cellspacing="0" >
    <tr>
	  <td valign="top" style="width:15%; text-align:left;">.</td>
      <td valign="top" style="width:70%; text-align:center; font-size:10px;"><img src="../img/logo.png" style="width:50%;"/></td>
      <td valign="top" style="width:15%; text-align:left;">
    <div style="width:100%; background-color:#E1E1E1; font-weight:bold; text-align:center; padding-top:5px; padding-bottom:5px;">No. Control</div>
            <div style="width:100%; color:#C00; text-align:center;">'.folio(5,$eve).'</div>
         </td>
    </tr>
    <tr>
    <td colspan="3" style="text-align:center; font-size:14px;" class="celda_color"><strong>FICHA DE CONTROL DE EVENTOS</strong></td>
    </tr>
</table>
<table style="width:100%; margin-top:5px;">
  <tr>
    <td style="width:20%; font-size:12px;"><strong>Categoría:</strong></td>
    <td style="width:30%;font-size:12px;">PRODUCCION</td>
  </tr>
  <tr>
    <td style="width:20%;font-size:12px;">&nbsp;</td>
    <td style="width:30%;font-size:12px;">&nbsp;</td>
	<td style="width:20%;font-size:12px;"><strong>STATUS</strong></td>
	<td style="width:30%;font-size:12px;border-bottom:0.3px solid #000;">TENTATIVO</td>
  </tr>
</table>
<table style="width:100%;">
<tr>
  <td style="width:20%;font-size:12px; "><strong>EVENTO:</strong></td>
  <td style="width:30%;font-size:12px; border-bottom:0.3px solid #000;">'.$nombreEve.'</td>
</tr>
  <tr>
    <td style="width:20%; font-size:12px;"><strong>FECHA:</strong></td>
    <td style="width:30%;font-size:12px; border-bottom:0.3px solid #000;">'.varFechaAbr($fechaEve).'</td>
    </tr>
    <tr>
    <td style="width:20%;font-size:12px;"><strong>LUGAR:</strong></td>
    <td style="width:30%;font-size:12x; border-bottom:0.3px solid #000;">'.$salon["nombre"].'</td>
  </tr>
  <tr>
    <td style="width:20%;font-size:12px;"><strong>CONTACTO:</strong></td>
    <td style="width:30%;font-size:12px;border-bottom:0.3px solid #000;"></td>
    </tr>
  <tr>
	<td style="width:20%;font-size:12px;"><strong>EMPRESA:</strong></td>
	<td style="width:30%;font-size:12px;border-bottom:0.3px solid #000;"></td>
  </tr>
   <tr>
    <td style="width:20%; font-size:12px;"><strong>CARGO:</strong></td>
    <td style="width:30%;font-size:12px;border-bottom:0.3px solid #000;"></td>
    </tr>
   <tr>
    <td style="width:20%;font-size:12px;"><strong>TELEFONOS:</strong></td>
    <td style="width:30%;font-size:12x;border-bottom:0.3px solid #000;"></td>
  </tr>
  <tr>
    <td style="width:20%;font-size:12px;"><strong>DIRECCION:</strong></td>
    <td style="width:30%;font-size:12px;border-bottom:0.3px solid #000;"></td>
  </tr>
</table>
<br/>

<table cellpadding="0" cellspacing="0" style="width:100%;" border="0.3">
<tr>
      <td style="width:50%; text-align:left;font-size:12px;"><strong>RESPONSABLE EN DAR SEGUIMIENTO</strong></td>
      <td style="width:50%; text-align:left;font-size:10px;"></td>
    </tr>
    <tr>
      <td style="width:50%; text-align:left;font-size:12px;"><strong>FECHA SOLICITUD DE SERVICIO</strong></td>
      <td style="width:50%;text-aling:left;font-size:10px;"></td>
    </tr>
    <tr>
      <td style="width:50%; text-align:left;font-size:12px;"><strong>FECHA ENVIO DE INFORMACION</strong></td>
      <td style="width:50%;text-aling:left;font-size:10px;"></td>
    </tr>
    <tr>
      <td style="width:50%; text-align:left;font-size:12px;">&nbsp;</td>
      <td style="width:50%; text-aling:left;font-size:10px;">&nbsp;</td>
    </tr>
    <tr>
      <td style="width:50%; text-align:left;font-size:12px;"><strong>SERVICIO QUE SOLICITAN DE PROCESA:</strong></td>
      <td style="width:50%;text-aling:left;font-size:10px;"><u></u></td>
    </tr>
</table>
<table border="0.1" cellspacing="0.8" style="width:100%;background-color:#000;font-size:10px;margin-top:2px;">
	<tr>
    <th style="width:15%;">CODIGO</th>
    <th style="width:40%;">DESCRIPCION DE MOBILIARIO</th>
    	<th style="width:15%;">CANT.</th>
        <th style="width:15%;">COLOR</th>
    </tr>';
	$total=0;
	foreach($articulos as $id=>$d){
	$total+=$d["total"];
	$html .= '<tr>
        <td style="width:15%;text-align:center;"></td>
        <td style="width:40%;text-align:justify;">'.$d["descripcion"].'</td>
        <td style="width:15%;text-align:center;">'.$d["cantidad"].'</td>
        <td style="width:15%;"></td>
    </tr>';
	}
	$html .= '</table>
<table style="width:100%; margin-top:5px;">
<tr>
      <td style="width:50%; text-align:left;font-size:12px;">SERVICIO DE ALIMENTOS:</td>
      <td style="width:50%; text-align:left;font-size:10px;">&nbsp;</td>
    </tr>
    <tr>
      <td style="width:50%; text-align:left;font-size:12px;">OBSERVACIONES:</td>
      <td style="width:50%;text-aling:left;font-size:10px;">&nbsp;</td>
    </tr>
    <tr>
      <td style="width:50%; text-align:left;font-size:12px;">FECHA ENVIO DE INFORMACION</td>
      <td style="width:50%;text-aling:left;font-size:10px;">&nbsp;</td>
    </tr>
   
</table>
<table style="width:100%; margin-top:5px;">
<tr>
      <td style="width:50%; text-align:left;font-size:12px;">FICHA / COTIZACION ALMACENADA EN:</td>
      <td style="width:50%; text-align:left;font-size:10px;">&nbsp;</td>
    </tr>
</table>
<table style="width:100%; margin-top:5px;">
<tr>
      <td class="celda_color1" style="width:50%; text-align:left;font-size:12px;">COSTO REAL DEL SERVICIO</td>
      <td class="celda_color1" style="width:50%; text-align:left;font-size:10px;">VENDEDOR</td>
    </tr>    
</table>
<br/>
<table style="width:100%; margin-top:5px;">
<tr>
      <td class="celda_color1" style="width:33%; text-align:left;font-size:12px;">PRECIO AL CLIENTE</td>
      <td class="celda_color1" style="width:33%; text-align:left;font-size:10px;">COMISION VENDEDOR</td>
      <td class="celda_color1" style="width:33%; text-align:left;font-size:10px;">UTILIDAD</td>
    </tr>
    <tr>
      <td style="width:33%; text-align:left;font-size:12px;">&nbsp;</td>
      <td style="width:33%; text-align:left;font-size:10px;">&nbsp;</td>
      <td style="width:33%; text-align:left;font-size:10px;">&nbsp;</td>
    </tr>
</table>
</page>';
}else{
	echo $error;
}
//$html=ob_get_clean();
$path='../docs/';
$filename="generador.pdf";
//$filename=$_POST["nombre"].".pdf";

//configurar la pagina
//$orientar=$_POST["orientar"];
$orientar="portrait";

$topdf=new HTML2PDF($orientar,array($mmCartaW,$mmCartaH),'es');
$topdf->writeHTML($html);
$topdf->Output();
//$path.$filename,'F'

//echo "http://".$_SERVER['HTTP_HOST']."/docs/".$filename;

?>