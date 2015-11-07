<?php session_start(); 
include("../scripts/funciones.php");
include("../scripts/func_form.php");
include("../scripts/datos.php");
$emp=$_SESSION["id_empresa"];

try{
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	$sql="SELECT
		*
	FROM usuarios
	WHERE id_empresa=$emp;";
	$res=$bd->query($sql);
	$usuarios=array();
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $d){
		$usuarios[$d["id_usuario"]]=$d;
	}
}catch(PDOException $err){
	echo "Error: ".$err->getMessage();
}

?>
<script src="js/formularios.js"></script>
<script>
$(document).ready(function(e) {
    $(".nombre").focusout(function(e) {
		$(".razon").val($(this).val());
    });
	$("form").submit(function(e) {
        e.preventDefault();
    });
	$( ".usuario" ).keyup(function(e){
		_this=$(this);
		//e.keyCode!=8 && _this.val()!=""
		if(e.keyCode==13){
			if(typeof timer=="undefined"){
				timer=setTimeout(function(){
					usuario();
				},300);
			}else{
				clearTimeout(timer);
				timer=setTimeout(function(){
					usuario();
				},300);
			}
		}else if(e.keyCode==8 && _this.val()==""){
			resetform();
		}
    }); //termina buscador de cotizacion
	$(".dbc").dblclick(function(e) {
        accion=$(this).attr("data-action");
		val=$(this).text();
		switch(accion){
			case 'clave':
				$(".usuario").val(val);
				scrollTop();
				usuario();
			break;
		}
    });
	$( ".nombre_buscar" ).autocomplete({
      source: "scripts/busca_usuarios.php",
      minLength: 2,
      select: function( event, ui ) {
		//muestra el botón modificar  
		$(".modificar").show();
		$(".guardar").hide();
		
		//da el nombre del formulario para buscarlo en el DOM
		form=ui.item.form;
		
		//asigna el valor en el campo
		$.each(ui.item,function(i,v){
			selector=form+" ."+i
			$(selector).val(v);
		});
		datosContacto(ui.item.id_cliente,'clientes');
		datosFiscal(ui.item.id_cliente,'clientes');
	  }
    });
	$(".mostrar").click(function(e) {
		ref=$(this).attr("data-c");
		$("."+ref).toggle();
    });
	$("#guardar").click(function(e){
		
		var clave = document.getElementById("clave").value;
		var nombre = document.getElementById("nombre").value;
		var password= document.getElementById("password").value;
		var usuario= document.getElementById("usuario").value;
		var direccion= document.getElementById("direccion").value;
		var colonia= document.getElementById("colonia").value;
		var ciudad= document.getElementById("ciudad").value;
		var estado= document.getElementById("estado").value;
		var cp= document.getElementById("cp").value;
		var telefono= document.getElementById("telefono").value;
		var celular= document.getElementById("celular").value;
		var email= document.getElementById("email").value;
			var cotizaciones= 0;
			var eventos= 0;
			var almacen= 0;
			var compras= 0;
			var bancos= 0;
			var modulos= 0;
			var gastos= 0;
			
		if(document.getElementById("cotizaciones").checked)
			cotizaciones= 1;
		if(document.getElementById("eventos").checked)
			 eventos= 1;
		if(document.getElementById("almacen").checked)
			 almacen= 1;
		if(document.getElementById("compras").checked)
			 compras= 1;
		if(document.getElementById("bancos").checked)
			 bancos= 1;
		if(document.getElementById("modulos").checked)
			 modulos= 1;
		if(document.getElementById("gastos").checked)
			 gastos= 1;
		
	

	$.ajax({
	  url:"scripts/agrega_usuario.php",
	  cache:false,
	  async:false,
	  data:{
		'clave':clave,
		'nombre':nombre,
		'usuario':usuario,
		'password':password,
		'direccion':direccion,
		'colonia':colonia,
		'ciudad':ciudad,
		'estado':estado,
		'cp':cp,
		'telefono':telefono,
		'celular':celular,
		'email':email,
		'cotizaciones':cotizaciones,
		'eventos':eventos,
		'almacen':almacen,
		'compras':compras,
		'bancos':bancos,
		'modulos':modulos,
		'gastos':gastos
	  },
	  success: function(r){
		 alert(r);
			  alerta("info","Usuario agregado exitosamente");
		
	  }
	});
	});
});
</script>
<form id="f_usuarios" class="formularios">
  <h3 class="titulo_form">USUARIO</h3>
  	<input type="hidden" name="id_usuario" class="id_usuario" />
    <div class="campo_form">
    <label class="label_width">Usuario</label>
    <input type="text" name="usuario" id="usuario" class="usuario text_mediano requerido" value="">
    </div>
    <div class="campo_form">
    <label class="label_width">Nombre</label>
    <input type="text" name="nombre" id="nombre" class="nombre text_largo nombre_buscar">
    </div>
    <div class="campo_form">
    <label class="label_width">Contraseña</label>
    <input type="text" name="password" id="password" class="password text_corto">
    </div>
    <input class="boton_dentro" type="reset" value="Limpiar" />
