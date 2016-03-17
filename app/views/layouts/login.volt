<!DOCTYPE html>
<html lang="es" class=" ">
<head>  
  <meta charset="utf-8" />
  <base href="{{ baseTag }}">
  {{ get_title() }}
  <meta name="description" content="app, web app, responsive, admin dashboard, admin, flat, flat ui, ui kit, off screen nav" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" /> 
   <link rel="icon" href="favicon.png" type="image/png" sizes="32x32">
  {{ stylesheet_link('css/bootstrap.css') }}
  {{ stylesheet_link('css/animate.css') }}
  {{ stylesheet_link('css/font-awesome.min.css') }}
  {{ stylesheet_link('css/icon.css') }}
  {{ stylesheet_link('css/font.css') }}
  {{ stylesheet_link('css/app.css') }}
  <!--[if lt IE 9]>
    {{ javascript_include('js/ie/html5shiv.js') }}
    {{ javascript_include('js/ie/respond.min.js') }}
    {{ javascript_include('js/ie/excanvas.js') }}
  <![endif]-->
</head>
<body class="" >
  {{ content() }}
  <!-- / footer -->
  {{ javascript_include('js/jquery.min.js') }}
  <!-- Bootstrap -->
  {{ javascript_include('js/bootstrap.js') }}
  <!-- App -->
  {{ javascript_include('js/app.js') }}
  {{ javascript_include('js/slimscroll/jquery.slimscroll.min.js') }}
  {{ javascript_include('js/app.plugin.js') }}
</body>
</html>