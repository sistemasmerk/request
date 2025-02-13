<?php

class consultas_base{
    public $link;
    
    public function genera_consulta_base($tabla){  
        $columnas = $this->obten_columnas_completas($tabla);
        $tablas = $this->obten_tablas_completas($tabla);        //print_r($columnas);
        $consulta = "SELECT $columnas FROM $tablas";
        return $consulta;
    }
    private function obten_columnas_completas($tabla){//echo"--> " . $tabla . "<--- <br><br>";
        $columnas = "";
        if($tabla == 'sis_accion'){
            $columnas = $columnas.$this->genera_columnas_consulta('sis_submenu')." , ";
            $columnas = $columnas.$this->genera_columnas_consulta('sis_menu')." , ";
            $columnas = $columnas.$this->genera_columnas_consulta('sis_accion');
        }
        
        
        if($tabla == 'sis_grupo'){
            $columnas = $columnas.$this->genera_columnas_consulta('sis_grupo');
        }
                
        if($tabla == 'sis_submenu'){
            $columnas = $columnas.$this->genera_columnas_consulta('sis_submenu')." , ";
            $columnas = $columnas.$this->genera_columnas_consulta('sis_menu');
        }
        if($tabla == 'sis_usuario'){
            $columnas = $columnas.$this->genera_columnas_consulta('sis_usuario')." , ";
            $columnas = $columnas.$this->genera_columnas_consulta('sis_grupo');
        }
        if($tabla == 'sis_accion_grupo'){
            $columnas = $columnas.$this->genera_columnas_consulta('sis_accion_grupo')." , ";
            $columnas = $columnas.$this->genera_columnas_consulta('sis_accion')." , ";
            $columnas = $columnas.$this->genera_columnas_consulta('sis_grupo')." , ";
            $columnas = $columnas.$this->genera_columnas_consulta('sis_submenu')." , ";
            $columnas = $columnas.$this->genera_columnas_consulta('sis_menu');
        }
        if($tabla == 'sis_menu'){
            $columnas = $columnas.$this->genera_columnas_consulta('sis_menu');
        }    
        

        return $columnas;
    }
    private function obten_tablas_completas($tabla){
        $tablas = "";
        if($tabla == 'sis_accion'){
            $tablas = $tablas.' sis_accion AS sis_accion';
            $tablas = $tablas.' INNER JOIN sis_submenu AS sis_submenu ON sis_submenu.id_sis_submenu = sis_accion.sis_submenu_id';
            $tablas = $tablas.' INNER JOIN sis_menu AS sis_menu ON sis_menu.id_sis_menu = sis_submenu.sis_menu_id';
        }
        if($tabla == 'sis_grupo'){
            $tablas = $tablas.' sis_grupo AS sis_grupo';
        }        
        if($tabla == 'sis_submenu'){
            $tablas = $tablas.' sis_submenu AS sis_submenu';
            $tablas = $tablas.' INNER JOIN sis_menu AS sis_menu ON sis_menu.id_sis_menu = sis_submenu.sis_menu_id';
        }
        if($tabla == 'sis_menu'){
            $tablas = $tablas.' sis_menu AS sis_menu';
        }
        if($tabla == 'sis_usuario'){
            $tablas = $tablas.' sis_usuario AS sis_usuario';
            $tablas = $tablas.' INNER JOIN sis_grupo AS sis_grupo ON sis_grupo.id_sis_grupo = sis_usuario.grupo_id';
        }
        if($tabla == 'sis_accion_grupo'){
            $tablas = $tablas.' sis_accion_grupo AS sis_accion_grupo';
            $tablas = $tablas.' INNER JOIN sis_accion AS sis_accion ON sis_accion.id_sis_accion = sis_accion_grupo.sis_accion_id';
            $tablas = $tablas.' INNER JOIN sis_grupo AS sis_grupo ON sis_grupo.id_sis_grupo = sis_accion_grupo.sis_grupo_id';
            $tablas = $tablas.' INNER JOIN sis_submenu AS sis_submenu ON sis_submenu.id_sis_submenu = sis_accion.sis_submenu_id';
            $tablas = $tablas.' INNER JOIN sis_menu AS sis_menu ON sis_menu.id_sis_menu = sis_submenu.sis_menu_id';
        }
                
        return $tablas;
    }
    
    private function genera_columnas_consulta($tabla){
        $columnas_parseadas = $this->obten_columnas($tabla);
        $columnas_sql = "";
        
//print_r($columnas_parseadas); echo")<br>"; die;
        foreach($columnas_parseadas as $columna_parseada){
            $columnas_sql .= $columnas_sql == ""?"$tabla.$columna_parseada AS $tabla"."_$columna_parseada":",$tabla.$columna_parseada AS $tabla"."_$columna_parseada";
            //print_r($columnas_sql); //echo")))))))<br>"; //die;
        }
                                                                      // print_r($columnas_sql); echo")))))))<br>"; //die;
        return $columnas_sql;
    }
    private function obten_columnas($tabla){
        $modelos = new modelos();
        $consulta = "DESCRIBE $tabla";  
        $result = $modelos->ejecuta_consulta($consulta);
        $columnas = $result['registros'];
        $columnas_parseadas = [];
        foreach($columnas as $columna ){
            foreach($columna as $campo=>$atributo){
                if($campo == 'Field'){
                    $columnas_parseadas[] = $atributo;
                }
            }
        } //echo"</pre>"; print_r($columnas_parseadas); echo"</pre>"; die;
        return $columnas_parseadas;
    }
}