<?php

class Modelos{
    protected  $link;
    public $tabla;
    //public $directivas;
    
    public function __construct(){
        $conexion = new Conexion();
        $conexion->selecciona_base_datos();
        $this->link = $conexion->link;
    }
    
    public function ejecuta_consulta($consulta){
        $result = $this->link->query($consulta);    //echo"<pre> ++-+-+-+-+-"; print_r($new_array); echo"</pre>";//die;
        $n_registros = $result->num_rows;
        if($this->link->error){
                return array('mensaje'=>$this->link->error.' '.$consulta, 'error'=>True);
        }
        $new_array = array();
        while( $row = mysqli_fetch_assoc( $result)){
                $new_array[] = $row; 
        }
        return array('registros' => $new_array, 'n_registros' => $n_registros);

    }
    public function obten_productos($parametro=''){
        $conexion = new Conexion();
        $conexion->selecciona_base_datos();
        $link = $conexion->link;
        
        $consulta = "SELECT p.*, pr.precio
                        FROM producto p
                            LEFT OUTER JOIN precios pr ON p.producto_id = pr.producto_id 
                            LEFT OUTER JOIN inventario i ON p.producto_id = i.producto_id
                        WHERE p.status = 1 AND p.visible = 1
                        GROUP BY p.nombre
                        HAVING SUM(i.stock) > 0                        
                        ORDER BY p.orden ASC";
                      
        $result = $link->query($consulta);
        $n_registros = $result->num_rows;

        if($link->error){
            return array('mensaje'=>$link->error.' '.$consulta, 'error'=>True);
        }

        $new_array = array();
        while( $row = mysqli_fetch_assoc( $result)){
            $new_array[] = $row; 
        }
        return array('registros' => $new_array, 'n_registros' => $n_registros);
    }
    
    public function obten_productos_activos(){
        $conexion = new Conexion();
        $conexion->selecciona_base_datos();
        $link = $conexion->link;
        
        $consulta = "SELECT p.*
                        FROM producto p
                        WHERE p.status = 1 AND p.visible = 1                        
                        ORDER BY p.orden ASC";
  
        $result = $link->query($consulta);
        $n_registros = $result->num_rows;

        if($link->error){
            return array('mensaje'=>$link->error.' '.$consulta, 'error'=>True);
        }

        $new_array = array();
        while( $row = mysqli_fetch_assoc( $result)){
            $new_array[] = $row; 
        }
        return array('registros' => $new_array, 'n_registros' => $n_registros);
    }
    
    public function obten_mesas(){
        $conexion = new Conexion();
        $conexion->selecciona_base_datos();
        $link = $conexion->link;
        
        $consulta = "SELECT *, '' AS pagado
                        FROM mesa
                        WHERE status = 1 
                        ORDER BY orden ASC";
        $result = $this->ejecuta_consulta($consulta);
        $mesas = $result["registros"];
//echo"<pre> "; print_r($mesas  ); echo"</pre>";//die;        
        $query = " SELECT mesa_id
                    FROM tiempo
                    WHERE pagado = 0 AND fin IS NULL AND tiempo_id NOT IN(SELECT tiempo_id FROM tiempo_cuenta)";
        $res = $this->ejecuta_consulta($query);
        $tiempos = $res["registros"];
//echo" ---------------------------------------------------------------- <pre> "; print_r($tiempos  ); echo"</pre>";        
        // si en tabla tiempo hay registro donde el status de pagado esta en 0, pone el indicador del resultado $mesas como pagado = 1
        $cont = count($mesas);
        for($i=0; $i<$cont; $i++){
            foreach ($tiempos AS $tiempo){
                if($mesas[$i]["mesa_id"] == $tiempo["mesa_id"]){
                    $mesas[$i]["pagado"] = 'n';
                }
            }
        }
        
//echo"<pre> ----************"; print_r($mesas  ); echo"</pre>"; die;
        return $mesas;
    }
    public function registra_incio($mesa){
        $inicio = date("Y-m-d H:i:s");
        
        $existe = $this->registro_repetido($mesa);

        if(!$existe){
            $insert = "INSERT INTO TIEMPO (inicio, mesa_id) VALUES ('".$inicio."',".$mesa.");";
            
            $this->link->query($insert);
            if($this->link->error){
                return 0;
            }else{
                return 1;
            }
        }else{
            return 'Ya existe una mesa activa, no es posible tener 2 tiempos activos en la misma mesa';
        }
    }
    public function registra_fin($mesa, $entre, $parcialidades, $pagado, $tiempo_id){
        $fin = date("Y-m-d");
        $entre = $entre == ''? 1 : $entre;
        $parcialidades = $parcialidades == '' ? 0 : $parcialidades;
        $pagado = $pagado == '' ? 1 : $pagado;
        $existe = $this->registro_repetido($mesa);//agregar en la consulta los tiempos a cuenta
         
        if($existe){
            $calculo = $this->consulta_tiempos($mesa);
            $hora_fin = substr($calculo['fin'], 0, 8);
            $update = "UPDATE tiempo SET fin = '" . $fin . " " . $hora_fin . "', tiempo = '" . $calculo['tiempo'] . "', costo = " . $calculo['costo'] . ", pagado = " . $pagado . ", entre = " . $entre . ", parcialidades = " . $parcialidades . " WHERE tiempo_id = " . $tiempo_id ;
 //echo"<pre> --------+++++"; print_r($update  ); echo"</pre>"; die;      
            $this->link->query($update);

            if($this->link->error){
                return 0;
            }else{                    
                return 1;
            }
        }else{// no existe registro
            return 'No existe un tiempo activo para esta mesa.';
        }
    }
    
