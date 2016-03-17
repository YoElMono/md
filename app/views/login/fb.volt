    <div id="wrapper">
            <div class="login-container">
                <h2><a href="#"><img src="assets/images/logo_ep.png" alt="logo" class="img-responsive"></a></h2>
                <div id="login-box" class="login-box visible">                  
                    <p class="bigger-110">
                        <i class="fa fa-key"></i> Por favor, introduzca su informaci&oacute;n
                    </p>
                    <div class="hr hr-8 hr-double dotted"></div>
                    <form id="login" method="post" action="./login/">
                        <div class="form-group">
                            <div class="input-icon right">
                                <span class="fa fa-key text-gray"></span>
                                <input type="text" class="form-control" placeholder="Usuario" id="usuario" name="usuario">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-icon right">
                                <span class="fa fa-lock text-gray"></span>
                                <input type="password" class="form-control" placeholder="Contrase&ntilde;a" id="pass" name="pass">
                            </div>
                        </div>
                        <div class="tcb">
                            <a href="#" onclick="document.getElementById('login').submit(); return false;" class="pull-right btn btn-primary">Entrar<i class="fa fa-key icon-on-right"></i></a>
                            <div class="clearfix"></div>
                        </div>              
                        <div class="social-or-login" id="mensajes" style="display: ">{{ content() }}</div>
                        <hr >
                        <div class="social-or-login">
                            <span class="text-primary">O inicia sesi&oacute;n usando</span>
                        </div>
                        <div class="space-4"></div>
                        <div class="text-center">
                            <a href="login/fb/" class="btn btn-facebook btn-sm btn-circle"><i class="fa fa-facebook icon-only bigger-130"></i></a>
                        </div>
                        <div class="footer-wrap">
                            <center>Power Sense System v1.0.0</center>
                        </div>                          
                    </form>
                </div>
            </div>
    </div>
