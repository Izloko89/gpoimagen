// JavaScript Document

	function eliminar(id){
		$.ajax({
			url:'scripts/eArticulo.php',
			cache:false,
			type:'POST',
			data:{
				'id_item':id
			},
			success: function(r){
			  if(r){
				document.getElementById("tableEve").deleteRow(elemento);
				alerta("info","<strong>Articulo</strong> Eliminado");
			  }else{
				alerta("error", r);
			  }
			}
		});
	}