    public function registro_repetido($mesa){
        $consulta = " SELECT * 
                        FROM sab.tiempo 
                        WHERE pagado = 0 AND tiempo_id NOT IN(SELECT tiempo_id FROM tiempo_cuenta) AND mesa_id = " . $mesa;
        $result = $this->ejecuta_consulta($consulta);
        $registro = $result["registros"];
        return $registro;
    }
    
    public function tiempo_nocargado(){
        $consulta = "SELECT * 
                        FROM sab.tiempo 
                        WHERE pagado = 0 AND fin IS NOT NULL AND tiempo_id NOT IN(SELECT tiempo_id FROM tiempo_cuenta)";
        $result = $this->ejecuta_consulta($consulta);
        $registro = $result["registros"];
        return $registro;
    }
    
    public function consulta_tiempos($mesa){
        $controlador = new Controlador_Session();//%p
        
        $consulta = " SELECT p.precio, m.nombre, t.tiempo_id, t.inicio AS fecha_inicio, date_format(t.inicio, '%h:%i:%s %p' ) AS inicio, date_format(t.fin, '%h:%i:%s %p' ) AS fin, t.tiempo, t.costo, t.mesa_id, t.pagado
                        FROM precios p
                            LEFT OUTER JOIN juegos j ON p.precios_id = j.precio_id
                            LEFT OUTER JOIN mesa m ON j.juego_id = m.juego_id
                            LEFT OUTER JOIN tiempo t ON m.mesa_id = t.mesa_id
                        WHERE p.status = 1 AND j.status = 1 AND m.status = 1 AND t.pagado = 0 AND t.tiempo_id NOT IN(SELECT tiempo_id FROM tiempo_cuenta WHERE pagado = 0) AND fin IS NULL AND t.mesa_id = " . $mesa;
        $result = $this->ejecuta_consulta($consulta);
        $registro = $result["registros"];

        if($registro){
            $reg = $registro[0];

            $calculo = $controlador->calcula_tiempo($reg);
            
            //return json_encode($ok);
            //echo"<pre> --------+++++"; print_r($calculo  ); echo"</pre>";//die;

            return $calculo;
        }
    }
    
    public function consulta_tiempos_baraja($mesa){
        //$controlador = new Controlador_Session();
        
        $consulta = " SELECT p.precio, m.nombre, t.tiempo_id, t.inicio, t.fin, t.tiempo, t.costo, t.mesa_id, t.pagado, jb.nombre AS jugador
                        FROM precios p
                            LEFT OUTER JOIN juegos j ON p.precios_id = j.precio_id
                            LEFT OUTER JOIN mesa m ON j.juego_id = m.juego_id
                            LEFT OUTER JOIN tiempo t ON m.mesa_id = t.mesa_id
                            LEFT OUTER JOIN jugador_baraja jb ON t.tiempo_id = jb.tiempo_id
                        WHERE p.status = 1 AND j.status = 1 AND m.status = 1 AND t.pagado = 0 AND t.tiempo_id NOT IN(SELECT tiempo_id FROM tiempo_cuenta) AND t.mesa_id = " . $mesa . " ORDER BY jb.id ASC ";
        $result = $this->ejecuta_consulta($consulta);
        $registro = $result["registros"];

        if($registro){                        
            return $registro;
        }else{
            return 0;
        }
    }
    
    public function consulta_mesa($tiempo_id){
        $consulta = 'SELECT mesa_id FROM tiempo WHERE tiempo_id = ' . $tiempo_id;
        $result = $this->ejecuta_consulta($consulta);
        $registro = $result["registros"];
        if($registro){
            return $registro[0]['mesa_id'];
        }else{
            return 0;
        }        
    }
    
