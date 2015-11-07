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
		$nombreEve=$evento["nombreEvento"];
		$cliente=$evento["nombre"];
		$telCliente=$evento["telefono"];
		$domicilio=$evento["direccion"]." ".$evento["colonia"]." ".$evento["ciudad"]." ".$evento["estado"]." ".$evento["cp"];
		
		//Datos del proveedor
		
		
		$sql = "select fecha from eventos_pagos where id_evento = '1_$eve'";
		$rse = $bd->query($sql);
		$rse = $rse->fetchAll(PDO::FETCH_ASSOC);
		$eve1 = $rse[0];
		$fechaEve=$eve1["fecha"];

		//para saber los articulos y paquetes
		$sql="SELECT
			t1.*,
			t2.nombre
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
					t2.nombre
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
	}catch(PDOException $err){
		$error= $err->getMessage();
	}
}

?>
<?php if($error==""){
	 $html='
<page backbottom="15px">
<page_footer> 
		<table border="0" cellpadding="0" cellspacing="0" style="font-size:13px; width:100%; margin-top:30px; padding:0 20px;">
			<tr>
				<td style="width:100%;vertical-align:top; text-align:center; border-top:'.pxtomm(2).' solid #484848;">
					<p style="width:100%; text-align:center; margin:5px auto; font-size:10px; color:#484848">Eulogio Parra 2714 Providencia. ID Nextel 52*168895*1/52*148605*1	Tel / fax 3642-0913/ 04<br/>
						www.bariconcept.net</p>
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
<table style="width:100%" cellpadding="0" cellspacing="0" >
    <tr>
	  <td valign="top" style="width:15%; text-align:left;">.</td>
      <td valign="top" style="width:70%; text-align:center; font-size:10px;"><img src="../img/logo2.jpg" style="width:50%;" />
      </td>
      <td valign="top" style="width:15%; text-align:left;">

        <div style="width:100%; color:#C00; text-align:center;"></div>
         </td>
    </tr>
</table>
<br/><br/>
<div class= "celda_color" style="width:100%; text-align:center; margin:5px auto; font-size:15px;"><strong>ORDEN DE EVENTO</strong></div>

<br/>
<table style="width:100%; margin-top:2px;">
<tr>
<td valign="top" style="width:75%;">
    <table cellpadding="0" cellspacing="0" style=" font-size:10px;width:100%; border:1px solid #000; border-radius:4px;">
        <tr>
            <td style="width:25%;"><strong>EVENTO</strong></td>
            <td style="width:25%;">'.$nombreEve.'</td>
            <td style="width:25%;"><strong>PROVEEDOR</strong></td>
            <td style="width:25%;"></td>
        </tr><tr>
            <td style="width:15%;"><strong>CLIENTE</strong></td>
            <td style="width:25%;">'.$cliente.'</td>
			<td style="width:15%;"><strong>RESPONSABLE</strong></td>
            <td style="width:25%;"></td>
        </tr>
    </table>
</td>
<td valign="top" style="width:30%;">
	<table cellpadding="0" cellspacing="0.2" style="text-align:center;font-size:10px;width:100%;">
        <tr>
            <td style="width:30%;">FECHA:</td>
            <td style="width:30%;">'.$fechaEve.'</td>
		</tr>
		<tr>	
            <td style="width:30%;">CONTACTO:</td>
			<td style="width:30%;">'.$cliente.'</td>
        </tr>
        <tr>
            <td style="width:30%;">TELEFONO: </td>
			<td style="width:30%;">'.$telCliente.'</td>
        </tr>
		<tr>
            <td style="width:30%;">E MAIL: </td>
			<td style="width:30%;"></td>
        </tr>
    </table>
</td></tr>
</table>



<div style="border: 0pt;">
  <table border="0" align="center" style="width:65%;">
      <tr align="center">
    	<th style="width:15%;color:#000; font-size:10px;">CANT.</th>
        <th style="width:70%;color:#000;font-size:10px;text-align:left;">CONCEPTO</th>
        <th style="width:15%;color:#000;font-size:10px;">IMPORTE</th>
    </tr>';
	$total=0;
	foreach($articulos as $id=>$d){
	$total+=$d["total"];
	$html .= '
    <tr>
        <td style="width:15%;text-align:center;font-size:10px;">'.$d["cantidad"].'</td>
        <td style="width:70%;font-size:10px;">'.$d["nombre"].'</td>
        <td style="width:15%;text-align:right;font-size:10px;">'.number_format($d["total"],2).'</td>
    </tr>';
	}
	$html .= '<tr>
        <td style="width:15%;text-align:center;font-size:10px;"> </td>
        <td style="width:70%;text-align:right;font-size:10px;"><strong>Total:</strong></td>
        <td style="width:15%;text-align:right;font-size:10px;">'.number_format($total,2).'</td>
    </tr>
  </table>
</div>
<br/>
<br/>

<table border="0" cellpadding="0" cellspacing="0" style="font-size:11px; width:100%; margin-top:5px;">
  <tr>
    <td style="width:50%;vertical-align:top; text-align:center;">
    Contratante
    <br/>
    <br/>
    <br/>____________________________<br />
	'.$cliente.'
      <br /></td>
    <td style="width:50%;vertical-align:top; text-align:center;">
     Contratado
    <br/>
    <br/>
    <br/>____________________________<br />NOMBRE DEL USUARIO</td>
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