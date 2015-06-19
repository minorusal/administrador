<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
	if(!function_exists('array_2_string_format')){
		function array_2_string_format($input = array(), $separator = "=", $glue = " "){
			if(!empty($input)){
				$output = implode($glue, array_map(function ($v, $k) { return sprintf("%s?'%s'" , $k, $v); }, $input, array_keys($input)));
				$output = str_replace('?',$separator,$output);
			}else{
				$output = '';
			}
			return $output ;
		}
	}

	/**
	*
	**/
function Verificar_Horarios_Servicios_VENTAS($parametros,$id_sucursal){
	$parametros = explode('|', $parametros);
	$COMIENZO   = $parametros[1];
	$FIN        = $parametros[2];
	if(($COMIENZO=='')||($FIN=='')){
       $__RESULTADO = '2|'.VISTA_mensajes('INFO','CHICO_BOTON','VHSV_01:<br>'.lenguaje('T_AV',28,DICCIONARIO),'20');
   }else{
   	$__INICIO    =  explode(':', $COMIENZO);
	$__TERMINO   =  explode(':', $FIN);
	$__MKINICIO  =  mktime($__INICIO[0],  $__INICIO[1],  $__INICIO[2],0,0,0);
	$__MKTERMINO =         mktime($__TERMINO[0], $__TERMINO[1], $__TERMINO[2],0,0,0);
	if($__MKINICIO==$__MKTERMINO){
		$__MODIFICACION = Modificar_Horario_Servicio_VENTAS(null,null,'HorIncorrect|'.$parametros[0].'|');
		$__UPDATE =  explode('!', $__MODIFICACION);
		if($__UPDATE[0]=='1'){
			$__RESULTADO= '1|'.VISTA_mensajes('WARNING','CHICO_BOTON','VHSV_01:<br>'.lenguaje('T_AV',29,DICCIONARIO),'20');
		}else{
			$__RESULTADO = '1|'.VISTA_mensajes('ERROR','CHICO_BOTON','VHSV_01:<br>'.$__UPDATE[0],'20');
		}
	}else{
		if($__MKINICIO>$__MKTERMINO){
			$__MODIFICACION = Modificar_Horario_Servicio_VENTAS(null,null,'HorIncorrect|'.$parametros[0].'|');
			$__UPDATE =  explode('!', $__MODIFICACION);
			if($__UPDATE[0]=='1'){
				$__RESULTADO = '1|'.VISTA_mensajes('WARNING','CHICO_BOTON','VHSV_02:<br>'.lenguaje('T_AV',30,DICCIONARIO),'20');
			}else{
				$__RESULTADO = '1|'.VISTA_mensajes('ERROR','CHICO_BOTON','VHSV_02:<br>'.$__UPDATE[0],'20');
			}
		}else{
			$__HORARIOS = Consulta_Horarios_Servicio_x_ID_VENTAS($id_sucursal,$parametros[0]);
			$__COUNT = 0;
			if(is_array($__HORARIOS)){
				for($i=0;$i<=count($__HORARIOS)-1;$i++){
					if(Verifica_Rengo($__HORARIOS[$i]['h_inicio'], $__HORARIOS[$i]['h_fin'], $COMIENZO)){
						$__COUNT++;
					}
					if(Verifica_Rengo($__HORARIOS[$i]['h_inicio'], $__HORARIOS[$i]['h_fin'], $FIN)){
						$__COUNT++;
					}
				}
              if($__COUNT>0){
				$__MODIFICACION = Modificar_Horario_Servicio_VENTAS(null,null,'HorIncorrect|'.$parametros[0].'|');
				$__UPDATE =  explode('!', $__MODIFICACION);
				if($__UPDATE[0]=='1'){
					$__RESULTADO = '1|'.VISTA_mensajes('WARNING','CHICO_BOTON','VHSV_03:<br>'.lenguaje('T_AV',31,DICCIONARIO),'20');
				}else{
					$__RESULTADO = '1|'.VISTA_mensajes('ERROR','CHICO_BOTON','VHSV_03:<br>'.$__UPDATE[0],'20');
				}
              }else{
				$__MODIFICACION = Modificar_Horario_Servicio_VENTAS(null,null,'HorCorrect|'.$parametros[0].'|'.$COMIENZO.'|'.$FIN);
				$__UPDATE =  explode('!', $__MODIFICACION);
                if($__UPDATE[0]=='1'){
                    $__RESULTADO = '1|'.VISTA_mensajes('WARNING','CHICO_BOTON','VHSV_04:<br>'.lenguaje('T_AV',31,DICCIONARIO),'20');
              	}else{
                    $__RESULTADO = '1|'.VISTA_mensajes('ERROR','CHICO_BOTON','VHSV_04:<br>'.$__UPDATE[0],'20');
              	}
              }
			}
		}else{
			if(is_null($__HORARIOS)){
			$__MODIFICACION = Modificar_Horario_Servicio_VENTAS(null,null,'HorCorrect|'.$parametros[0].'|'.$COMIENZO.'|'.$FIN);
			$__UPDATE =  explode('!', $__MODIFICACION);
			if($__UPDATE[0]=='1'){
				$__RESULTADO = '0|'.VISTA_mensajes('BIEN','CHICO_BOTON','VHSV_02:<br>'.lenguaje('T_AV',32,DICCIONARIO),'20');
			}else{
				$__RESULTADO = '1|'.VISTA_mensajes('ERROR','CHICO_BOTON','VHSV_06:<br>'.$__UPDATE[0],'20');
			}
		}else{
		$__RESULTADO = '1|'.VISTA_mensajes('ERROR','CHICO_BOTON','VHSV_07:<br>'.$__HORARIOS,'20');
		}
		}
	}
   }
                   return $__RESULTADO;
}




    /**
	* imprime un arreglo formateado para debug
	* y detiene la ejecucion del script
	* @return array $array
	*/
	if(!function_exists('print_debug')){
		function print_debug($array){
			echo '<pre>';
			print_r($array);
			echo '</pre>';
			die();
		}
	}


	if(!function_exists('time_to_decimal')){
		function time_to_decimal($time){
			$horaArray = split(":",$time);
			$horaDecimal = $horaArray[0]+($horaArray[1]/60);
			return $horaDecimal;
		}
	}

	if(!function_exists('dump_var')){
		function dump_var($variable,$tipo=0){
			echo "<pre>";
			if(!$tipo){ print_r($variable); }else{var_dump($variable);}
			echo "</pre>";
			die();
		}
	}

	if(!function_exists('imagebmp')){
		function imagebmp($tipo='jpg',$imagesource='',$imagebmp='new.bmp'){
	    // Conviete imagen de JPG a BMP
			switch ($tipo) {
				case 'jpg': $im = imagecreatefromjpeg($imagesource); break;	
				case 'png': $im = imagecreatefrompng($imagesource); break;
				case 'gif': $im = imagecreatefromgif($imagesource); break;
			}
	        if (!$im) return false;
	        $w = imagesx($im);
	        $h = imagesy($im);
	        $result = '';
	        if (!imageistruecolor($im)) {
	            $tmp = imagecreatetruecolor($w, $h);
	            imagecopy($tmp, $im, 0, 0, 0, 0, $w, $h);
	            imagedestroy($im);
	            $im = & $tmp;
	        }
	        $biBPLine = $w * 3;
	        $biStride = ($biBPLine + 3) & ~3;
	        $biSizeImage = $biStride * $h;
	        $bfOffBits = 54;
	        $bfSize = $bfOffBits + $biSizeImage;
	        $result .= substr('BM', 0, 2);
	        $result .=  pack ('VvvV', $bfSize, 0, 0, $bfOffBits);
	        $result .= pack ('VVVvvVVVVVV', 40, $w, $h, 1, 24, 0, $biSizeImage, 0, 0, 0, 0);
	        $numpad = $biStride - $biBPLine;
	        for ($y = $h - 1; $y >= 0; --$y) {
	            for ($x = 0; $x < $w; ++$x) {
	                $col = imagecolorat ($im, $x, $y);
	                $result .=  substr(pack ('V', $col), 0, 3);
	            }
	            for ($i = 0; $i < $numpad; ++$i)
	                $result .= pack ('C', 0);
	        }
	        if($imagebmp==""){
	            echo $result;
	        }else{
	            $file = fopen($imagebmp, "wb");
	            fwrite($file, $result);
	            fclose($file);
	        }
	        return $imagebmp;
	    }
	}
		
?>