    public function guarda_baraja($cliente, $inicio, $id){
        
        $insert = "INSERT INTO tiempo (inicio, mesa_id) VALUES ('".$inicio."', ".$id.");";
            
        $this->link->query($insert);
        if($this->link->error){
            return array('mensaje'=>$this->link->error. ' ' . $insert, 'error'=>True);
        }else{
            $tiempo_id = $this->guarda_jugador($cliente, $id);
            return $tiempo_id;
        }
    }
    
    public function consulta_tiempo_baraja($id){
        $consulta = "SELECT * FROM tiempo WHERE pagado = 0 AND mesa_id = ".$id." ORDER BY 1 DESC LIMIT 1";
        $result = $this->ejecuta_consulta($consulta);
        $registro = $result["registros"][0]["tiempo_id"];
        return $registro;
    }
    
    public function obten_tiempo($id){
        $consulta = "SELECT * FROM tiempo WHERE tiempo_id = ".$id;
        $result = $this->ejecuta_consulta($consulta);
        $registro = $result["registros"][0];
        return $registro;
    }
    
    public function obten_tiempos_cuenta(){
        $consulta = 'SELECT t.*, tc.cuenta_id
                        FROM tiempo t
                                LEFT OUTER JOIN tiempo_cuenta tc ON t.tiempo_id = tc.tiempo_id
                        WHERE t.pagado = 0 AND t.tiempo_id IN(SELECT tiempo_id FROM tiempo_cuenta)';
        $result = $this->ejecuta_consulta($consulta);
        $registro = $result["registros"];
        return $registro;
    }
    
    public function guarda_jugador($cliente, $id){
        $tiempo_id = $this->consulta_tiempo_baraja($id);
        $insert_jugador = "INSERT INTO jugador_baraja (nombre, tiempo_id) VALUES ('".$cliente."', ".$tiempo_id.");";
        
        $this->link->query($insert_jugador);
        if($this->link->error){
            return array('mensaje'=>$this->link->error. ' ' . $insert, 'error'=>True);
        }else{
            return $tiempo_id;
        }
    }
    
    public function paga_jugador($id, $tiempo, $costo){
        $fin = $fin = date("Y-m-d H:i:s");
        
        $update = "UPDATE tiempo SET fin = '" . $fin ."', tiempo = '" . $tiempo . "', costo = " . $costo . ", pagado = 1 WHERE tiempo_id = " . $id . " ";
        //echo"<pre> ---11-----+++++"; print_r($update  ); echo"</pre>";    
        $this->link->query($update);

        if($this->link->error){
            return array('mensaje'=>$this->link->error, 'error'=>True);
        }else{                    
            return 1;
        }
        
    }
    
    public function obten_cuenta($cuenta_id){
        $consulta = "SELECT c.cuenta_id, c.nombre
                        FROM cuenta c
                                LEFT OUTER JOIN ventas v ON c.cuenta_id = v.cuenta_id
                        WHERE c.pagada = 0 AND credito = 0  AND c.cuenta_id = " . $cuenta_id;
        $result = $this->ejecuta_consulta($consulta);
        $registros = $result["registros"]; 
        return $registros;
    }
    
    public function obten_cuentas(){
        $consulta = " SELECT c.cuenta_id, c.nombre
                        FROM cuenta c
                                        LEFT OUTER JOIN ventas v ON c.cuenta_id = v.cuenta_id
                        WHERE c.pagada = 0 AND c.credito = 0 
                        GROUP BY c.nombre
                        HAVING sum(v.ventas_id) > 0
                                UNION
                        SELECT c.cuenta_id, c.nombre
                        FROM cuenta c
                                LEFT OUTER JOIN tiempo_cuenta tc ON c.cuenta_id = tc.cuenta_id
                                LEFT OUTER JOIN tiempo t ON tc.tiempo_id = t.tiempo_id
                        WHERE c.pagada = 0 AND c.credito = 0 AND t.pagado = 0 AND t.tiempo_id IN(SELECT tiempo_id FROM tiempo_cuenta) 
                        ORDER BY 1";
        $result = $this->ejecuta_consulta($consulta);
        $registros = $result["registros"]; 
        return $registros;
    }
    
    public function existe_cuenta($nombre){
        $consulta = "SELECT c.cuenta_id, c.nombre
                        FROM cuenta c
                                LEFT OUTER JOIN ventas v ON c.cuenta_id = v.cuenta_id
                        WHERE c.pagada = 0 AND c.credito = 0  AND c.nombre = '" . $nombre . "'";
        $result = $this->ejecuta_consulta($consulta);
        $registros = $result["registros"]; 
        return $registros;
    }
    
