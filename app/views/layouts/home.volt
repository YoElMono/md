<!DOCTYPE html>
<html lang="en" class="app">
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
  {{ stylesheet_link('js/fullcalendar/fullcalendar.css') }}
  {{ stylesheet_link('js/fullcalendar/theme.css') }}
  {{ stylesheet_link('js/datepicker/datepicker.css') }}
  {{ stylesheet_link('js/calendar/bootstrap_calendar.css') }}
  {{ stylesheet_link('js/datatables/datatables.css') }}
  {{ stylesheet_link('js/dropzone/basic.min.css') }}
  {{ stylesheet_link('js/dropzone/dropzone.min.css') }}
   {{ stylesheet_link('js/gritter/jquery.gritter.css') }}
  <!--[if lt IE 9]>
    {{ javascript_include('js/ie/html5shiv.js') }}
    {{ javascript_include('js/ie/respond.min.js') }}
    {{ javascript_include('js/ie/excanvas.js') }}
  <![endif]-->
  <script type="text/javascript">var titleAjax = '{{ titleAjax }}';</script>
  <script type="text/javascript">var ajaxType = false;</script>  
   <!--{{ javascript_include('js/jquery.min.js') }}
  <script src="js/jquery.min.js"></script>-->
  <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>

  {{ javascript_include('js/bootstrap.js') }}
  {{ javascript_include('js/app.js') }}
  {{ javascript_include('js/slimscroll/jquery.slimscroll.min.js') }}
  {{ javascript_include('js/charts/easypiechart/jquery.easy-pie-chart.js') }}
  {{ javascript_include('js/charts/sparkline/jquery.sparkline.min.js') }}
  {{ javascript_include('js/charts/flot/jquery.flot.min.js') }}
  {{ javascript_include('js/charts/flot/jquery.flot.tooltip.min.js') }}
  {{ javascript_include('js/charts/flot/jquery.flot.spline.js') }}
  {{ javascript_include('js/charts/flot/jquery.flot.pie.min.js') }}
  {{ javascript_include('js/charts/flot/jquery.flot.resize.js') }}
  {{ javascript_include('js/charts/flot/jquery.flot.grow.js') }}
  {{ javascript_include('js/charts/flot/demo.js') }}
  {{ javascript_include('js/calendar/bootstrap_calendar.js') }}
  {{ javascript_include('js/datatables/jquery.dataTables.min.js') }} 
  {{ javascript_include('js/sortable/jquery.sortable.js') }}
  {{ javascript_include('js/app.plugin.js') }}
  {{ javascript_include('js/parsley/parsley.min.js') }}
  {{ javascript_include('js/parsley/parsley.extend.js') }}
  {{ javascript_include('js/globales.js') }}
  {{ javascript_include('js/ajax.js') }}
  {{ javascript_include('js/wysiwyg/jquery.hotkeys.js') }}
  {{ javascript_include('js/wysiwyg/bootstrap-wysiwyg.js') }}
  {{ javascript_include('js/wysiwyg/demo.js') }} 

  {{ javascript_include('js/datepicker/bootstrap-datepicker.js') }}
  {{ javascript_include('js/ckeditor/ckeditor.js') }}
  {{ javascript_include('js/gritter/jquery.gritter.min.js') }}
  {{ javascript_include('js/dropzone/dropzone.js') }}

  {{ javascript_include('js/llqrcode.js') }}
  <script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
{{ javascript_include('js/webqr.js') }}



  <!--{{ javascript_include('js/dropzone/dropzone.min.js') }}-->
  <!--{{ javascript_include(jsFile) }}-->
  <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places,geometry"></script> 
  
  <!-- Bootstrap -->
  <!-- App -->
 
  <script type="text/javascript" src="<?php echo $jsFile ?>"></script>
  <!--{{ javascript_include("https://maps.googleapis.com/maps/api/js?sensor=true&libraries=places,geometry") }}-->  

</head>
<body class="" >
  <section class="vbox">
    <header class="bg-white header header-md navbar navbar-fixed-top-xs box-shadow">
      <div class="navbar-header aside-md dk">
        <a class="btn btn-link visible-xs" data-toggle="class:nav-off-screen,open" data-target="#nav,html">
          <i class="fa fa-bars"></i>
        </a>
        <a href="./" class="navbar-brand">
          <img src="images/logo_interior.png" class="m-r-sm" alt="scale">
         
        </a>
        <a class="btn btn-link visible-xs" data-toggle="dropdown" data-target=".user">
          <i class="fa fa-cog"></i>
        </a>
      </div>
      <ul class="nav navbar-nav navbar-right m-n hidden-xs nav-user user">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <span class="thumb-sm avatar pull-left">
              <img src="http://graph.facebook.com/{{ session_id_fb }}/picture" alt="{{ session_name }}">
            </span>
            {{ session_name }} <b class="caret"></b>
          </a>
          <ul class="dropdown-menu animated fadeInRight">            
            <li>
              <a href="login/logout/" onclick="return Logout();" data-toggle="" >Logout</a>
            </li>
          </ul>
        </li>
      </ul>
    </header>
    <section>
      <section class="hbox stretch">
        <!-- .aside -->
        <aside class="bg-black aside-md hidden-print hidden-xs" id="nav">          
          <section class="vbox">
            <section class="w-f scrollable">
              <div class="slim-scroll" data-height="auto" data-disable-fade-out="true" data-distance="0" data-size="10px" data-railOpacity="0.2">
                             
                <!-- nav -->                 
                <nav class="nav-primary hidden-xs">
                  <ul class="nav nav-main" data-ride="collapse">
                    {{ menu }}
                  </ul>
                </nav>
                <!-- / nav -->
              </div>
            </section>
            <footer class="footer hidden-xs no-padder text-center-nav-xs">
             
              <a href="#nav" data-toggle="class:nav-xs" class="btn btn-icon icon-muted btn-inactive m-l-xs m-r-xs">
                <i class="i i-circleleft text"></i>
                <i class="i i-circleright text-active"></i>
              </a>
            </footer>
          </section>
        </aside>
        <!-- /.aside -->
        

        <section id="content">
        <div>{{ getHeaderMenu }}</div>
          <section class="hbox stretch">
            <section>
              <section class="vbox">
                <section class="scrollable padder">              
                  {{ content() }}
                  <!--
                  <p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p>
                  <p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p>
                  <p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p>
                  <p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p>
                  <p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p>
                  <p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p>
                  <p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p>
                  <p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p><p>m</p>
                  -->
                </section>
              </section>
            </section>
          </section>
          <a href="#" class="hide nav-off-screen-block" data-toggle="class:nav-off-screen,open" data-target="#nav,html"></a>
        </section>
      </section>
    </section>
  </section>

</body>
</html>