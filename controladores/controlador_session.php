<?php 
class Controlador_Session {
	public function denegado(){
		
	}
	public function logout(){
		unset ($_SESSION['username']);
		session_destroy();
		header('Location: index.php?seccion=session&accion=login');
		exit;
	}
	public function loguea(){
		$modelo_usuario = new Usuario();
		$usuario = $_POST['user'];
		$password = $_POST['password'];

		$usuarios = $modelo_usuario->valida_usuario_password($usuario, $password);

		$datos_usuario = $usuarios['registros'];

		if($usuarios['error']){
			$mensaje = $usuarios['mensaje'];
			session_destroy();
			header("Location: ./index.php?seccion=session&accion=login&mensaje=$mensaje&tipo_mensaje=error");
			exit;
		}
		else{
                    $_SESSION['activa'] = 1;
                    $_SESSION['grupo_id'] = $datos_usuario[0]['sis_grupo_id'];
                    $_SESSION['usuario_id'] = $datos_usuario[0]['id'];
                    $_SESSION['nombre'] = $datos_usuario[0]['nombre'];
                    $mensaje = $usuarios['mensaje'];
                    //header("Location: ./index.php?seccion=session&accion=inicio&mensaje=Bienvenido&tipo_mensaje=exito");
                    header("Location: ./index.php?seccion=session&accion=inicio");
                    exit;
		}
	}
	public function login(){
	}
	public function inicio(){
	}
        
        public function registra_tiempo(){
            $modelos = new Modelos();
            $directivas = new Directivas();
            
            $mesa = $_POST['mesa'];
            $funcion = $_POST['funcion'];
    //echo"<pre> "; print_r($_POST  ); echo"</pre>";//die;

            if( $funcion == 'inicio'){
                $resultado = $modelos->registra_incio($mesa);
                $status = array("status"=>$resultado);
                echo json_encode($status);
            }else if( $funcion == 'fin'){ 
                $entre = $_POST['dato1'];
                $parcialidades = $_POST['dato2'];
                $pagado = $_POST['dato3'];
                $tiempo_id = $_POST['dato4'];
                $resultado = $modelos->registra_fin($mesa, $entre, $parcialidades, $pagado, $tiempo_id);
                $status = array("status"=>$resultado);
                echo json_encode($status);
            }else if($funcion == 'actualiza_modal'){
                $tiempo = $modelos->consulta_tiempos($mesa);
                echo json_encode($tiempo);
            }else if($funcion == 'status_mesa'){
                $tiempo = $modelos->consulta_tiempos($mesa);
           
                if($tiempo){
                    $status = array("status"=>"0");
                }else{
                    $status = array("status"=>"1");
                }
                //echo"<pre> "; print_r($tiempo  ); echo"</pre>";
                echo json_encode($status);
            }   
           
        }
        
        public function calcula_tiempo($registro){
            $ini = new DateTime($registro["fecha_inicio"]);
            $fin = new DateTime("now");                     
            $minutos = $ini->diff($fin);
    //echo "<pre> "; print_r($minutos);   echo"</pre>";
            $time = (($minutos->d * 24) * 60) + ($minutos->h * 60) + ($minutos->i);
            $inicio = array("inicio" => $ini->format("h:i:s a"));
            $hora_fin = array('fin' => $fin->format("h:i:s a"));
           
            $hora = strlen($minutos->h) == 1 ? "0".$minutos->h : $minutos->h;
            $minuto = strlen($minutos->i) == 1 ? "0".$minutos->i : $minutos->i;
            $segundo = strlen($minutos->s) == 1 ? "0".$minutos->s : $minutos->s;
        
            $tiempo = array("tiempo" => $hora .":". $minuto .":". $segundo);
        
            $costo_real = (($time * $registro["precio"]) / 60);            
            $costo = array("costo" => round($costo_real, 0, PHP_ROUND_HALF_UP));
            //$costo = ;
           
            $calculo = array_replace($registro, $inicio, $hora_fin, $tiempo, $costo);             
           
            //return $calculo;
            return $calculo;
        }
        
        public function calcula_tiempo_jugado($fecha_inicio){
            $ini = new DateTime($fecha_inicio);
            $fin = new DateTime("now");                     //echo"<pre>"; print_r($fin  ); echo"</pre>";
            $diferencia = $ini->diff($fin);
            $horas = ($diferencia->d * 24) + $diferencia->h;
            $hora = strlen($horas) == 1 ? "0".$horas : $horas;
            $minuto = strlen($diferencia->i) == 1 ? "0".$diferencia->i : $diferencia->i;
            $segundo = strlen($diferencia->s) == 1 ? "0".$diferencia->s : $diferencia->s;
    //echo"<pre> "; print_r( $diferencia ); echo"</pre>";die;        
            $tiempo = $hora . ':' . $minuto . ':' . $segundo;
            return $tiempo;
        }
        