    public function obten_ventas($cuenta_id=''){
        $consulta = " SELECT v.ventas_id, v.cantidad, v.comentarios, v.cuenta_id,
                            p.imagen, p.nombre, (pr.precio * v.cantidad ) AS costo, date_format(v.fecha, '%h:%i:%s %p' ) as hora, 'venta' AS tipo, v.producto_id
                        FROM ventas v
                                LEFT OUTER JOIN cuenta c ON c.cuenta_id = v.cuenta_id
                                LEFT OUTER JOIN inventario i ON v.inventario_id = i.inventario_id
                                LEFT OUTER JOIN producto p ON i.producto_id = p.producto_id
                                LEFT OUTER JOIN precios pr ON p.producto_id = pr.producto_id
                        WHERE c.pagada = 0 AND c.credito = 0 AND v.status = 0 ";
        if($cuenta_id){
            $consulta .= " AND v.cuenta_id = " . $cuenta_id;
        }                        
        
        $consulta .=" UNION ALL
                        SELECT t.tiempo_id AS ventas_id, t.tiempo AS cantidad, m.nombre AS comentarios, tc.cuenta_id, '' AS imagen,
                            'Tiempo' AS nombre, IF(t.entre > 1, t.parcialidades, t.costo) AS costo, CONCAT(date_format(t.inicio, '%h:%i'), ' - ', date_format(t.fin, '%h:%i')) AS hora, 'tiempo' as tipo, '' as producto_id 
                        FROM tiempo t
                                LEFT OUTER JOIN tiempo_cuenta tc ON t.tiempo_id = tc.tiempo_id
                            LEFT OUTER JOIN mesa m ON t.mesa_id = m.mesa_id
                        WHERE tc.pagado = 0 AND t.tiempo_id IN(SELECT tiempo_id FROM tiempo_cuenta WHERE pagado = 0) ";
        
        if($cuenta_id){
            $consulta .= " AND tc.cuenta_id = " . $cuenta_id;
        }
    //echo"<pre> ---------"; print_r($consulta  ); echo"</pre>"; die;   
        $result = $this->ejecuta_consulta($consulta);
        $registros = $result["registros"];      
        return $registros;
    }
    
    public function obten_total_venta($cuenta){
        $consulta = "SELECT SUM(pr.precio * v.cantidad) AS total
                        FROM ventas v
                                LEFT OUTER JOIN cuenta c ON c.cuenta_id = v.cuenta_id
                            LEFT OUTER JOIN inventario i ON v.inventario_id = i.inventario_id
                                LEFT OUTER JOIN producto p ON i.producto_id = p.producto_id
                                LEFT OUTER JOIN precios pr ON p.producto_id = pr.producto_id
                        WHERE c.pagada = 0 AND c.credito = 0  AND v.cuenta_id = " . $cuenta;
        $result = $this->ejecuta_consulta($consulta);
        $registros = $result["registros"][0];
        
        $consulta2 = 'SELECT SUM(IF(t.entre > 1, t.parcialidades, t.costo)) total
                        FROM tiempo t
                                LEFT OUTER JOIN tiempo_cuenta tc ON t.tiempo_id = tc.tiempo_id
                            LEFT OUTER JOIN cuenta c ON tc.cuenta_id = c.cuenta_id
                        WHERE tc.pagado = 0 AND c.pagada = 0 AND c.credito = 0 AND tc.cuenta_id = ' . $cuenta;
        $result2 = $this->ejecuta_consulta($consulta2);
        $registros2 = $result2["registros"][0];
        
        $consulta3 = ' SELECT SUM(cantidad) AS total
                        FROM ventas 
                        WHERE producto_id = 13 AND status = 0 AND cuenta_id = ' . $cuenta;
        $result2 = $this->ejecuta_consulta($consulta3);
        $registros3 = $result2["registros"][0];
        
//echo"<pre> ---------"; print_r($consulta  ); echo"</pre>"; die;        
        $total = $registros['total'] + $registros2['total'] + $registros3['total'];
        return $total;                
    }
    
