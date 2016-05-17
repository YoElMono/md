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
      "ajax" : "tramites/get/",
      "sDom": "<'row'<'col-sm-6'l><'col-sm-6'f>r>t<'row'<'col-sm-6'i><'col-sm-6'p>>",
      "sPaginationType": "full_numbers",
      "aoColumns": [
        { "mData": "id" },       
        { "mData": "empresa" },
        { "mData": "liquidador" },
        { "mData": "fecha" },		
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
				url: "tramites/delete/"+id ,
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
  	$("#filestyle-0").change(function (argument) {
		var inputFileImage = document.getElementById("filestyle-0");
		var file = inputFileImage.files[0];
  		var name = file.name;
  		console.log(file);
  		$("#archivo").val(name);
  	})

  	$("#fecha_entrega,#fecha1,#fecha2,#fecha3,#fecha4").datepicker({"format":"yyyy-mm-dd"});

  });
}(window.jQuery);
