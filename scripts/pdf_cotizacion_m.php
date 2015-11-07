<?php session_start();
setlocale(LC_ALL,"");
setlocale(LC_ALL,"es_MX");
include_once("datos.php");
require_once('../clases/html2pdf.class.php');
include_once("func_form.php");
$emp=$_SESSION["id_empresa"];

if(isset($_GET["id"])){
    $id=$_GET["id"];
}

//funciones para convertir px->mm
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

try{
    $bd=new PDO($dsnw,$userw,$passw,$optPDO);
    // para saber los datos del cliente
    $sql="SELECT
        t1.id_cotizacion,
		t1.nombre as nombreEve,
        t1.fecha,
        t1.fechaevento,
        t1.fechamontaje,
        t1.fechadesmont,
        t1.id_cliente,
		t1.dirEvento,
        t2.nombre,
        t3.direccion,
        t3.colonia,
        t3.ciudad,
        t3.estado,
        t3.cp,
        t3.telefono,
		t4.contacto,
		t1.nombrecontacto,
		t1.noinvitados
    FROM cotizaciones t1
    LEFT JOIN clientes t2 ON t1.id_cliente=t2.id_cliente
    LEFT JOIN clientes_contacto t3 ON t1.id_cliente=t3.id_cliente
	LEFT JOIN clientes_fiscal t4 ON t1.id_cliente=t4.id_cliente
    WHERE id_cotizacion=$id;";
    $res=$bd->query($sql);
    $res=$res->fetchAll(PDO::FETCH_ASSOC);
    $evento=$res[0];
    $cliente=$evento["nombre"];
    $telCliente=$evento["telefono"];
    $domicilio=$evento["direccion"]." ".$evento["colonia"]." ".$evento["ciudad"]." ".$evento["estado"]." ".$evento["cp"];
    $fecha=$evento["fechaevento"];
    $fechaEve=$evento["fechaevento"];
	$nombreEve= $evento["nombre"];
	$contacto = $evento["contacto"];
	$lugar = $evento["dirEvento"];
	$nombrecontacto = $evento["nombrecontacto"];
	$nopersonas = $evento["noinvitados"];
}catch(PDOException $err){
    echo $err->getMessage();
}
$bd=NULL;

//para saber los articulos y paquetes
try{
    $bd=new PDO($dsnw,$userw,$passw,$optPDO);
    $sql="SELECT
        t1.*,
        t2.nombre
    FROM cotizaciones_articulos t1
    LEFT JOIN articulos t2 ON t1.id_articulo=t2.id_articulo
    WHERE t1.id_cotizacion=$id;";
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
}catch(PDOException $err){
    echo $err->getMessage();
}

//var_dump($articulos);
?>
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
.celda_color1
{
    background-color:#FC6;
    color:#000;
}
.celda_color
{
    background-color:#FC9;
    color:#000;
}
</style>
<table style="width:100%;border-bottom:<?php echo pxtomm(2); ?> solid #000;" cellpadding="0" cellspacing="0" >
    <tr>
         <td style="width:40%; text-align:left"><img src="../img/logo.jpg" style="width:150px;"/></td>
         <td style="width:60%; text-align:left; padding-bottom:5px;">
            <div style="width:100%; text-align:right;font-size:24px;"><strong>Presupuesto Autorizado</strong></div>
            <div style="width:100%; text-align:right;font-size:10px;">FOLIO N&ordm; <?php echo folio(4,$id);  ?></div>
         </td>
    </tr>
</table>
<p style="width:100%; text-align:center; margin:5px auto; font-size:12px;">Prado de los Tabachines 130 Fracc. Prados Tepeyac C.P. 45050, Zapopan, Jalisco, México. <br/>
Tel: +52 (33) 3122 8562
<br/> 3121 9467 </p>