    public function carga_ventas($producto, $cantidad, $cuenta_id, $comentarios){
        $fecha = date("Y-m-d H:i:s");
        $status = 0;
        
        $inventarios = $this->obten_inventarios($producto, $cantidad);
        
        if($inventarios){
            foreach ($inventarios AS $inventario){
                if(!array_key_exists('faltante', $inventario)){

                    if($cuenta_id == 'contado'){
                        $insert = "INSERT INTO ventas (cantidad, fecha, producto_id, contado, status, inventario_id, comentarios) VALUES (".$inventario['cantidad'].",'".$fecha."', ".$producto.", 1, 1, ".$inventario['inventario_id'].", '".$comentarios."');";
                    }else{
                        $insert = "INSERT INTO ventas (cantidad, fecha, producto_id, cuenta_id, inventario_id, comentarios) VALUES (".$inventario['cantidad'].",'".$fecha."', ".$producto.", ".$cuenta_id.", ".$inventario['inventario_id'].", '".$comentarios."');";
                    }
                    
                    $this->link->query($insert);
//echo"<pre> ---11-----+++++"; print_r($insert  ); echo"</pre>"; die;
                    if($this->link->error){
                        return array('mensaje'=>$this->link->error, 'error'=>True);
                    }else{   
                        $this->actualiza_inventario($inventario['inventario_id'], $inventario['stock'], $inventario['status'], $fecha);
                        $status = 'ok';
                    }
                }else{
                    //ya no hay inventarios disponibles
                    $status = $inventario['faltante'] . " ". $inventario['nombre'] . "_" . $inventario['producto_id'];
                }
            }
            return $status;
        }else{
            return 'productos';
        }
    }
    
    public function carga_ventas_dinero($producto, $cantidad, $cuenta_id){
        $fecha = date("Y-m-d H:i:s");
        $insert = "INSERT INTO ventas (cantidad, fecha, producto_id, cuenta_id) VALUES (".$cantidad.",'".$fecha."', 13, ".$cuenta_id.");";
        $this->link->query($insert);

        if($this->link->error){
            return array('mensaje'=>$this->link->error, 'error'=>True);
        }else{   
            $status = 'ok';
        }
        return $status;
    }
    
    public function obten_inventarios($producto, $cantidad){
        $consulta = " SELECT i.*, p.nombre
                        FROM inventario i 
                         LEFT OUTER JOIN producto p ON i.producto_id = p.producto_id
                        WHERE i.stock > 0 AND i.status = 1 AND i.producto_id = " . $producto;
        $result = $this->ejecuta_consulta($consulta);
        $registros = $result["registros"];
        
        $inventarios = array();
        if($registros){    
            if($registros[0]['stock'] >= $cantidad){// validar trigger cuando el inventario llegue a cero que le cambie el status
                $inventarios[0]['inventario_id'] = $registros[0]['inventario_id'];
                $inventarios[0]['cantidad'] = $cantidad;
                $inventarios[0]['stock'] = $registros[0]['stock'] - $cantidad;
                if($registros[0]['stock'] == $cantidad){
                    $inventarios[0]['status'] = 0;
                }else{
                    $inventarios[0]['status'] = 1;
                }
                //echo"<pre> +++++"; print_r($inventarios  ); echo"</pre>";
                return $inventarios;
            }else{
                $contador = 0;
                $acumulado = 0;
                $faltante = 0;

                while($acumulado < $cantidad){
                    if(array_key_exists($contador, $registros)){
                        if($registros[$contador]['stock'] < $cantidad){
                            $inventarios[$contador]['inventario_id'] = $registros[$contador]['inventario_id'];
                            $inventarios[$contador]['cantidad'] = $registros[$contador]['stock'];
                            $acumulado = $acumulado + $registros[$contador]['stock'];
                            $inventarios[$contador]['stock'] = 0;
                            $inventarios[$contador]['status'] = 0;
                        }else{
                            $inventarios[$contador]['inventario_id'] = $registros[$contador]['inventario_id'];
                            $inventarios[$contador]['cantidad'] = $cantidad - $acumulado;
                            $inventarios[$contador]['stock'] = $registros[$contador]['stock'] - $acumulado;
                            $inventarios[$contador]['status'] = 1;
                            $acumulado = $acumulado + ($cantidad - $acumulado);

                            //echo "))))))))))))))))))))))))) " . $acumulado;
                        }
                        $contador++;
                    }else{
                        $faltante = ($cantidad - $acumulado);
                        $inventarios[$contador]['faltante'] = $faltante;
                        $inventarios[$contador]['nombre'] = $registros[$contador-1]['nombre'];
                        $inventarios[$contador]['producto_id'] = $registros[$contador-1]['producto_id'];
                        break;
                    }

                }
                //echo"<pre>$faltante ///////// "; print_r($inventarios  ); echo"</pre>";
                return $inventarios;
            }

            //echo"<pre> ---11-----+++++"; print_r($registros  ); echo"</pre>";
        }else{
            return false;
        }    
    }
    
