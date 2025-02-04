<?php
    class Controlador_Base{
        public $registros;
        public $mensaje;
        public $error;
        public $registro;
        public $registro_id;
        public $breadcrumbs;
        public $lista;
        public $modelo;
        public $directiva;

        public function __construct(){
            $this->modelo = new Modelos();
            $this->directiva = new Directivas();
        }

        public function alta(){
		$breadcrumbs = array('lista');
		$this->breadcrumbs = $this->directiva->nav_breadcumbs(8, 2, $breadcrumbs);
	}
        
        public function alta_bd(){      //echo"<pre>"; print_r($_POST); echo"</pre>"; die;
            $tabla = $_GET['seccion'];
            $accion = $_GET['accion'];
            $parametros = $_POST;
            $imagen = '';
            
            if($tabla == 'productos'){
                $_POST['imagen'] = $_FILES['archivo']['name'];
                $imagen = $this->guarda_imagen($_FILES);
            }else if($tabla == 'precios'){
                $resultado = $this->modelo->cambia_status_precio($_POST['producto_id']); //echo"----------->>>>>>>>>>>> " . $resultado; die;
                if($resultado == 0){
                    header("Location: ./index.php?seccion=$tabla&accion=alta&mensaje=No fue posible cambiar status de registros anteriores&tipo_mensaje=error");
                    exit;
                }
            }
    //echo"<pre>"; print_r($imagen); echo"</pre>"; die;        
            $registros = $this->cambia_letra_capital($_POST);

            $resultado = $this->modelo->alta_bd($registros, $tabla);

            if($resultado['error']){
                $mensaje = $resultado['mensaje'];
                header("Location: ./index.php?seccion=$tabla&accion=alta&mensaje=$mensaje&tipo_mensaje=error");
                exit;
            }
            
            if($tabla == 'productos'){
                $producto = $this->modelo->obten_producto_id($_POST['nombre']);
                $parametro = $producto[0]['producto_id'];
                header("Location: ./index.php?seccion=precios&accion=alta&producto_id=".$parametro."&mensaje=Registro insertado con éxito&tipo_mensaje=exito");
            }else if($tabla == 'precios'){
                header("Location: ./index.php?seccion=inventario&accion=alta&producto_id=".$_POST['producto_id']."&mensaje=Registro insertado con éxito&tipo_mensaje=exito");
            }
            else{
                header("Location: ./index.php?seccion=$tabla&accion=alta&mensaje=Registro insertado con éxito&tipo_mensaje=exito");
            }
        }
        
        public function cambia_letra_capital($registros){
            foreach ($registros AS $clave => $registro){
                if($clave == 'nombre' || $clave == 'proveedor' || $clave == 'unidad'){
                    $registros[$clave] = ucwords(strtolower($registro));
                    continue;
                }                
            }
            return $registros;
        }
        
        public function guarda_imagen($file){
            if($file['archivo']['type'] == 'image/png' ){
                if(@move_uploaded_file($file['archivo']["tmp_name"], './img/'.$file['archivo']['name'])){
                    return 1;
                }else{
                    return 0;
                }
            }
        }
        
        public function lista(){
            $breadcrumbs = array('alta');

                       
            $this->breadcrumbs = $this->directiva->nav_breadcumbs(12, 0, $breadcrumbs);
            $resultado = $this->modelo->obten_registros($_GET['seccion']);
//echo"<pre>"; print_r($resultado); echo"</pre>"; die;
            $this->registros = $resultado['registros'];
        }
        
        public function desactiva_bd(){
            $tabla = $_GET['seccion'];
            $registro_id = $_GET['registro_id'];
            $registro = $this->modelo->desactiva_bd($tabla, $registro_id);
            $this->resultado($registro);
        }
        
        public function activa_bd(){
            $registro_id = $_GET['registro_id'];
            $registro = $this->modelo->activa_bd(SECCION, $registro_id);
            $this->resultado($registro);
        }
        
        public function elimina_bd(){
            $tabla = $_GET['seccion'];
            $registro_id = $_GET['registro_id'];
            $registro = $this->modelo->elimina_bd($tabla, $registro_id);
            $this->resultado($registro);
        }
        
        public function resultado($registro){
            echo $registro['mensaje'];
            if($registro['error']){
                http_response_code(404);
            }
            else{
                http_response_code(200);
            }
        }

        public function modifica(){
            $breadcrumbs = array('alta','lista');
            $this->breadcrumbs = $this->directiva->nav_breadcumbs_modifica(8, 2, $breadcrumbs);

            $tabla = $_GET['seccion'];
            $this->registro_id = $_GET['registro_id'];                                              
            $resultado = $this->modelo->obten_por_id($tabla, $this->registro_id);   //echo"<pre>"; print_r($resultado); echo"</pre>";
            $this->registro = $resultado['registros'][0];
        }
        
        public function modifica_bd(){//echo"<pre>"; print_r($_POST); echo"</pre>";die;
            $tabla = $_GET['seccion'];
            $this->registro_id = $_GET['registro_id'];

            if($tabla == 'productos'){
                if(!isset($_POST['visible'])){
                    $_POST['visible'] = 0;
                }
                if($_FILES['archivo']['name']){
                    $_POST['imagen'] = $_FILES['archivo']['name'];
                    $imagen = $this->guarda_imagen($_FILES);
                }
                
            }
        //echo"<pre>"; print_r($_POST); echo"</pre>";die;
            $resultado = $this->modelo->modifica_bd($_POST, $tabla, $this->registro_id);

            if($resultado['error']){
                $mensaje = $resultado['mensaje'];
                header("Location: ./index.php?seccion=$tabla&accion=modifica&mensaje=$mensaje&tipo_mensaje=error&registro_id=$this->registro_id");
                exit;
            }
            header("Location: ./index.php?seccion=$tabla&accion=lista&mensaje=Registro modificado con éxito&tipo_mensaje=exito");
        }
        
    }