</form>
<form id="f_usuarios_contacto" class="formularios">
  <h3 class="titulo_form">INFORMACIÓN DEL USUARIO <input type="button" class="mostrar" data-c="wrap_hide_1" value="Mostrar/Ocultar" /></h3>
<div class="wrap_hide_1" style="display:none;">
  <input type="hidden" name="id_usuario" class="id_usuario" />
  <input type="hidden" name="id_empresa" value="<?php echo $_SESSION["id_empresa"]; ?>" />
    <div class="campo_form">
        <label class="label_width">CLAVE</label>
        <input type="text" name="clave" id="clave" class="requerido mayuscula clave">
    </div>
    <div class="campo_form">
        <label class="label_width">Dirección</label>
        <input type="text" name="direccion" id="direccion" class="direccion">
    </div>
    <div class="campo_form">
        <label class="label_width">Colonia</label>
        <input type="text" name="colonia" id="colonia" class="colonia">
    </div>
    <div class="campo_form">
        <label class="label_width">Ciudad</label>
        <input type="text" name="ciudad" id="ciudad" class="ciudad">
    </div>
    <div class="campo_form">
        <label class="label_width">Estado</label>
        <input type="text" name="estado" id="estado" class="estado">
    </div>
    <div class="campo_form">
        <label class="label_width">Código Postal</label>
        <input type="text" name="cp" id="cp" class="cp">
    </div>
    <div class="campo_form">
        <label class="label_width">Telefono</label>
        <input type="text" name="telefono" id="telefono" class="telefono">
    </div>
    <div class="campo_form">
        <label class="label_width">Celular</label>
        <input type="text" name="celular" id="celular" class="celular">
    </div>
    <div class="campo_form">
        <label class="label_width">E-mail</label>
        <input type="text" name="email" id="email" class="email">
    </div>
</div>
</form>
<form id="f_usuarios_permisos" class="formularios">
  <h3 class="titulo_form">PERMISOS</h3>
    <input type="hidden" class="id_permiso" name="id_permiso" />
	<div class="formularios" style="border:0;">
      <h3 class="titulo_form"><input type="checkbox" id="admin" name="admin" value="1" /> - Administracion</h3>
    </div>
    <div class="formularios" style="border:0;">
      <h3 class="titulo_form"><input type="checkbox" id="lic" name="lic" value="1" /> - Licitaciones</h3>
    </div>
    <div class="formularios" style="border:0;">
      <h3 class="titulo_form"><input type="checkbox" id="ven" name="ven" value="1" /> - Ventas</h3>
    </div>
	 <div class="formularios" style="border:0;">
      <h3 class="titulo_form"><input type="checkbox" id="gru" name="gru" value="1" /> - Grupos</h3>
    </div>
    <div class="formularios" style="border:0;">
      <h3 class="titulo_form"><input type="checkbox" id="com" name="com" value="1" /> - Compras</h3>
    </div>
    <div class="formularios" style="border:0;">
      <h3 class="titulo_form"><input type="checkbox" id="ope" name="ope" value="1" /> - Operaciones</h3>
    </div>
	 <div class="formularios" style="border:0;">
      <h3 class="titulo_form"><input type="checkbox" id="aud" name="aud" value="1" /> - Auditoria</h3>
    </div>
	 <div class="formularios" style="border:0;">
      <h3 class="titulo_form"><input type="checkbox" id="alm" name="alm" value="1" /> - Almacen</h3>
    </div>
	 <div class="formularios" style="border:0;">
      <h3 class="titulo_form"><input type="checkbox" id="fac" name="fac" value="1" /> - Facturacion</h3>
    </div>
    <div class="formularios" style="border:0;">
      <h3 class="titulo_form"><input type="checkbox" id="modu" name="modu" value="1" /> - Módulos</h3>
    </div>
