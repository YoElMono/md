var actionForm = 0;
var typeEdit = 0;
var tableGrid = null;

if(ajaxType){
	actionForm = 1;
}

function Grid(){
  $('[data-ride="datatables"]').each(function() {
    tableGrid = $(this).dataTable( {
      "initComplete": function(){
      	setTimeout(actionDelete , 500);
      	console.log("Termino");
      },
      "ajax" : "negocios/get/",
      "sDom": "<'row'<'col-sm-6'l><'col-sm-6'f>r>t<'row'<'col-sm-6'i><'col-sm-6'p>>",
      "sPaginationType": "full_numbers",
      "aoColumns": [
        { "mData": "id" },  
         { "mData": "cliente" },    
        { "mData": "nombre" },
        { "mData": "telefono" },
		{ "mData": "tipo_sociedad" },
        { "mData": "status" },
        { "mData": "buttons" }
      ]
    } );
    return false;
   });
	return false;
}


function JsNewSocio(documento) {
	$('#pantalla_actividades').toggleClass('hidden');	
	$('#documento').html(documento);
}

function hiddensocio () {
	$('#pantalla_actividades').toggleClass('hidden');
	return false;
}

function validar_socio(id) {
	var form = $('#form_socios');

	if($("#acciones_socios").val() == ""){
		 $("#acciones_socios").attr("placeholder", "Necesario");
		$("#acciones_socios").focus();
		return false;
	}


	if($("#valor").val() == ""){
		 $("#valor").attr("placeholder", "Necesario");
		$("#valor").focus();
		return false;
	}


	if($("#nombre_socio").val() == ""){
		 $("#nombre_socio").attr("placeholder", "Necesario");
		$("#nombre_socio").focus();
		return false;
	}


	if($("#rfc_socio").val() == ""){
		 $("#rfc_socio").attr("placeholder", "Necesario");
		$("#rfc_socio").focus();
		return false;
	}

	if($("#curps_socio").val() == ""){
		 $("#curps_socio").attr("placeholder", "Necesario");
		$("#curps_socio").focus();
		return false;
	}
	$('#progress').removeClass('hidden');
	$('#submit').prop('disabled',true);
	$.ajax({
			url: 'negocios/socionew/'+id,
			type: 'POST',
			dataType: 'json',
			data: form.serialize(),
			success:function (data) {
				if(data.bien)
					$('#progress').attr("src","images/ok.png").load(function() {
						alert(data.msg)
						//alertify.alert(data.msg);
						//window.location = "mascotas/edit/"+id
						jsCargarSocios(id);
					});
				else
					$('#progress').attr("src","images/error.png").load(function() {
						alert(data.msg)
						//alertify.alert(data.msg);
						//window.location = "mascotas/edit/"+id
						jsCargarSocios(id);
					});
			},
			error:function (error1,error2) {
				console.log(error1,error2);
			}
		});
		return false;




}

function jsCargarSocios(id){



}



function actionDelete(){
	$("#grid_listado .delete_usuarios").click(function(event){
		var __confirm = confirm("Desea eliminar el regsitro?");
		if(__confirm){
			var id = $(this).attr("href");
			var id = id.split("/")[1];
			$.ajax({
				async:true,
				dataType: "json",
				type: "GET",
				url: "negocios/delete/"+id ,
				global: true,
				ifModified: false,
				processData:true,
				contentType: "application/x-www-form-urlencoded",
				success: function(datos){
					console.log(datos);
					if( datos["status"] == 1 ){
						//$("#row_usuarios_"+id).remove();
						alert(datos["msj"]);
						window.location = window.location.href;
						//msjReturn("Eliminar" , datos["msj"] , "success");
					} else {
						//msjReturn("Eliminar" , datos["msj"] , "error");
						alert(datos["msj"]);
					}
				}
			});
			return false;
		}
		return false;
	});	
	return false;
}




function JscalcularAccion(){

	var acciones=$("#acciones_socios").val();
	var valor=$("#valor").val();
	var suma=acciones*valor;
	if(suma!=''){$("#suma").val(suma);}

	 return false;




}
 function isNumberKey(evt)
      {
         var charCode = (evt.which) ? evt.which : event.keyCode
         if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
 
         return true;
      }


+function ($) { "use strict";

  $(function(){
  	Grid();
  	//CKEDITOR.replace("contenido");
  });
}(window.jQuery);
