var actionForm = 0;
var typeEdit = 0;
var tableGrid = null;

if(ajaxType){
	actionForm = 1;
}



/*var Latitud = '23.3852467' ;
var Longitud = '-111.5710476' ;  
var Latitud2 = '23.1015651' ;
var Longitud2 = '-103.7197809' ; */   
var Zoom = 4 ;
var map = null ;
var Marcardor = null;
var noIndex = 0 ;


function LoadMapa(){
	if($("#longitud").val()!='') Longitud = $("#longitud").val();
	if($("#latitud").val()!='') Latitud = $("#latitud").val();
	if($("#latitud").val()!='' &&  $("#latitud").val()!='') Zoom =16;



	var optionsMaps = {
		zoom: Zoom
		, center: new google.maps.LatLng(Latitud,Longitud)
		, mapTypeId: google.maps.MapTypeId.ROADMAP
		, mapTypeControl: true
	   , zoomControl: true,
		 zoomControlOptions: {
			position: google.maps.ControlPosition.LEFT_CENTER   
		}
	   , panControl: false,
		 panControlOptions: {
			position: google.maps.ControlPosition.TOP_LEFT
		} ,
		streetViewControl: true
	};
	map = new google.maps.Map(document.getElementById("mapa"), optionsMaps);
	Marker(Latitud,Longitud);
}  


function Marker(x,y){
	$("#latitud").val(x);
	$("#longitud").val(y);
	Marcador = new google.maps.Marker({
		position: new google.maps.LatLng(x,y)
		, map: map
		, cursor: "default"
		, draggable: true
	});		
	google.maps.event.addListener(Marcador, 'dragend', function(e){
		$("#latitud").val(e.latLng.lat());
		$("#longitud").val(e.latLng.lng());	
		Longitud = e.latLng.lat() ;    
		Latitud = e.latLng.lng() ;		
	});			
}












function Grid(){
  $('[data-ride="datatables"]').each(function() {
    tableGrid = $(this).dataTable( {
      "initComplete": function(){
      	setTimeout(actionDelete , 500);
      	console.log("Termino");
      },
      "ajax" : "sucursales/get/",
      "sDom": "<'row'<'col-sm-6'l><'col-sm-6'f>r>t<'row'<'col-sm-6'i><'col-sm-6'p>>",
      "sPaginationType": "full_numbers",
      "aoColumns": [
        { "mData": "id" },      
        { "mData": "nombre" },
        { "mData": "direccion" },
        { "mData": "telefono" },
        { "mData": "nombre_contacto" },
        { "mData": "status" },
        { "mData": "buttons" }
      ]
    } );
    return false;
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
				url: "sucursales/delete/"+id ,
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








+function ($) { "use strict";

  $(function(){
  	Grid();
  	LoadMapa();
  });
}(window.jQuery);
