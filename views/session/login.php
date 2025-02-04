<div class="container">
  	<div class="card card-container login">
   		<img id="profile-img" class="profile-img-card" src="img/logo.png" />
     	<p id="profile-name" class="profile-name-card"></p>
      	<form class="form-signin" method="post"  action="./index.php?seccion=session&accion=loguea">
       		<span id="reauth-email" class="reauth-email"></span>
         	<input type="text" id="user" class="form-control" placeholder="Usuario" name="user" required autofocus>
         	<input type="password" id="password" class="form-control" placeholder="Password" name="password" required>
        	<button class="btn btn-lg btn-dark btn-block btn-signin" type="submit">Aceptar</button>
     	</form><!-- /form -->
   	</div><!-- /card-container -->
</div><!--