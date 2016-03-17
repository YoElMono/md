var actionForm = 0;
var typeEdit = 0;
var tableGrid = null;

if(ajaxType){
	actionForm = 1;
}



function validarUsuariosFB(){
	var __confirm = confirm("Desea validar los usuarios?")
	if(__confirm){
		window.location = "usuarios/fb/";
	}
	return false;
}



function tipoUsuario(){
	var tipo = $('#formulario_registro #tipo').val();
 
	if( tipo.length <= 0 ){
		PermisosClear();
	} else {
		if( typeEdit == 0 ){
			if( tipo == "Usuario" ){
				PermisosUsuario();
			} else if( tipo == "Administrador" ){
				PermisosAdm();
      }else if(tipo == "Sucursal" ||  tipo == "Vendedor" || tipo == "Tablet" ){        
        $('#select_sucursales').show();        

			} else {
				PermisosClear();
			}
		}
	}
	return false;
}

function PermisosAdm () {
	var permisos = $(":checkbox");
	$.each(permisos, function(index, val) {
		var obj = $(val);
		obj.attr({'checked':true,"disabled":true})
		// console.log(val)
	});
}
function PermisosUsuario () {
	var permisos = $(":checkbox");
	$.each(permisos, function(index, val) {
		var obj = $(val);
		obj.attr({'checked':false,"disabled":false})
		// console.log(val)
	});
}




$(document).ready(function() {


});


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
				url: "usuarios/delete/"+id ,
				global: true,
				ifModified: false,
				processData:true,
				contentType: "application/x-www-form-urlencoded",
				success: function(datos){
					console.log(datos);
					if( datos["status"] == 1 ){
						$("#row_usuarios_"+id).remove();
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



function Grid(){
  $('[data-ride="datatables"]').each(function() {
    tableGrid = $(this).dataTable( {
      "initComplete": function(){
      	setTimeout(actionDelete , 500);
      	console.log("Termino");
      },
      "ajax" : "usuarios/get/",
      "sDom": "<'row'<'col-sm-6'l><'col-sm-6'f>r>t<'row'<'col-sm-6'i><'col-sm-6'p>>",
      "sPaginationType": "full_numbers",
      "aoColumns": [
        { "mData": "id" },
        { "mData": "nombre" },
        { "mData": "email" },
        { "mData": "user" },       
        { "mData": "tipo" },
        { "mData": "status" },
        { "mData": "buttons" }
      ]
    } );
    return false;
   });
	return false;
}



function GridNegocios(){
  $('[data-ride="datatables_negocios"]').each(function() {
    tableGrid = $(this).dataTable( {
      "initComplete": function(){
      	setTimeout(actionDelete , 500);
      	console.log("Termino");
      },
      "ajax" : "usuarios/getnegocios/",
      "sDom": "<'row'<'col-sm-6'l><'col-sm-6'f>r>t<'row'<'col-sm-6'i><'col-sm-6'p>>",
      "sPaginationType": "full_numbers",
      "aoColumns": [
        { "mData": "id" },
        { "mData": "estado" },
        { "mData": "nombre" },
        { "mData": "email" },
        { "mData": "status" },
        { "mData": "buttons" }
      ]
    } );
    return false;
   });
	return false;
}



function GridSucursales(){
  $('[data-ride="datatables_sucursales"]').each(function() {
    tableGrid = $(this).dataTable( {
      "initComplete": function(){
      	setTimeout(actionDelete , 500);
      	console.log("Termino");
      },
      "ajax" : "usuarios/getsucursales/",
      "sDom": "<'row'<'col-sm-6'l><'col-sm-6'f>r>t<'row'<'col-sm-6'i><'col-sm-6'p>>",
      "sPaginationType": "full_numbers",
      "aoColumns": [
        { "mData": "id" },
        { "mData": "estado" },
        { "mData": "negocio" },
        { "mData": "sucursal" },
        { "mData": "nombre" },
        { "mData": "email" },       
        { "mData": "status" },
        { "mData": "buttons" }
      ]
    } );
    return false;
   });
	return false;
}





function GridUsuarios(){
  $('[data-ride="datatables_usuarios"]').each(function() {
    tableGrid = $(this).dataTable( {
      "initComplete": function(){
      	setTimeout(actionDelete , 500);
      	console.log("Termino");
      },
      "ajax" : "usuarios/getusuarios/",
      "sDom": "<'row'<'col-sm-6'l><'col-sm-6'f>r>t<'row'<'col-sm-6'i><'col-sm-6'p>>",
      "sPaginationType": "full_numbers",
      "aoColumns": [
        { "mData": "id" },
        { "mData": "nombre" },
        { "mData": "email" },
         { "mData": "os" },     
        { "mData": "status" },
        { "mData": "buttons" }
      ]
    } );
    return false;
   });
	return false;
}



+function ($) { "use strict";

  $(function(){
  	Grid();
  	GridNegocios();
  	GridSucursales();
  	GridUsuarios();
 
  	if($('#formulario_registro #tipo').val() == "Administrador")
  		PermisosAdm();
  });
}(window.jQuery);