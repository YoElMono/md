var actionForm = 0;
var typeEdit = 0;
var tableGrid = null;

if(ajaxType){
	actionForm = 1;
}

var socios = [];

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

function JsEditarSocio (index) {
	$('#pantalla_actividades').toggleClass('hidden');
	var data = socios[index];
	console.log(data);	
	$('#documento').html(data.nombre);
	$("#form_socios").append('<input type="hidden" name="id" value="'+data.id+'">');
	$("#acciones_socios").val(data.acciones)
	$("#valor").val(data.valor)
	$("#suma").val(data.total)
	$("#nombre_socio").val(data.nombre)
	$("#rfc_socio").val(data.rfc)
	$("#curps_socio").val(data.curp)
}

function hiddensocio () {
	$('#pantalla_actividades').toggleClass('hidden');
	return false;
}

function JsEliminarSocio (index,id) {
	var data = socios[index];
	if(confirm("¿Desea elminar al socio "+data.nombre+"?")){
		$.ajax({
			url: 'negocios/deletesocio/'+data.id,
			type: 'POST',
			dataType: 'json',
			success:function (data) {
				if(data.status == 1){
					alert(data.msj);
					//$('#progress').attr("src","images/ok.png").load(function() {
						//alertify.alert(data.msg);
						//window.location = "mascotas/edit/"+id
						//$("#pantalla_actividades").toggleClass('hidden');
						jsCargarSocios(id);
					//});

				}
				else{
					//$('#progress').attr("src","images/error.png").load(function() {
						//alertify.alert(data.msg);
						//window.location = "mascotas/edit/"+id
					alert(data.msj);
						jsCargarSocios(id);
					//});
				}
			},
			error:function (error1,error2) {
				console.log(error1,error2);
			}
		});
	}
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

	//console.log("data:"+form.serialize());
	$.ajax({
			url: 'negocios/socionew/'+id,
			type: 'POST',
			dataType: 'json',
			data: form.serialize(),
			success:function (data) {
				console.log(data.bien);
				if(data.bien){
					//$('#progress').attr("src","images/ok.png").load(function() {
						//alertify.alert(data.msg);
						//window.location = "mascotas/edit/"+id
						$("#pantalla_actividades").toggleClass('hidden');
						jsCargarSocios(id);
						alertify.alert(data.msg);
					//});
				}
				else{
					//$('#progress').attr("src","images/error.png").load(function() {
						//window.location = "mascotas/edit/"+id
						jsCargarSocios(id);
						alertify.alert(data.msg);
					//});
				}
			},
			error:function (error1,error2) {
				console.log(error1,error2);
			}
		});
		return false;




}

function jsCargarSocios(id){

	$.ajax({
			url: 'negocios/getsocios/'+id,
			type: 'POST',
			dataType: 'json',
			success:function (data) {
				console.log(data);
				if(data.bien){
					//$('#progress').attr("src","images/ok.png").load(function() {
						//alert(data.msg);
						socios = data.socios;
                        $("#listado_socios").html("")
						for(var i = 0; i < socios.length; i++){
							var socio = socios[i];
							console.log(socio);
							var html = '<li class="list-group-item">'+ 
	                                '<div class="media"> '+
	                                  '<span class="pull-left thumb-sm"><img src="images/profile.png" alt="" class="img-circle"></span> '+
                                 	  '<div class="pull-right text-danger m-t-sm"> '+
	                                    '<b>Total:</b> $'+socio.total+' '+ 
	                                    '<a href="#" onclick="JsEditarSocio('+i+');return false" class="btn btn-sm btn-icon btn-primary"><i class="fa fa-pencil"></i></a>'+
	                                    '<a href="#" class="btn btn-sm btn-icon btn-danger" onclick="JsEliminarSocio('+i+','+socio.id_negocio+');return false"><i class="fa fa-trash-o"></i></a>'+
	                                  '</div>'+ 
	                                  '<div class="media-body">'+ 
	                                    '<div><a href="#">'+socio.nombre+'</a></div>'+
	                                    '<small class="text-muted"><b>RFC</b>: '+ socio.rfc+' | <b>CURP</b>: '+socio.curp+' | <b>No.Aciones</b>: '+socio.acciones+' <b>Valor Accion:</b> '+socio.valor+' </small>'+ 
	                                  '</div>'+ 
	                                '</div>'+ 
	                              '</li>';
	                        $("#listado_socios").append(html); 
						}

						//alertify.alert(data.msg);
						//window.location = "mascotas/edit/"+id
						//jsCargarSocios(id);
					//});
				}
				else{
					//$('#progress').attr("src","images/error.png").load(function() {
						alert(data.msg)
						//alertify.alert(data.msg);
						//window.location = "mascotas/edit/"+id
						//jsCargarSocios(id);
					//});
				}
			},
			error:function (error1,error2) {
				console.log(error1,error2);
			}
		});
		return false;

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
	if(acciones != "" && valor != ""){
		var suma=acciones*valor;
		if(suma!=''){$("#suma").val(suma);}
	}
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
