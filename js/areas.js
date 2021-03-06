// JavaScript Document
$(document).ready(function(e) {
    //busca cliente
	$( ".nombre" ).autocomplete({
      source: "scripts/busca_area.php",
      minLength: 1,
      select: function( event, ui ) {
		//asignacion individual alos campos
		$("#f_tipo_evento .id_tipo").val(ui.item.id_tipo);
		$(".modificar").show();
		$(".guardar_individual").hide();
	  }
    });
	$(".nombre").keyup(function(e) {
        if(e.keyCode==8){
			if($(this).val()==""){
				$(".modificar").hide();
				$(".guardar_individual").show();
			}
		}
    });
});
	function eliminar_art(elemento, id_item){
		$.ajax({
			url:'scripts/eTipo_area.php',
			cache:false,
			type:'POST',
			data:{
				'id_item':id_item
			},
			success: function(r){
			  if(r){
				document.getElementById("tableEve").deleteRow(elemento);
				alerta("info","<strong>Tipo de Area</strong> Eliminado");
			  }else{
				alerta("error", r);
			  }
			}
		});
	}
	
	
	function guardar_area(){
	var id_item  = document.getElementById("nombre").value;
	
		$.ajax({
			url:'scripts/guarda_area.php',
			cache:false,
			type:'POST',
			data:{
				'nombre':id_item
			},
			success: function(r){
			  if(r){
				document.getElementById("tableEve").deleteRow(elemento);
				alerta("info","<strong>Tipo de Area</strong> Agregado correctamente");
			  }else{
				alerta("error", r);
			  }
			}
		});
	}