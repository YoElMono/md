  <script type="text/javascript">var titleAjax = '{{ titleAjax }}';</script>
  <script type="text/javascript">var ajaxType = true;</script>
  {{ javascript_include('assets/js/ckeditor/ckeditor.js') }}
  {{ javascript_include('assets/js/speech-commands.js') }}
  {{ javascript_include('assets/js/plugins/slimscroll/jquery.slimscroll.init.js') }}
  {{ javascript_include('assets/js/main.js') }}
  {{ javascript_include('assets/js/ajax.js') }}
  
  <div id="home_loader" style="display:none">
      <span id="preloaderImage"><img src="assets/images/preloader.gif"></span>
  </div>  
  <div id="wrapper">
    <div id="main-container">   
      <!-- BEGIN TOP NAVIGATION -->
        <nav class="navbar-top" role="navigation">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle pull-right" data-toggle="collapse" data-target=".top-collapse">
              <i class="fa fa-bars"></i>
            </button>
            <div class="navbar-brand">
              <a href="#">
                PingoShop Admin
              </a>
            </div>
          </div>
          <div class="nav-top">
              <ul class="nav navbar-right">
                <li class="dropdown">
                  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                    <i class="fa fa-bars"></i>
                  </button>
                </li>
                <!--<li class="dropdown">
                  <a href="#" class="speech-button">
                    <i class="fa fa-microphone"></i>
                  </a>
                </li>-->
                <li class="dropdown user-box">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <img class="img-circle" src="http://graph.facebook.com/{{ session_id_fb }}/picture" alt=""> <span class="user-info">{{ session_name }}</span> <b class="caret"></b>
                  </a>
                    <ul class="dropdown-menu dropdown-user">
                      <li>
                        <a href="login/logout/" onclick="return confirm('Esta seguro salir del sitio?');">
                          <i class="fa fa-power-off"></i> Salir
                        </a>
                      </li>
                    </ul>
                </li>
              </ul>
            <!-- BEGIN TOP MENU -->
              <div class="collapse navbar-collapse top-collapse">
                <ul class="nav navbar-left navbar-nav">
                  {{ Menu }}
                </ul>
              </div>
            <!-- END TOP MENU -->
          </div>
        </nav>
        <!-- END TOP NAVIGATION -->
        <!-- BEGIN SIDE NAVIGATION -->        
        <nav class="navbar-side {{ getShowSubMenu }}" role="navigation">
            {% if getHtmlSubMenu != "" %}
              <?php include(__DIR__.'/../views/' . $getHtmlSubMenu); ?>
            {% endif %}
        </nav><!-- /.navbar-side -->
      <!-- END SIDE NAVIGATION -->
      <!-- BEGIN MAIN PAGE CONTENT -->
        <div id="page-wrapper" class="{{ getShowSubMenu }}">
          <!-- BEGIN PAGE HEADING ROW -->
            <div class="row">
              <div class="col-lg-12">
                <!-- BEGIN BREADCRUMB -->
                <div class="breadcrumbs {{ getShowSubMenu }}">
                  {{ getHeaderMenu }}
                </div>
                <!-- END BREADCRUMB --> 
                <div style="height:15px">&nbsp;</div>
              </div>
            </div>
          <!-- END PAGE HEADING ROW -->         
            <div class="row">
              <div class="col-lg-12">
                <!-- START YOUR CONTENT HERE -->
                <div class="row">
                  {{ content() }}
                </div>      
                <!-- END YOUR CONTENT HERE -->
              </div>
            </div>
          <!-- BEGIN FOOTER CONTENT -->   
            <div class="footer">
              <div class="footer-inner {{ getShowSubMenu }}">
                <!-- basics/footer -->
                <div class="footer-content">
                  &copy; 2015 <a href="http://www.powersensesystem.com/">Power Sense System</a>, Todos los derechos reservados.
                </div>
                <!-- /basics/footer -->
              </div>
            </div>
            <button type="button" id="back-to-top" class="btn btn-primary btn-sm back-to-top">
              <i class="fa fa-angle-double-up icon-only bigger-110"></i>
            </button>
          <!-- END FOOTER CONTENT -->
        </div><!-- /#page-wrapper -->   
      <!-- END MAIN PAGE CONTENT -->
    </div>  
  </div>
  {{ javascript_include(jsFile) }}