<table cellpadding="0" cellspacing="0" border="0.3" style=" font-size:12px;width:100%">
    <tr>
                <td style="width:50%;" class="celda_color">Evento </td>
                <td style="width:50%;"><?php echo $nombreEve;?></td>
          </tr>
        <tr>
        <td style="width:50%;"class="celda_color">Lugar &nbsp;</td>
        <td style="width:50%;">&nbsp;<?php echo $lugar ?></td>
    </tr>
    <tr>
        <td style="width:50%;"class="celda_color">Fecha</td>
        <td style="width:50%;">&nbsp;<?php echo $fecha ?></td>
        </tr>
        <tr>
        <td style="width:50%;"class="celda_color">Contacto</td>
		<td style="width:50%;">&nbsp;<?php echo $nombrecontacto ?></td>
    </tr>
    <tr>
        <td style="width:50%;"class="celda_color">Número de personas</td>
		<td style="width:50%;">&nbsp;<?php echo $nopersonas ?></td>
    </tr>
</table>

<br>
<table border="0.3" cellspacing="-0.5" cellpadding="1" style="width:100%;font-size:10px;margin-top:5px;">
    <tr align="center">
        <th style="width:10%; font-size:13px" class="celda_color">Cant.</th>
        <th style="width:50%;font-size:13px"class="celda_color">Concepto</th>
        <th style="width:10%;font-size:13px"class="celda_color">Días</th>
        <th style="width:15%;font-size:13px"class="celda_color">P/U</th>
        <th style="width:15%;font-size:13px"class="celda_color">Costo</th>
    </tr>
<?php
    $total=0;
    foreach($articulos as $id=>$d){
    $total+=$d["total"];
?>
    <tr>
       <td style="width:10%;text-align:center;"><?php echo $d["cantidad"] ?></td>
        <td style="width:50%;"><?php echo $d["nombre"] ?></td>
        <td style="width:10%;"></td>
        <td style="width:15%;text-align:center;"><?php echo number_format($d["precio"],2) ?></td>
        <td style="width:15%;text-align:center;"><?php echo number_format($d["total"],2) ?></td>
    </tr>
<?php } ?>
    </table>
    <table border="0" cellspacing="0" cellpadding="0" style="width:100%;font-size:10px;margin-top:5px;">
    <tr >
      <td style="width:10%;text-align:center;">&nbsp;</td>
      <td style="width:50%;"></td>
        <td style="width:10%;text-align:center;"></td>
        <td style="width:15%;text-align:right;" class="celda_color"><strong>SUBTOTAL</strong></td>
        <td style="width:15%;text-align:center;" class="celda_color"><?php echo number_format($total,2)?></td>
    </tr>
        <tr>
      <td style="width:10%;text-align:center;">&nbsp;</td>
      <td style="width:50%;"></td>
        <td style="width:10%;text-align:center;"></td>    
        <td style="width:15%;text-align:right;" class="celda_color"><strong>IVA</strong></td>
        <td style="width:15%;text-align:center;" class="celda_color"><?php echo number_format($iva=$total*(0.16),2)?></td>
    </tr>
    <tr>
      <td style="width:10%;text-align:center;">&nbsp;</td>
      <td style="width:50%;"></td>
        <td style="width:10%;text-align:center;"></td>
        <td style="width:15%;text-align:right;" class="celda_color"><strong>TOTAL</strong></td>
        <td style="width:15%;text-align:center;" class="celda_color"><?php echo number_format(($total+$iva),2)?></td>
    </tr>
</table>
<br>
<div style="width:90%; padding:0 20px; font-size:12px; text-align:center">El pago sería, 50% al contratar, el resto liquidarlo en 4 días antes del evento</div>
<table border="0" cellpadding="0" cellspacing="0" style="font-size:13px; width:100%; margin-top:30px; padding:0 20px;">
    <tr>
        <td style="width:100%;vertical-align:top; text-align:center;">
            ATENTAMENTE<br />Lic. Lupita Villaseñor<br />Gerente
        </td>
    </tr>
</table>
<?php
$html=ob_get_clean();
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