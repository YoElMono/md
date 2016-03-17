function ajaxBody(url , type , post ,  back){
	if (typeof (type) == "undefined") { type = "POST" ; }
	if (typeof (post) == "undefined") { post = "" ; }
	if (typeof (back) == "undefined") { back = "" ; }
	console.log(url);
	console.log(type);
	console.log(post);
	alert(0);
	return false;
	$("#home_loader").show();
	$.ajax({
		async:true,
		dataType: "html",
		type: type,
		url: url ,
		data: post,
		global: true,
		ifModified: false,
		processData:true,
		contentType: "application/x-www-form-urlencoded",
		success: function(datos){
			if( back.length > 0 ){
				history.pushState({}, '', back);
			} else {
				history.pushState({}, '', url);
			}
			document.title = titleAjax;
			$("#home_loader").hide();
			$("body").html(datos);
		},
		error:function (xhr, ajaxOptions, thrownError){
			console.log(xhr.status);
			console.log(xhr.statusText);
			console.log(xhr.responseText);
			$("#home_loader").hide();
			if( xhr.status == "404" ){
				window.location = url;
			}
		}		
	});
	return false;
}



/*
$(document).ready(function() {	
	$("a").click(function(event){
		return true;
		var url = $(this).attr("href");
		var total = url.indexOf("#");
		var Total = url.indexOf("#wiki");
		if( Total >= 0 ){
			return true;
		}
		if( url.length > 3 && total < 0 ){
			console.log(url);
			if( url == "login/logout/" ){
				window.location = url;
				return false;
			}
			var Total = url.indexOf("/excel/");
			if( Total >= 0 ){
				window.open(url);
				return false;
			}
			var total = url.indexOf("del/");
			if( total >= 0 ){
				return false;
			}
			var total = url.indexOf("admin_clases_clases/new/");
			if( total >= 0 ){
				window.location = url;
				return false;
			}
			var total = url.indexOf("admin_clases_clases/edit/");
			if( total >= 0 ){
				window.location = url;
				return false;
			}
			var total = url.indexOf("admin_foro_clases/new/");
			if( total >= 0 ){
				window.location = url;
				return false;
			}
			var total = url.indexOf("admin_foro_clases/edit/");
			if( total >= 0 ){
				window.location = url;
				return false;
			}
			var total = url.indexOf("admin_wiki_clases/new/");
			if( total >= 0 ){
				window.location = url;
				return false;
			}
			var total = url.indexOf("admin_wiki_clases/edit/");
			if( total >= 0 ){
				window.location = url;
				return false;
			}

			var total = url.indexOf("foro/new/");
			if( total >= 0 ){
				window.location = url;
				return false;
			}
			var total = url.indexOf("foro/edit/");
			if( total >= 0 ){
				window.location = url;
				return false;
			}
			var total = url.indexOf("soporte/new/");
			if( total >= 0 ){
				window.location = url;
				return false;
			}
			var total = url.indexOf("soporte/edit/");
			if( total >= 0 ){
				window.location = url;
				return false;
			}
			var total = url.indexOf("clases/detalle/");
			if( total >= 0 ){
				window.location = url;
				return false;
			}
			var total = url.indexOf("foro/detalle/");
			if( total >= 0 ){
				window.location = url;
				return false;
			}
			var total = url.indexOf("soporte/detalle/");
			if( total >= 0 ){
				window.location = url;
				return false;
			}
			$("#home_loader").show();
			$.ajax({
				async:true,
				dataType: "html",
				type: "GET",
				url: url ,
				global: true,
				ifModified: false,
				processData:true,
				contentType: "application/x-www-form-urlencoded",
				success: function(datos){
					history.pushState({}, '', url);
					document.title = titleAjax;
					$("#home_loader").hide();
					$("body").html(datos);
				},
				error:function (xhr, ajaxOptions, thrownError){
					console.log(xhr.status);
					console.log(xhr.statusText);
					console.log(xhr.responseText);
					$("#home_loader").hide();
					if( xhr.status == "404" ){
						window.location = url;
					}					
				}
			});
		}
		return false;
	});
});
*/