    public function actualiza_inventario($inventario, $stock, $status, $fecha){
        //$update = "UPDATE tiempo SET fin = '" . $fin . " " . $calculo['fin'] . "', tiempo = '" . $calculo['tiempo'] . "', costo = " . $calculo['costo'] . ", pagado = 1 WHERE mesa_id = " . $calculo['mesa_id'] . " AND pagado = 0";
        $update = "UPDATE inventario SET stock = " . $stock . ", status = " . $status . ", fecha_modifica = '".$fecha."' WHERE inventario_id = " . $inventario ;
        //echo"<pre> --------+++++"; print_r($update  ); echo"</pre>";    
        $this->link->query($update);

        if($this->link->error){
            return array('mensaje'=>$this->link->error, 'error'=>True);
        }else{                    
            return array('mensaje'=>'Registro guardado con éxito', 'error'=>False);
        }
    }
    
    public function pagar_cuenta($id){
        $update = 'UPDATE cuenta SET pagada = 1 WHERE cuenta_id = ' . $id;
                
        $this->link->query($update);
        if($this->link->error){
            return 0;
        }else{
            return 1;
        }
    }
    
    public function pagar_ventas($id, $ventas){
        $update = 'UPDATE ventas SET status = 1 WHERE cuenta_id = '.$id.' AND ventas_id IN(' . $ventas . ')';
        
        $this->link->query($update);
        if($this->link->error){
            return 0;
        }else{
            return 1;
        }
    }
    
    public function pagar_tiempos($id, $tiempos){
        $update = 'UPDATE tiempo_cuenta SET pagado = 1 WHERE tiempo_id IN('. $tiempos .') AND cuenta_id = ' . $id;
        
        $this->link->query($update);
        if($this->link->error){
            return 0;
        }else{
            return 1;
        }
    }
    
    public function actualiza_tiempo($id, $costo, $tiempo){
        $fecha_fin = new DateTime("now");
        $fin = $fecha_fin->format("Y-m-d H:i:s");
        $update = 'UPDATE tiempo SET fin = "'. $fin .'", costo = '. $costo .', tiempo = "'. $tiempo .'" WHERE tiempo_id = ' . $id;
    //echo $update;    die;
        $this->link->query($update);
        if($this->link->error){
            return 0;
        }else{
            return 1;
        }
    }
    
    public function nueva_cuenta($nombre){
        $existe = $this->obten_id($nombre); 
        if(!$existe){
            $insert = "INSERT INTO cuenta (nombre) VALUES ('".$nombre."');";
    //echo"<pre>"; print_r($insert  ); echo"</pre>";die;        
            $this->link->query($insert);
            if($this->link->error){
                return 0;
            }else{
                return 1;
            }
        }else{
            return 'Ya existe una cuenta con el mismo nombre';
        }
    }
    
    public function crea_tiempo_cuenta($tiempo, $cuenta){
        $insert = 'INSERT INTO tiempo_cuenta (tiempo_id, cuenta_id) VALUES ('.$tiempo.', ' . $cuenta . ')';
    //echo"<pre>"; print_r($insert  ); echo"</pre>";die;    
        $this->link->query($insert);
        if($this->link->error){
            return 0;
        }else{
            return 1;
        }
    }
    
    public function obten_id($nombre){
        $consulta = 'SELECT cuenta_id FROM cuenta WHERE pagada = 0 AND credito = 0 AND nombre = "' . $nombre . '"';
        
        $result = $this->ejecuta_consulta($consulta);
        $registros = $result["registros"];
        
        if($registros){
            return $registros[0]['cuenta_id'];
        }else{
            return $registros;
        }
    }
    
    public function obten_status_tiempos($tiempos_id){
        $consulta = 'SELECT * FROM tiempo_cuenta WHERE tiempo_id IN('.$tiempos_id.')';
        $result = $this->ejecuta_consulta($consulta);
        $registros = $result["registros"];
        return $registros;
    }
    
    public function actualiza_status_tiempo($tiempo_id){
        $update = 'UPDATE tiempo SET pagado = 1 WHERE tiempo_id = ' . $tiempo_id;
        $this->link->query($update);
        if($this->link->error){
            return 0;
        }else{
            return 1;
        }
    }
    
    public function obten_producto_id($nombre){
        $consulta = 'SELECT producto_id FROM producto WHERE nombre = "'.$nombre.'" ORDER BY 1 DESC LIMIT 1';
        $result = $this->ejecuta_consulta($consulta);
        $registros = $result["registros"];
        return $registros;
    }
    
