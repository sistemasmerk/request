<?php

class productos{
    
    public function obten_productos(){
        $conexion = new Conexion();
        $conexion->selecciona_base_datos();
        $link = $conexion->link;
        
        $consulta = "SELECT *
                        FROM producto
                        WHERE status = 1 AND visible = 1
                        ORDER BY orden ASC";
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
}

