  <section id="content" class="m-t-lg wrapper-md animated fadeInUp">    
    <div class="container aside-xl" align="center">
      <img src="images/logoprincipal.png" width="197">
      <section class="m-b-lg">
        <!--<header class="wrapper text-center">
          <strong>Iniciar sesión</strong>
        </header>--><br>
        <form id="login" method="post" action="./login/">
          <div class="list-group">
            <div class="list-group-item">
              <input type="text" placeholder="Email o Usuario" class="form-control no-border" id="usuario" name="usuario">
            </div>
            <div class="list-group-item">
               <input type="password" placeholder="Password" class="form-control no-border" id="pass" name="pass">
            </div>
          </div>
          <center>
            <span class="text-primary">
                {{ content() }}
            </span>
          </center>
          <button type="submit" class="btn btn-lg btn-primary btn-block">Ingresar</button>
        </form>
      </section>
    </div>
  </section>
  <!-- footer -->
  <footer id="footer">
    <div class="text-center padder">
      <p>
        <small>MD <br>&copy; <?php echo date('Y') ?> v1</small>
      </p>
    </div>
  </footer>    
