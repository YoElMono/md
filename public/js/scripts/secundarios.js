var Marcardor = null;
var noIndex = 0 ;


if(ajaxType){
	actionForm = 1;
	noIndex = 1;
}

	




$(document).ready(function() {
	if( noIndex == 1 && $('#formulario_registro').length ){
		$('#formulario_registro').validate({
			errorElement: 'div',
			errorClass: 'help-block',
			focusInvalid: false,
			rules: {
				status: 'required',
				id_usuario: 'required',
				nombre: 'required',
				user: 'required',
				pass: 'required'
			},
			messages: {
				status: "Por favor ingrese un dato",
				id_usuario: "Por favor ingrese un dato",
				nombre: "Por favor ingrese un dato",
				user: "Por favor ingrese un dato",
				pass: "Por favor ingrese un dato"
			},
			invalidHandler: function (event, validator) {
				$('.alert-danger', $('.login-form')).show();
			},
			highlight: function (e) {
				$(e).closest('.form-group').removeClass('has-info').addClass('has-error');
			},
			success: function (e) {
				$(e).closest('.form-group').removeClass('has-error').addClass('has-info');
				$(e).remove();
			},
			errorPlacement: function (error, element) {
				if(element.is(':checkbox') || element.is(':radio')) {
					var controls = element.closest('div[class*="col-"]');
					if(controls.find(':checkbox,:radio').length > 1) controls.append(error);
					else error.insertAfter(element.nextAll('.labels:eq(0)').eq(0));
				}
				else error.insertAfter(element.parent());
			},
			submitHandler: function (form) {
				ajaxBody($("#formulario_registro").attr("action") , "POST" , $("#formulario_registro").serialize() , "secundarios/");
				return false;
			},
			invalidHandler: function (form) {
			}
		});
	}
	$("#form_secundarios .delete_secundarios").click(function(event){
		var __confirm = confirm("Desea eliminar el registro?");
		if(__confirm){
			var id = $(this).attr("href");
			$.ajax({
				async:true,
				dataType: "json",
				type: "GET",
				url: "secundarios/delete/"+id ,
				global: true,
				ifModified: false,
				processData:true,
				contentType: "application/x-www-form-urlencoded",
				success: function(datos){
					console.log(datos);
					if( datos["status"] == 1 ){
						$("#row_secundarios_"+id).remove();
						msjReturn("Eliminar" , datos["msj"] , "success");
					} else {
						msjReturn("Eliminar" , datos["msj"] , "error");
					}
				}
			});
			return false;
		}
		return false;
	});		
});