        public function actualiza_consumos($id){
            $modelos = new Modelos();
            $ventas = $modelos->obten_ventas($id);
            $html = '';
    //echo"<pre> "; print_r( $ventas ); echo"</pre>";die;        
            foreach ($ventas AS $venta){
                if($venta['producto_id'] == 13){
                    $html .= '<i class="icon-dollar"></i>';
                }else{
                
                    if($venta['tipo'] == 'tiempo'){
                        $html .= '<i class="icon-clock"></i>';
                    }else{
                        for($i=0; $i<$venta['cantidad']; $i++){
                            $html .= '<img class="img-fluid rounded producto_cuenta" src="./img/'.$venta['imagen'].'">';
                        }
                    }
                }
                //$html .= '<img class="img-fluid rounded producto_cuenta" src="./img/'.$venta['imagen'].'">';
            }
            
            return $html;
            
        }
        
        public function actualiza_cuenta_ventas($cuenta){
            $modelos = new Modelos(); 
            $ventas = $modelos->obten_ventas($cuenta);
    //echo"<pre> --------+++++"; print_r($ventas  ); echo"</pre>";        
            $html = '';
            
            for($i=0; $i<count($ventas); $i++){
                if($ventas[$i]['producto_id'] == 13){
                    $ventas[$i]['nombre'] = 'Dinero';
                    $ventas[$i]['costo'] = $ventas[$i]['cantidad'];
                }
                    
                    $html .= "<tr class='fila_cuenta'>"; 
                        $html .= "<td><label class='custom-control custom-checkbox'><input type='Checkbox' tipo='".$ventas[$i]['tipo']."' value='".$ventas[$i]['ventas_id']."' name='' class='custom-control-input' checked><span class='custom-control-indicator'></span></label></td>";
                        $html .= "<td>".$ventas[$i]['nombre']."</td>";
                        $html .= "<td>".$ventas[$i]['cantidad']."</td>";
                        $html .= "<td>".$ventas[$i]['costo']."</td>";
                        $html .= "<td>".$ventas[$i]['hora']."</td>";
                        $html .= "<td>".$ventas[$i]['comentarios']."</td>";
                    $html .= "</tr'>";
                //}
            }
            return $html;
            //echo"<pre> --------+++++"; print_r($ventas  ); echo"</pre>";            
        }
        
        public function pagar_venta_cuenta($cuenta_id, $ventas_id, $tiempos_id){
            $modelos = new Modelos();
            $resultado = '';
                        
            if($ventas_id){
                $resultado = $modelos->pagar_ventas($cuenta_id, $ventas_id);
            }
            
            if($tiempos_id){
                $resultado = $modelos->pagar_tiempos($cuenta_id, $tiempos_id);
                $tiempos = $modelos->obten_status_tiempos($tiempos_id);
                $existe = 0;
               
                foreach ($tiempos AS $tiempo){
                    for($i=0; $i<count($tiempos); $i++){
                        if($tiempos[$i]['tiempo_id'] == $tiempo['tiempo_id'] && $tiempos[$i]['pagado'] == 0){
                            $existe ++;
                            continue ;
                        }
                    }                    
                    if($existe == 0){
                        $modelos->actualiza_status_tiempo($tiempo['tiempo_id']);
                    }
                }
            }
            
            if($resultado == 1){
                $noPagado = $modelos->obten_ventas($cuenta_id);
                if(!$noPagado){
                    $modelos->pagar_cuenta($cuenta_id);
                    return 'elimina_fila';
                }else{
                    return 'actualiza_fila';
                }
            }else{
                return 'Falla en la funcion pagar_venta_cuenta de controlador_session';
            }
            
        }
        
        public function carga_tiempo($tiempo_id, $nombre, $costo){
            $modelos = new Modelos();
            $existe_cuenta = $modelos->existe_cuenta($nombre);
            
            if(!$existe_cuenta){
                $modelos->nueva_cuenta($nombre);
                $existe_cuenta[0]['cuenta_id'] = $modelos->obten_id($nombre);
            }
    //echo"<pre>"; print_r($existe_cuenta  ); echo"</pre>";die;        
            $inicio = $modelos->obten_tiempo($tiempo_id);
            $tiempo = $this->calcula_tiempo_jugado($inicio['inicio']);
            $actualizo = $modelos->actualiza_tiempo($tiempo_id, $costo, $tiempo);

            if($actualizo == 1){
                $registro = $modelos->crea_tiempo_cuenta($tiempo_id, $existe_cuenta[0]['cuenta_id']);
                return $registro;
            }else{
                return 'No se actualizo el registro de tiempos en carga_tiempo controlador_session';
            }
            
        }
        
        public function muestra_carga_tiempo($tiempo_id, $nombre){
            $modelos = new Modelos(); 
            $directivas = new Directivas();
            $cuenta = $modelos->obten_id($nombre);
            $ventas = $modelos->obten_ventas($cuenta);
            $status_cuenta = array();
            
            $cant = count($ventas);
            if($cant == 1 && $ventas[0]['ventas_id'] == $tiempo_id){
                //insertar cuenta                
                $status_cuenta['html'] = $directivas->obten_cuentas($cuenta);
                $status_cuenta['status'] = 'n';
            }else{
                //actualiza consumos
                $status_cuenta['html'] = $this->actualiza_consumos($cuenta);
                $status_cuenta['status'] = 'a';
            }
            $status_cuenta['cuenta'] = $cuenta;
            return $status_cuenta;
        }
        
