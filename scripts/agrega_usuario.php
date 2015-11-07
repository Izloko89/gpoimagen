<?php session_start();
header("Content-type: application/json");
$empresaid=$_SESSION["id_empresa"];
include("datos.php");

$usuario = $_GET['usuario'];
$nombre = $_GET['nombre'];
$password = $_GET['password'];
$cotizacion = 0;
$evento = 0;
$almacen = 0;
$compras = 0;
$bancos = 0;
$modulos = 0;
$fichacontrol = 0;


$cotizacion = $_GET['coti'];
$evento = $_GET['even'];
$almacen = $_GET['alma'];
$compras = $_GET['compr'];
$bancos = $_GET['banc'];
$modulos = $_GET['modu'];
$gastos = $_GET['gastos'];
$fichacontrol = $_GET['fichacontrol'];

$clave = $_GET['clave'];
$direccion = $_GET['direccion'];
$colonia = $_GET['colonia'];
$ciudad = $_GET['ciudad'];
$estado = $_GET['estado'];
$cp = $_GET['cp'];
$telefono = $_GET['telefono'];
$celular = $_GET['celular'];
$email = $_GET['email'];



	$bd=new PDO($dsnw, $userw, $passw, $optPDO);


try{	
	$bd->query("insert into usuarios (usuario,password,nombre,categoria) values ('$usuario','$password','$nombre','administrador')");
	
	$res = $bd->query("SELECT MAX(id_usuario) as id FROM usuarios");
	$adidi = $res->fetchAll(PDO::FETCH_ASSOC);
	
	$id = $adidi[0]["id"];

	$sql = "insert into usuario_permisos (id_usuario,cotizacion,evento,almacen,compras,bancos,modulos,gastos,fichacontrol) values
	($id,$cotizacion,$evento,$almacen,$compras,$bancos,$modulos, $gastos, $fichacontrol)";
	$bd->query($sql);
	
	
	
	$sql = "insert into usuarios_contacto (id_usuario,clave,direccion,colonia,ciudad,estado,cp,telefono,celular,email)values($id,'$clave','$direccion','$colonia','$ciudad','$estado','$cp','$telefono','$celular','$email')";
	$bd->query($sql);
	
	echo true;
}catch(PDOException $err){
	echo $err->getMessage();
	echo false;
}
?>