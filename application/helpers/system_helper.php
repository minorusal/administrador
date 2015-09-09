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
	* imprime un arreglo formateado para debug
	* y detiene la ejecucion del script
	* @return array $array
	*/
	if(!function_exists('print_debug')){
		function print_debug($array, $die = true){
			echo '<pre>';
			print_r($array);
			echo '</pre>';
			if($die){
				die();	
			}
			
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