    public function alta_bd($registro, $tabla){	
        $campos = "";
        $valores = "";

        foreach ($registro as $campo => $value) {
            if($campo == 'status'){
                if($value == 'on'){
                    $value = 1;
                }else{
                    $value = 0;
                }
            }
            if($campo == 'visible'){
                if($value == 'visible'){
                    $value = 1;
                }else{
                    $value = 0;
                }
            }

            if($tabla == 'productos'){
                $tabla = 'producto';
            }
            
            $campo = addslashes($campo);
            $value = addslashes($value);
            $campos .= $campos == ""?"$campo":",$campo";//echo"-->" . $campos . "<br>";
            $valores .= $valores == ""?"'$value'":",'$value'";
        }

        $consulta_insercion = "INSERT INTO ". $tabla." (".$campos.") VALUES (".$valores.")";
//echo"<pre>"; print_r($consulta_insercion  ); echo"</pre>";die;
        $this->link->query($consulta_insercion);
        if($this->link->error){
                return array('mensaje'=>$this->link->error.' '.$consulta_insercion, 'error'=>True);
        }
        else{
                $registro_id = $this->link->insert_id;
                return array(
                        'mensaje'=>'Registro insertado con éxito', 'error'=>False, 'registro_id'=>$registro_id);
        }
    }
    
    public function llena_select($tabla){
        $registros = '';
        if($tabla == 'inventario'){
            $registros = $this->llena_select_inventario();
        }else if($tabla == 'precios'){
            $registros = $this->llena_select_precios();
        }
        return $registros;
    }
    
    public function llena_select_inventario(){
        $consulta = 'SELECT p.producto_id, p.nombre
                    FROM producto p
                            LEFT OUTER JOIN precios pr ON p.producto_id = pr.producto_id	
                    WHERE p.status = 1 AND p.visible = 1
                    GROUP BY p.nombre
                    HAVING SUM(pr.precio) > 0
                    ORDER BY p.orden ASC';
        $result = $this->ejecuta_consulta($consulta);
        $registros = $result["registros"];
        return $registros;
    }
    
    public function llena_select_precios(){
        $consulta = 'SELECT p.producto_id, p.nombre
                    FROM producto p	
                    WHERE p.status = 1
                    ORDER BY p.orden ASC';
        $result = $this->ejecuta_consulta($consulta);
        $registros = $result["registros"];
        return $registros;
    }
    
    public function cambia_status_precio($producto_id){
        $consulta = 'SELECT COUNT(*) FROM precios WHERE status = 1 AND producto_id = ' . $producto_id;
        $result = $this->ejecuta_consulta($consulta);
 //echo"<pre>"; print_r($result); echo"</pre>"; die;        
        if($result){
            $update = 'UPDATE precios SET status = 0 WHERE producto_id = ' . $producto_id;
            $this->link->query($update);
            if($this->link->error){
                return 0;
            }else{
                return 1;
            }
        }else{
            return 1;
        }
    }
    
    public function obten_registros($tabla){
        $tabla = $tabla == 'productos' ? 'producto' : $tabla;
        
        if($tabla == 'producto'){
            $consulta = 'SELECT * FROM producto ORDER BY orden ASC';
        }else if($tabla == 'precios'){
            $consulta = 'SELECT * FROM producto p LEFT OUTER JOIN precios pr ON p.producto_id = pr.producto_id WHERE p.status = 1 ORDER BY p.orden ASC';
        }else if($tabla == 'inventario'){
            $consulta = 'SELECT *, DATE_FORMAT(i.fecha_alta, "%d/%m/%Y") AS fecha_captura
                            FROM producto p
                                    LEFT OUTER JOIN inventario i ON p.producto_id = i.producto_id
                            WHERE i.stock > 0 AND i.status = 1
                            ORDER BY p.orden, p.nombre , i.fecha_alta ASC';
        }
        else{
            $consulta = 'SELECT * FROM ' . $tabla;
        }
        
        $result = $this->ejecuta_consulta($consulta);
        return $result;
    }
    
    public function desactiva_bd($tabla, $id){
        $tabla = $tabla == 'productos' ? 'producto' : $tabla;
        $this->link->query("UPDATE $tabla SET status = '0' WHERE ".$tabla."_id = $id");
            
        if($this->link->error){
            return array('mensaje'=>$this->link->error, 'error'=>True);
        }
        else{
            $registro_id = $id;
            return array('mensaje'=>'Registro desactivado con éxito', 'error'=>False, 'registro_id'=>$registro_id);
        }
    }
    