</form>

    <div align="right">
	
        <input type="button" id="guardar" onclick="" style="padding:10px;" value="GUARDAR" data-wrap="#" onclick="agregar_usr()" data-accion="nuevo" data-m="pivote" />
        <input type="button" class="modificar" value="MODIFICAR" style="display:none;" />
    	<input type="button" class="volver" value="VOLVER">
    </div>
</div>
<div class="formularios">
<h3 class="titulo_form">Listado de usuarios registrados</h3>
	<table style="width:100%;">
    	<tr>
        	<th>USUARIO<br /><font style="font-size:0.4em; color:#999;">Doble Clic<br />para modificar</font></th>
            <th>NOMBRE</th>
            <th>CATEGORÍA</th>
        </tr>
        
    <?php if(count($usuarios)>0){foreach($usuarios as $art=>$d){
		echo '<tr>';
		echo '<td class="dbc" data-action="clave">'.$d["usuario"].'</td>';
		echo '<td>'.$d["nombre"].'</td>';
		echo '<td>'.$d["categoria"].'</td>';
		echo '</tr>';
	}//foreach
	}//if end ?>
    </table>
</div>

<script>
function agregar_usr()
{

	
	
}
</script>

<script>
function usuario(){
	$(".id_usuario").val('');
	dato=$(".usuario").val();
	input=$(".usuario");
	input.addClass("ui-autocomplete-loading");
	$.ajax({
	  url:"scripts/busca_usuarios.php",
	  cache:false,
	  async:false,
	  data:{
		term:dato
	  },
	  success: function(r){
		clave=$(".usuario").val();
		resetform();
		$(".usuario").val(clave);
		$.each(r[0],function(i,v){
			$("."+i).text(v);
			$("."+i).val(v);
		});
		datosContacto(r[0].id_usuario,"usuarios");
		permisos();
		//asigna el id de cotización
		input.removeClass("ui-autocomplete-loading");
	  }
	});
}
function permisos(){
	$(".id_permiso").val('');
	id_usuario=$(".id_usuario").val();
	$.ajax({
	  url:"scripts/s_usuarios_permisos.php",
	  cache:false,
	  async:false,
	  data:{
		'id':id_usuario
	  },
	  success: function(r){
		  if(r){
			  /*
		if(
		if(document.getElementById("lic").checked)
		document.getElementById("ven").checked
		
		
		
		
		
		
		
		*/
	r = JSON.parse(r);
			  if(r.administracion == 1)
			  {
				  document.getElementById("admin").checked = true;
			  }
			  else
			  {
				  document.getElementById("admin").checked = false;
			  }
			  if(r.licitacion == 1)
			  {
				  document.getElementById("lic").checked = true;
			  }
			  else
			  {
				  document.getElementById("lic").checked = false;
			  }
			  // if(r.ventas == 1)
			  // {
				  // document.getElementById("ven").checked = true;
			  // }else
			  // {
				  // document.getElementById("ven").checked = false;
			  // }
			  // if(r.grupos == 1)
			  // {
				  // document.getElementById("gru").checked = true;
			  // }
			  // else
			  // {
				  // document.getElementById("gru").checked = false;
			  // }
			  if(r.compras == 1)
			  {
				  document.getElementById("com").checked = true;
			  }
			  else
			  {
				  document.getElementById("com").checked = false;
			  }
			  // if(r.operaciones == 1)
			  // {
				  // document.getElementById("ope").checked = true;
			  // }
			  // else
			  // {
				  // document.getElementById("ope").checked = false;
			  // }
			  // if(r.auditorias == 1)
			  // {
				  // document.getElementById("aud").checked = true;
			  // }
			  // else
			  // {
				  // document.getElementById("aud").checked = false;
			  // }
			  if(r.almacen == 1)
			  {
				  document.getElementById("alm").checked = true;
			  }
			  else
			  {
				  document.getElementById("alm").checked = false;
			  }
			  // if(r.facturacion == 1)
			  // {
				  // document.getElementById("fac").checked = true;
			  // }
			  // else
			  // {
				  // document.getElementById("fac").checked = false;
			  // }
			  if(r.modulos == 1)
			  {
				  document.getElementById("modu").checked = true;
			  }
			  else
			  {
				  document.getElementById("modu").checked = false;
			  }
		  }else{
			  alerta("info",r.info);
		  }
	  }
	});
}
</script>