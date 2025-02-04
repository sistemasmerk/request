<?php

class Menu extends Modelos{

	public function obten_menu_permitido(){
            $conexion = new Conexion();
            $conexion->selecciona_base_datos();
            $link = $conexion->link;	

            $grupo_id = $_SESSION['grupo_id'];	

            $consulta = "SELECT 
                            menu.id_sis_menu AS id ,
                            menu.icono AS icono,
                            menu.nombre AS descripcion
                        FROM sis_menu AS menu
                            INNER JOIN sis_submenu AS seccion_menu ON seccion_menu.sis_menu_id = menu.id_sis_menu
                            INNER JOIN sis_accion AS accion ON accion.sis_submenu_id = seccion_menu.id_sis_submenu
                            INNER JOIN sis_accion_grupo AS permiso ON permiso.sis_accion_id = accion.id_sis_accion
                            INNER JOIN sis_grupo AS grupo ON grupo.id_sis_grupo = permiso.sis_grupo_id
                        WHERE 
                            menu.status = 1
                            AND seccion_menu.status = 1 
                            AND accion.status = 1 
                            AND grupo.status = 1 
                            AND permiso.sis_grupo_id = $grupo_id
                            AND accion.visible = 1                    
                        GROUP BY menu.id_sis_menu
                        ORDER BY menu.orden
            ";
        //echo"<pre>"; print_r( $_SESSION); echo"</pre>";die;
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
        
        public function obten_submenu_permitido($menu_id){
                $conexion = new Conexion();
                $conexion->selecciona_base_datos();
                $link = $conexion->link;	
                
                $grupo_id = $_SESSION['grupo_id'];

                $consulta = "SELECT 
                		seccion_menu.id AS id ,
                		seccion_menu.icono AS icono,
                		seccion_menu.nombre AS descripcion, menu.nombre AS seccion
                            FROM cat_sis_menu AS menu 
                                INNER JOIN cat_sis_submenu AS seccion_menu ON menu.id = seccion_menu.menu_id
                                INNER JOIN cat_sis_accion AS accion ON accion.submenu_id = seccion_menu.id
                                INNER JOIN cat_sis_accion_grupo AS permiso ON permiso.accion_id = accion.id
                                INNER JOIN cat_sis_grupo AS grupo ON grupo.id = permiso.grupo_id
                            WHERE 
                                    seccion_menu.status = 1 
                                    AND accion.status = 1 
                                    AND grupo.status = 1 
                                    AND permiso.grupo_id = $grupo_id AND seccion_menu.menu_id = $menu_id
                                    AND accion.visible = 1
                            GROUP BY seccion_menu.id
                ";
        //echo"<pre>"; print_r($consulta); echo"</pre>";die;
        //echo"--->" . $consulta . "+++ <br>";
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

?>