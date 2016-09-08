var actionForm = 0;
var typeEdit = 0;
var tableGrid = null;

if(ajaxType){
	actionForm = 1;
}

var total_pagos = 0;
var total_gastos = 0;
var restante = 0;
var utilidad = 0;
var valor_total_pagos = 0;
var valor_total_gastos = 0;

function Grid(){
  $('[data-ride="datatables"]').each(function() {
    tableGrid = $(this).dataTable( {
      "initComplete": function(){
      	//setTimeout(actionDelete , 500);
      	//console.log("Termino");
      },
      "ajax" : "pagos/get/",
      "sDom": "<'row'<'col-sm-6'l><'col-sm-6'f>r>t<'row'<'col-sm-6'i><'col-sm-6'p>>",
      "sPaginationType": "full_numbers",
      "aoColumns": [
        { "mData": "id" },       
        { "mData": "cliente" },
        { "mData": "empresa" },
        { "mData": "costo" },
        { "mData": "pagos" },			
        { "mData": "status" },
        { "mData": "buttons" }
      ]
    } );
    return false;
   });
	return false;
}



//function actionDelete(){
	
	//return false;
//}

function JsNewPago(documento) {
	$('#pantalla_pagos').toggleClass('hidden');	
	/*$('#documento').html(documento);
	$("#acciones_socios, #suma, #nombre_socio, #rfc_socio, #curps_socio").val("");
	$("#form_socios [name='id']").remove();
	$("#guardar").attr("onclick","validar_socio("+$("#id_registro").val()+",-1);return false;");*/
}

function hiddenPago () {
	$('#pantalla_pagos').toggleClass('hidden');
	return false;
}
function JsNewGasto(documento) {
	$('#pantalla_gasto').toggleClass('hidden');	
	/*$('#documento').html(documento);
	$("#acciones_socios, #suma, #nombre_socio, #rfc_socio, #curps_socio").val("");
	$("#form_socios [name='id']").remove();
	$("#guardar").attr("onclick","validar_socio("+$("#id_registro").val()+",-1);return false;");*/
}

function hiddenGasto() {
	$('#pantalla_gasto').toggleClass('hidden');
	return false;
}


function new_pago () {
	var fecha = $("#fecha");
	var monto = $("#monto");
	var form = $("#form_pagos");
	if(fecha.val() == null || fecha.val() == ''){
		alert("Falta la fecha");
		return false;
	}
	if(monto.val() == null || monto.val() == ''){
		alert("Falta el monto");
		return false;
	}
	var data = form.serialize();
	data +="&tipo=Pago";
	console.log(data);
	$.ajax({
		url:"pagos/new/0",
		data:data,
		type:"post",
		dataType:"json",
		beforeSend:function () {
			$("#cargando").show();
		},
		success:function (Data) {
			console.log(Data);
			$("#cargando").hide();
			if(Data.status == 1){
				total_pagos += 1;
				var html = '<li class="list-group-item">'+ 
                        '<div class="media">'+
                          '<span class="pull-left thumb-sm"><img src="images/processing-file.png" class="img-circle"></span>'+                     
                          '<span class="pull-right thumb-sm"><a href="delete/'+Data.id+'/" class="btn btn-sm btn-icon btn-danger delete"><i class="fa fa-minus"></i></a> </span>'+
                          '<div class="media-body">'+
                            '<div><small class="text-muted"> <b>Fecha:</b> '+Data.fecha+'</small> | <small class="text-muted">Pago '+total_pagos+'$'+number_format(Data.monto,2)+'</small> </div>'+
                          '</div>'+ 
                        '</div>'+ 
                      '</li> ';
                var html2 = $("#pagos").html()+html;
                $("#pagos").html(html2);
                restante -= parseInt(Data.monto);
                valor_total_pagos += parseInt(Data.monto);
                $("#total_pagos").html('<b>Pagos a la fecha</b> $ '+number_format(valor_total_pagos,2));
                $("#restante").html('<b>Restante</b> $'+number_format(restante,2));
                hiddenPago();
                alert("Pago agregado exitosamente");
			}else{
				alert(Data.msj);
			}
		},
		error:function (descripcion,error) {
			console.log(descripcion,error);
		}
	});
}


