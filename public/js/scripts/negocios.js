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
        { "mData": "razon_social" },
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
	$("#acciones_socios, #suma, #nombre_socio, #rfc_socio, #curps_socio").val("");
	$("#form_socios [name='id']").remove();
	$("#guardar").attr("onclick","validar_socio("+$("#id_registro").val()+",-1);return false;");
}

function JsEditarSocio (index) {
	$('#pantalla_actividades').toggleClass('hidden');
	var data = socios[index];
	var id = $("#form_socios [name='id']");
	console.log(id);	
	$('#documento').html(data.nombre);
	if(id != null) $("#form_socios [name='id']").remove();
	$("#form_socios").append('<input type="hidden" name="id" value="'+data.id+'">');
	$("#acciones_socios").val(data.acciones)
	$("#suma").val(data.total)
	$("#nombre_socio").val(data.nombre)
	$("#rfc_socio").val(data.rfc)
	$("#curps_socio").val(data.curp)
	$("#guardar").attr("onclick","validar_socio("+$("#id_registro").val()+","+index+");return false;");
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

function validar_socio(id,index) {
	$("guardar").prop("disabled",true);
	var form = $('#form_socios');
	var acciones_totales = $("#acciones_totales2").val();
	var socio = index == -1 ? "":socios[index];
	var conteo = 0;

	for (var i = 0; i < socios.length; i++) {
		var aux = socios[i];
		if(aux != socio)
			conteo += parseInt(aux.acciones);
	}

	//if(socio != ""){
		conteo += parseInt($("#acciones_socios").val());
		console.log("conteo: "+conteo+" total: "+acciones_totales);
		if(conteo > acciones_totales){
			alert("Este socio ya no puede tener tantas acciones");
	$("guardar").prop("disabled",false);
			return false;
		}
	//}


	if($("#acciones_socios").val() == ""){
		 $("#acciones_socios").attr("placeholder", "Necesario");
		$("#acciones_socios").focus();
	$("guardar").prop("disabled",false);
		return false;
	}


	if($("#valor").val() == ""){
		 $("#valor").attr("placeholder", "Necesario");
		$("#valor").focus();
	$("guardar").prop("disabled",false);
		return false;
	}


	if($("#nombre_socio").val() == ""){
		 $("#nombre_socio").attr("placeholder", "Necesario");
		$("#nombre_socio").focus();
	$("guardar").prop("disabled",false);
		return false;
	}


	if($("#rfc_socio").val() == ""){
		 $("#rfc_socio").attr("placeholder", "Necesario");
		$("#rfc_socio").focus();
	$("guardar").prop("disabled",false);
		return false;
	}

	if($("#curps_socio").val() == ""){
		 $("#curps_socio").attr("placeholder", "Necesario");
		$("#curps_socio").focus();
	$("guardar").prop("disabled",false);
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
						//alertify.
						alert(data.msg);
					//});
				}
				else{
					//$('#progress').attr("src","images/error.png").load(function() {
						//window.location = "mascotas/edit/"+id
						jsCargarSocios(id);
						//alertify.
						alert(data.msg);
					//});
				}
				$("guardar").prop("disabled",false);
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

function jsCargarDocumentos(id){

	$.ajax({
			url: 'negocios/getdocumentos/'+id,
			type: 'POST',
			dataType: 'json',
			success:function (data) {
				console.log(data);
				if(data.bien){
					//$('#progress').attr("src","images/ok.png").load(function() {
						//alert(data.msg);
						documentos = data.documentos;
                        $("#listado_documentos").html("")
						for(var i = 0; i < documentos.length; i++){
							var documento = documentos[i];
							console.log(documento);
							var html = '<li class="list-group-item"><div class="media"><div class="pull-right text-danger m-t-sm">'+documento.link+'</div><div class="media-body"><div><a href="#" onclick="return false;">'+documento.nombre+'</a></div></div></li>';
							$("#listado_documentos").append(html);
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







function JsNewDocumento(documento) {
	$('#pantalla_documentos').toggleClass('hidden');	
	$('#pantalla_documentos #documento').html(documento);	
	$("#guardar").attr("onclick","validar_socio("+$("#id_registro").val()+",-1);return false;");
}
function hiddendocumento() {
	$('#pantalla_documentos').toggleClass('hidden');
	return false;
}


function validar_documento (id) {
	
	var destino = "documentos";	
	var form = $("#form_documentos");


	if($("#nombre_documento").val() == ""){
		 $("#nombre_documento").attr("placeholder", "Necesario");
		$("#nombre_documento").focus();
	$("guardar").prop("disabled",false);
		return false;
	}


	var formData = new FormData(document.getElementById("form_documentos"));
	var inputFileImage = document.getElementById("filestyle-0");
	var file = inputFileImage.files[0];
	var name = $('#filestyle-0')[0].name;
	if(typeof(file) == "undefined"){
		alert("Archivo no seleccionado nada :( ");
		return false;
	}


	formData.append(name,file);
	$.ajax({
		url: 'negocios/'+destino+'/'+id,
		type: 'POST',
		dataType: 'json',
		data: formData,
		contentType:false,
		processData:false,
		cache:false,
		success:function (data) {
			console.log(data);
			$('#pantalla_documentos').toggleClass('hidden');
			if(data.bien){
				/*$('#progress3').attr("src","images/bien.png").load(function() {
					alert(data.msg)
					window.location = "negocios/edit/"+id
				});*/
				var html = '<li class="list-group-item"><div class="media"><div class="pull-right text-danger m-t-sm">'+data.doc.link+'</div><div class="media-body"><div><a href="#" onclick="return false;">'+data.doc.nombre+'</a></div></div></li>';
				$("#listado_documentos").append(html);
			}else{
				alert(data.msj);
			}
				/*$('#progress3').attr("src","images/mal.png").load(function() {
					alert(data.msg)
					window.location = 'negocios/edit/'+id
				});*/
		},
		error:function (error1,error2) {
			console.log(error1,error2);
		}
	});
	return false;
}












+function ($) { "use strict";

  $(function(){
  	Grid();
  	//CKEDITOR.replace("contenido");
  });
}(window.jQuery);
