<?php 
require_once('config/seguridad.php');
require_once('config/requires.php');

$seguridad = new Seguridad();
$seccion = $seguridad->seccion;
$accion = $seguridad->accion;

define('SECCION',$seccion);
define('ACCION',$accion);

$modelo_accion = new accion();

//$permiso = $modelo_accion->valida_permiso(SECCION, ACCION);


/*if(!$permiso){
    $seccion = 'session';
    $accion = 'denegado';
    $_GET['tipo_mensaje'] = 'error';
    $_GET['mensaje'] = 'Permiso denegado';
}
*/
$directiva = new Directivas();
$template = new templates();
$name_ctl = 'controlador_'.$seccion;
$controlador = new $name_ctl;
                                                            //echo"-------------------"; print_r($accion); echo"---------";die;
$controlador->$accion();

    $include = './views/'.$seccion.'/'.$accion.'.php';
    if(file_exists($include)){
        include($include);
    }
    elseif(ACCION == 'lista_ajax') {
        include('./views/vista_base/lista_ajax.php');
    }
    elseif (ACCION == 'desactiva_bd'){
        include('./views/vista_base/desactiva_bd.php');
    }
    elseif (ACCION == 'activa_bd'){
        include('./views/vista_base/activa_bd.php');
    }
    elseif (ACCION == 'elimina_bd'){
        include('./views/vista_base/elimina_bd.php');
    }
    elseif (ACCION == 'registra_tiempo'){
        include('./views/vista_base/registra_tiempo.php');
    }elseif (ACCION == 'jquery_php'){
        include('./views/vista_base/jquery_php.php');
    }
    
?>