    public function activa_bd($tabla, $id){
        $tabla = $tabla == 'productos' ? 'producto' : $tabla;
        $this->link->query("UPDATE $tabla SET status = '1' WHERE ".$tabla."_id = $id");
            
        if($this->link->error){
            return array('mensaje'=>$this->link->error, 'error'=>True);
        }
        else{
            $registro_id = $id;
            return array('mensaje'=>'Registro activado con éxito', 'error'=>False, 'registro_id'=>$registro_id);
        }
    }
    
    public function elimina_bd($tabla, $id){ 
        $tabla = $tabla == 'productos' ? 'producto' : $tabla;
        $consulta = "DELETE FROM ".$tabla. " WHERE ".$tabla."_id = ".$id;

        $this->link->query($consulta);
        if($this->link->error){
            return array('mensaje'=>$this->link->error.' '.$consulta, 'error'=>True);
        }
        else{
            $registro_id = $this->link->insert_id;
            return array('mensaje'=>'Registro eliminado con éxito', 'error'=>False, 'registro_id'=>$registro_id);
        }
    }
    
    public function obten_por_id($tabla, $id){
        $tabla = $tabla == 'productos' ? 'producto' : $tabla;
        
        $and = '';
        if($tabla == 'precios'){
            $and = ' AND status = 1 ';
        }
        $sql = new consultas_base();
        $consulta = $sql->genera_consulta_base($tabla);
        $where = " WHERE $tabla".".".$tabla."_id = $id $and ";
        $consulta = $consulta.$where;                                       //echo"<pre>"; print_r($consulta); echo"</pre>";die;
        $result = $this->ejecuta_consulta($consulta);
        return $result;
    }
    
    public function modifica_bd($registro, $tabla, $id){            
        $campos = "";
        $and = '';
        $tabla = $tabla == 'productos' ? 'producto' : $tabla;
        if($tabla == 'precios'){
            $fecha = date("Y-m-d H:i:s");
            $registro['fecha'] = $fecha;
            $and = ' AND status = 1 ';
        }
    //echo"<pre>"; print_r($registro); echo"</pre>";die;
        foreach ($registro as $campo => $value) {
            $visible = "";
            if($tabla == 'cat_sis_accion'){
                if($campo == 'visible'){
                    $visible = " , visible = '1' ";
                    continue;
                }else{
                    $visible = " , visible = '0' ";
                }
            }

            $campo = addslashes($campo);
            $value = addslashes($value);
            $campos .= $campos == ""?"$campo = '$value'":", $campo = '$value'";
        }
            
        $consulta = "UPDATE ". $tabla." SET ".$campos." $visible WHERE ". $tabla."_id = $id $and";

        $this->link->query($consulta);
        if($this->link->error){
            return array('mensaje'=>$this->link->error.' '.$consulta, 'error'=>True);
        }
        else{
            $registro_id = $id;
            return array('mensaje'=>'Registro modificado con éxito', 'error'=>False, 'registro_id'=>$registro_id);
        } 
    }
    
    public function obten_apertura_cierre($tipo){
        $consulta = "SELECT pr.producto_id, c.cantidad, p.nombre, p.imagen, pr.precio, c.tipo 
                        FROM caja c
                                LEFT OUTER JOIN producto p ON c.producto_id = p.producto_id
                            LEFT OUTER JOIN precios pr ON p.producto_id = pr.producto_id
                        WHERE c.tipo = '".$tipo."' AND pr.status = 1 AND p.status = 1 
                            AND c.fecha BETWEEN (SELECT DATE_FORMAT(fecha, '%Y-%m-%d 00:00:00') fecha FROM caja WHERE tipo = 'A' ORDER BY 1 DESC LIMIT 1) AND current_timestamp()";
        $result = $this->ejecuta_consulta($consulta);
        $resultado = $result['registros'];
        return $resultado;
    }
    
    public function obten_ventas_cierre(){
        $consulta = "SELECT producto_id, SUM(cantidad) cantidad
                        FROM ventas
                        WHERE fecha BETWEEN (SELECT DATE_FORMAT(fecha, '%Y-%m-%d 00:00:00') fecha FROM caja WHERE tipo = 'A' ORDER BY 1 DESC LIMIT 1) AND current_timestamp()
                        GROUP BY producto_id";
        $result = $this->ejecuta_consulta($consulta);
        $resultado = $result['registros'];
        return $resultado;
    }
    
    public function obten_movimiento($tipo){
        $consulta = "SELECT * FROM cat_movimiento WHERE status = 1 AND tipo = " . $tipo;
        $result = $this->ejecuta_consulta($consulta);
        $resultado = $result['registros'];
        return $resultado;
    }
    
}