        public function actualiza_pago_mesa($mesa){
            $modelo = new Modelos();
            $tiempo = $modelo->consulta_tiempos($mesa);
            $html = '';
//echo"<pre> "; print_r($tiempo  ); echo"</pre>";die;            
            $html .= "<tr class='' id='".$tiempo['tiempo_id']."'>"; 
                $html .= "<td>".$tiempo['inicio']."</td>";
                $html .= "<td>".$tiempo['fin']."</td>";
                $html .= "<td>".$tiempo['tiempo']."</td>";
                $html .= "<td><strong><h2 class='costo_tiempo'>".$tiempo['costo']."</h2></strong></td>";
            $html .= "</tr'>";
            return $html;
        }
        
        public function jquery_php(){
            $modelos = new Modelos();
            $directivas = new Directivas();
            
            $cliente = $_POST['texto'];
            $inicio = $_POST['inicio'];
            $funcion = $_POST['funcion'];
            $id = $_POST['id'];
            $dato1 = $_POST['dato1'];
            
            if( $funcion == "guarda_baraja"){
                 $guarda = $modelos->guarda_baraja($cliente, $inicio, $id);
                 //$id = array("id"=>$guarda);
                 //echo json_encode($id);
            }else if( $funcion == "paga_jugador"){
                $tiempo = $cliente;
                $costo = $inicio;                
                $actualizo = $modelos->paga_jugador($id, $tiempo, $costo);
                $mesa = $modelos->consulta_mesa($id);
                $status_mesa = $modelos->consulta_tiempos_baraja($mesa);
                $respuesta = array("status"=>$actualizo, "status_mesa"=>$status_mesa);
                echo json_encode($respuesta);
            }else if( $funcion == "carga_ventas"){    //$ok = strpos($cliente, 'dinero');   echo('---------------------->>> ' . $ok);
                //$carga = 'si';
                if(strpos($cliente, 'dinero')){        
                    $carga = $modelos->carga_ventas_dinero($cliente, $inicio, $id);
                }else{
                    $carga = $modelos->carga_ventas($cliente, $inicio, $id, $dato1);
                }
                $respuesta = array("status"=>$carga, "id"=>$id);
                echo json_encode($respuesta);
            }else if( $funcion == "actualiza_consumos"){
                $html = $this->actualiza_consumos($id);
                $respuesta = array('html'=>$html);
                echo json_encode($respuesta);
            }else if($funcion == "actualiza_ventas"){
                $html = $this->actualiza_cuenta_ventas($id);
                $total = $modelos->obten_total_venta($id);
                $respuesta = array('html'=>$html, 'total'=>$total);
                echo json_encode($respuesta);
            }else if($funcion == 'pagar_cuenta'){
                $status = $this->pagar_venta_cuenta($id, $cliente, $inicio);
                $respuesta = array('status'=>$status);
                echo json_encode($respuesta);
            }else if( $funcion == 'nueva_cuenta'){
                $status = $modelos->nueva_cuenta($cliente, $inicio);
                $cuenta_id = $modelos->obten_id($cliente);
                $html = $directivas->obten_cuentas($cuenta_id);
                $respuesta = array('status'=>$status, 'id'=>$cuenta_id, 'html'=>$html);
                echo json_encode($respuesta);
            }else if($funcion == 'carga_tiempo'){
                $carga = $this->carga_tiempo($id, $cliente, $inicio);
                $mesa = $modelos->consulta_mesa($id);
                $status_mesa = $modelos->consulta_tiempos_baraja($mesa);
            $tipo = $this->muestra_carga_tiempo($id, $cliente);
                $respuesta = array('status'=>$carga, 'id'=>$id, 'status_mesa'=>$status_mesa, 'tipo'=>$tipo['status'], 'html'=>$tipo['html'], 'cuenta'=>$tipo['cuenta']);
                echo json_encode($respuesta);
            }else if($funcion == 'actualiza_jugador_baraja'){
                $html = $directivas->actualiza_jugador_baraja($id);
                $respuesta = array('html'=>$html);
                echo json_encode($respuesta);
            }else if($funcion == 'actualiza_pago_mesa'){
                $html = $this->actualiza_pago_mesa($id);
                $respuesta = array('html'=>$html, 'id'=>'tiempo_modal_'.$id);
                echo json_encode($respuesta);
            }else if($funcion == 'carga_tiempo_mesa'){
                $registro = $modelos->crea_tiempo_cuenta($cliente, $id);
            $tipo = $this->muestra_carga_tiempo($cliente, $inicio);
                $respuesta = array('status'=>$registro, 'tipo'=>$tipo['status'], 'html'=>$tipo['html'], 'cuenta'=>$tipo['cuenta']);
                echo json_encode($respuesta);
            }
        }

}