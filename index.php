<?php 
    require_once('config/seguridad.php');
    require_once('config/requires.php');
    require_once('config/configuracion.php');
    
    $seguridad = new Seguridad();
    $seccion = $seguridad->seccion;
    $accion = $seguridad->accion;
//print_r(_TITULO);    
    define('SECCION',$seccion);
    define('ACCION',$accion);
    
    $modelo_accion = new accion();
    
    $directiva = new Directivas();
    $template = new templates();
    $name_ctrl = 'controlador_'.$seccion;
    $controlador = new $name_ctrl;
    $controlador->$accion();
    $directivas = new Directivas();
?>

<!DOCTYPE html>

<html>
    <head>
        <meta charset="utf-8" name="viewport" content="
                width=device-width, height=device-height,
                initial-scale=1.0, maximum-scale=1.0, target-densityDpi=device-dpi" />
        <meta http-equiv="Expires" content="0">
        <meta http-equiv="Last-Modified" content="0">
        <meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
        <meta http-equiv="Pragma" content="no-cache">
        
        <title><?php echo _TITULO ?></title>
        
        <script src="./includes/js/bootstrap.bundle.min.js"></script>
        
        
        <script type="text/javascript" src="./includes/js/funciones.js"></script>
        <!--<script type="text/javascript" src="./includes/js/jquery-confirm.min.js"></script>-->
        
        <link rel="stylesheet" href="./includes/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
        
        <link rel="stylesheet" href="./includes/css/estilos.css" media="print">
        <link rel="stylesheet" href="./includes/css/estilos.css">
        
        <link rel="stylesheet" href="./includes/css/configuracion.css" media="print">
        <link rel="stylesheet" href="./includes/css/configuracion.css">
        <!--<link rel="stylesheet" href="./includes/css/jquery-confirm.min.css">-->
        
    </head>
    <body>
        
        <div class="container-fluid">
            <div class="row">
                
                <div class="barra-lateral col-12 col-sm-auto">
                    <?php 
                        if($seguridad->menu){
                    ?>
                    <img src=" <?php echo _LOGO ?>" class="logo">
                    <!--<nav class="menu d-flex d-sm-block justify-content-center flex-nowrap navbar-dark bg-dark">-->
                    <nav class="navbar menu d-flex d-sm-block justify-content-center flex-nowrap navbar-dark bg-dark">
                        <?php  echo $directiva->menu(); ?>                               
                    </nav>
                    <?php
                        } 
                    ?>
                </div>
                <main class="main col">
                    <?php                  
                    
                        $class_mensaje = "";
                        if(isset($_GET['tipo_mensaje'])){
                            $tipo_mensaje = $_GET['tipo_mensaje'];
                            if($tipo_mensaje == 'error'){
                                $class_mensaje = 'alert alert-danger';
                            }else if($tipo_mensaje == 'info'){
                                $class_mensaje = 'alert alert-info';
                            }else{
                              if($tipo_mensaje == 'exito'){
                                $class_mensaje = 'alert alert-success';
                              }
                            }
                        }

                        if(isset($_GET['mensaje'])){
                            $mensaje = $_GET['mensaje'];
                        }else{
                            $mensaje = "";
                        }
                    
                    ?>    
                      <div class="<?php echo $class_mensaje; ?> mensaje" ><?php echo $mensaje; ?></div>
                      
                    <?php
                        $include = './views/'.$seccion.'/'.$accion.'.php';                  //echo"----->" . $include;
                        if(file_exists($include)){
                            include($include);
                        }elseif(ACCION == 'lista') {
                            include('./views/vista_base/lista.php');
                        }elseif (ACCION=='modifica'){
                            include('./views/vista_base/modifica.php');
                        }elseif (ACCION=='alta'){
                            include('./views/vista_base/alta.php');
                        }elseif (ACCION=='documentos'){
                            include('./views/documentos.php');
                        }elseif (ACCION=='apertura'){
                            include('./views/vista_base/alta.php');
                        }elseif (ACCION=='cierre'){
                            include('./views/vista_base/alta.php');
                        }else if(ACCION=='balance'){
                            include('./views/vista_base/balance.php');
                        }
                        
                        //echo"<pre>"; print_r($controlador->actualiza_pago_mesa(1) ); echo"</pre>";
                    ?>  
                </main>
                
                <div class="jumbotron py-1"> 
                    <div class="d-flex justify-content-between">
                        <a href="<?php echo _WEB ?>" target="_blank" class="text-dark">
                            <i class="bi bi-globe2"></i> <?php echo _EMPRESA ?>
                        </a>
                        <span>
                            <i class="bi bi-pc-display-horizontal"></i> Tecnologías de la información - <?php echo date('Y'); ?>
                        </span>
                        <span>
                            <i class="bi bi-person-circle"></i> <?php if($_SESSION){ print_r($_SESSION['nombre']);} ?>
                        </span>                        
                    </div>
                </div>                
            </div>
        </div>
    </body>
</html>
