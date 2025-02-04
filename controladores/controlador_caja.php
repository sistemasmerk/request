<?php
class Controlador_caja extends Controlador_Base{
    public $modelo;
    public $directiva;
    public $html;
    
    public function __construct(){
        $this->modelo = new Modelos();
        $this->directiva = new Directivas();
    }
    
    public function apertura(){
        $breadcrumbs = array('cierre');
        $this->breadcrumbs = $this->directiva->nav_breadcumbs(8, 2, $breadcrumbs);
    }
    
    public function cierre(){
        $breadcrumbs = array('apertura');
        $this->breadcrumbs = $this->directiva->nav_breadcumbs(8, 2, $breadcrumbs);
    }
    
    public function alta_bd(){      
        $tabla = $_GET['seccion'];
        $accion = $_GET['session_accion'];
        $tipo = '';
        $registros = $_POST;
        $usuario = $_SESSION['usuario_id'];
            
        if($accion == 'apertura'){
            $tipo = 'A';
        }else if($accion == 'cierre'){
            $tipo = 'C';
        }
    
        //echo" +++ <pre>"; print_r($producto); echo"</pre>"; die;
        foreach ($registros AS $registro => $valor){
            $producto['producto_id'] = $registro;
            $producto['cantidad'] = $valor;
            $producto['tipo'] = $tipo;
            $producto['usuario'] = $usuario;
             
            $resultado = $this->modelo->alta_bd($producto, $tabla);
        }
        

        if($resultado['error']){
            $mensaje = $resultado['mensaje'];
            header("Location: ./index.php?seccion=$tabla&accion=alta&mensaje=$mensaje&tipo_mensaje=error");
            exit;
        }


        header("Location: ./index.php?seccion=session&accion=inicio&mensaje=$accion de caja exitosa&tipo_mensaje=exito");
    }
    
    public function balance(){
        $aperturas = $this->modelo->obten_apertura_cierre('A');
        $cierres = $this->modelo->obten_apertura_cierre('C');
        $ventas = $this->modelo->obten_ventas_cierre();
        $registros = array();
        
        for($i=0; $i<count($aperturas); $i++){
            foreach ($cierres AS $cierre){
                if($aperturas[$i]['producto_id'] == $cierre['producto_id']){
                    $diferencia = $aperturas[$i]['cantidad'] - $cierre['cantidad'];
                    $registros[$i]['producto_id'] = $cierre['producto_id'];
                    $registros[$i]['nombre'] = $cierre['nombre'];
                    $registros[$i]['apertura'] = $aperturas[$i]['cantidad'];
                    $registros[$i]['cierre'] = $cierre['cantidad'];                    
                    if($aperturas[$i]['producto_id'] == 13){
                        $registros[$i]['diferencia'] = $cierre['cantidad'] - $aperturas[$i]['cantidad'];
                        $registros[$i]['total'] = $aperturas[$i]['cantidad'] + $cierre['cantidad'];
                    }else{
                        $registros[$i]['diferencia'] = $diferencia;
                        $registros[$i]['total'] = $diferencia * $cierre['precio'];
                    }
                    
                }
            }
        }
        
        for($i=0; $i<count($registros); $i++){
            foreach ($ventas AS $venta){
                if($registros[$i]['producto_id'] == 13){
                    $registros[$i]['venta'] = $registros[$i]['diferencia'];
                }
                if($registros[$i]['producto_id'] == $venta['producto_id']){                             
                    $registros[$i]['venta'] = $venta['cantidad'];                    
                }
            }
            if(in_array('venta', $registros[$i])){//print_r(         $registros[$i]['producto_id']);echo"-----------++---------------<br>";
                $registros[$i]['venta'] = 0;
            }
            if($registros[$i]['diferencia'] != $registros[$i]['venta']){
                $registros[$i]['cuadra'] = '<i class="icon-cancel"></i>';
            }else{
                $registros[$i]['cuadra'] = '<i class="icon-ok cuadra_ok"></i>';
            }
        }
        
        $encabezados = array('producto_id', 'nombre', 'apertura', 'cierre', 'diferencia', 'venta', 'total', 'cuadra');        
        $tabla = $this->directiva->crea_tabla($encabezados, $registros);
        
        $concepto_percepcion = $this->modelo->obten_movimiento('P');
        $concepto_deduccion = $this->modelo->obten_movimiento('D');
        $tpercepcion = count($concepto_percepcion);
        $tdeduccion = count($concepto_deduccion);
        $tamano = $tpercepcion > $tdeduccion ? $tpercepcion : $tdeduccion;
        
        for($i=0; $i<$tamano; $i++){
            
        }
        
        $encabezado_balance = array('percepciones', 'deducciones');
        $tabla_balance = $this->directiva->crea_tabla_balance($encabezado_balance, $registros);
        $this->html = $tabla;
        //echo" +++ <pre>"; print_r($registros); echo"</pre>"; die;
        
    }
        
}