function new_gasto () {
	var fecha = $("#fecha1");
	var monto = $("#monto1");
	var concepto = $("#concepto");
	var form = $("#form_gastos");
	if(fecha.val() == null || fecha.val() == ''){
		alert("Falta la fecha");
		return false;
	}
	if(monto.val() == null || monto.val() == ''){
		alert("Falta el monto");
		return false;
	}
	if(concepto.val() == null || concepto.val() == ''){
		alert("Falta el concepto");
		return false;
	}
	var data = form.serialize();
	data +="&tipo=Gasto";
	console.log(data);
	$.ajax({
		url:"pagos/new/0",
		data:data,
		type:"post",
		dataType:"json",
		beforeSend:function () {
			$("#cargando").show();
		},
		success:function (Data) {
			console.log(Data);
			$("#cargando").hide();
			if(Data.status == 1){
				total_gastos += 1;
				var html = '<li class="list-group-item">'+ 
                        '<div class="media">'+
                          '<span class="pull-left thumb-sm"><img src="images/processing-file.png" class="img-circle"></span>'+                     
                          '<span class="pull-right thumb-sm"><a href="delete/'+Data.id+'/" class="btn btn-sm btn-icon btn-danger delete"><i class="fa fa-minus"></i></a> </span>'+
                          '<div class="media-body">'+
                          	'<div><small class="text-muted"> <b>Concepto:</b> '+Data.concepto+'</small></div>'+
                            '<div><small class="text-muted"> <b>Fecha:</b> '+Data.fecha+'</small> | <small class="text-muted">Pago '+total_gastos+'$'+number_format(Data.monto,2)+'</small> </div>'+
                          '</div>'+ 
                        '</div>'+ 
                      '</li> ';
                var html2 = $("#gastos").html()+html;
                $("#gastos").html(html2);
                utilidad -= parseInt(Data.monto);
                valor_total_gastos += parseInt(Data.monto);
                $("#total_gastos").html('<b>Gastos a la fecha</b> $ '+number_format(valor_total_gastos,2));
                $("#utilidad").html('<b>Utilidad</b> $'+number_format(restante,2));
                hiddenGasto();
                alert("Gasto agregado exitosamente");
			}else{
				alert(Data.msj);
			}
		},
		error:function (descripcion,error) {
			console.log(descripcion,error);
		}
	});
}

 function isNumberKey(evt)
      {
         var charCode = (evt.which) ? evt.which : event.keyCode
         if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
 
         return true;
      }



function number_format (number, decimals, decPoint, thousandsSep) { // eslint-disable-line camelcase
  //  discuss at: http://locutus.io/php/number_format/
  // original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
  // improved by: Kevin van Zonneveld (http://kvz.io)
  // improved by: davook
  // improved by: Brett Zamir (http://brett-zamir.me)
  // improved by: Brett Zamir (http://brett-zamir.me)
  // improved by: Theriault (https://github.com/Theriault)
  // improved by: Kevin van Zonneveld (http://kvz.io)
  // bugfixed by: Michael White (http://getsprink.com)
  // bugfixed by: Benjamin Lupton
  // bugfixed by: Allan Jensen (http://www.winternet.no)
  // bugfixed by: Howard Yeend
  // bugfixed by: Diogo Resende
  // bugfixed by: Rival
  // bugfixed by: Brett Zamir (http://brett-zamir.me)
  //  revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
  //  revised by: Luke Smith (http://lucassmith.name)
  //    input by: Kheang Hok Chin (http://www.distantia.ca/)
  //    input by: Jay Klehr
  //    input by: Amir Habibi (http://www.residence-mixte.com/)
  //    input by: Amirouche
  //   example 1: number_format(1234.56)
  //   returns 1: '1,235'
  //   example 2: number_format(1234.56, 2, ',', ' ')
  //   returns 2: '1 234,56'
  //   example 3: number_format(1234.5678, 2, '.', '')
  //   returns 3: '1234.57'
  //   example 4: number_format(67, 2, ',', '.')
  //   returns 4: '67,00'
  //   example 5: number_format(1000)
  //   returns 5: '1,000'
  //   example 6: number_format(67.311, 2)
  //   returns 6: '67.31'
  //   example 7: number_format(1000.55, 1)
  //   returns 7: '1,000.6'
  //   example 8: number_format(67000, 5, ',', '.')
  //   returns 8: '67.000,00000'
  //   example 9: number_format(0.9, 0)
  //   returns 9: '1'
  //  example 10: number_format('1.20', 2)
  //  returns 10: '1.20'
  //  example 11: number_format('1.20', 4)
  //  returns 11: '1.2000'
  //  example 12: number_format('1.2000', 3)
  //  returns 12: '1.200'
  //  example 13: number_format('1 000,50', 2, '.', ' ')
  //  returns 13: '100 050.00'
  //  example 14: number_format(1e-8, 8, '.', '')
  //  returns 14: '0.00000001'

  number = (number + '').replace(/[^0-9+\-Ee.]/g, '')
  var n = !isFinite(+number) ? 0 : +number
  var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals)
  var sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep
  var dec = (typeof decPoint === 'undefined') ? '.' : decPoint
  var s = ''

  var toFixedFix = function (n, prec) {
    var k = Math.pow(10, prec)
    return '' + (Math.round(n * k) / k)
      .toFixed(prec)
  }

  // @todo: for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.')
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
  }
  if ((s[1] || '').length < prec) {
    s[1] = s[1] || ''
    s[1] += new Array(prec - s[1].length + 1).join('0')
  }

  return s.join(dec)
}


+function ($) { "use strict";

  $(function(){
  	Grid();
  	//CKEDITOR.replace("contenido");
  	$(".delete").click(function(event){
		var __confirm = confirm("Desea eliminar el regsitro?");
		if(__confirm){
			var id = $(this).attr("href");
			var id = id.split("/")[1];
			$.ajax({
				async:true,
				dataType: "json",
				type: "GET",
				url: "pagos/delete/"+id ,
				global: true,
				ifModified: false,
				processData:true,
				contentType: "application/x-www-form-urlencoded",
				success: function(datos){
					console.log(datos);
					if( datos["status"] == 1 ){
						//$("#row_usuarios_"+id).remove();
						alert(datos["msj"]);
						window.location = "pagos/";
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

  	$("#fecha,#fecha1").datepicker({"format":"yyyy-mm-dd"});

  });
}(window.jQuery);
