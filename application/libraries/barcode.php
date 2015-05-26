<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Descripción:  Crea imagen de código de barras (wbmp|png|gif|jpg)
* @author:      Oscar Maldonado - O3M
* Creación:     2015-05-26
*/
// Clases requeridas
require_once(APPPATH.'third_party/barcode/class/BCGFontFile.php');
require_once(APPPATH.'third_party/barcode/class/BCGColor.php');
require_once(APPPATH.'third_party/barcode/class/BCGDrawing.php');
// Codificacion de barcode
// require_once(APPPATH.'third_party/barcode/class/BCGcode39.barcode.php');
require_once(APPPATH.'third_party/barcode/class/BCGcode128.barcode.php');

class barcode{

    protected $font_name, $font_size; #Fuente
    protected $barcode_escala, $barcode_grosor; #Imagen
    public $barcode_img_tipo; #Formato de imagen
    
    function __construct(){
        $this->font_name        = APPPATH.'third_party/barcode/font/Arial.ttf';
        $this->font_size        = 13;
        $this->barcode_escala   = 2;
        $this->barcode_grosor   = 20;
        $this->barcode_img_tipo = 'png'; #wbmp|png|gif|jpg
    }

    public function create($texto=''){
        if($texto){
            // Variables
            $f_name = $this->font_name;
            $f_size = $this->font_size;
            $escala = $this->barcode_escala;
            $grosor = $this->barcode_grosor;
            $tipo   = $this->barcode_img_tipo;
            // Cargar fuente
            $font = new BCGFontFile($f_name, $f_size);

            // Definción de colores
            $color_black = new BCGColor(0, 0, 0);
            $color_white = new BCGColor(255, 255, 255);

            $drawException = null;
            try {
                $code = new BCGcode128();
                $code->setScale($escala); // Resolucion
                $code->setThickness($grosor); // grosor
                $code->setForegroundColor($color_black); // Color de barras
                $code->setBackgroundColor($color_white); // Color de espacios
                $code->setFont($font); // Fuente (or 0)
                $code->parse($texto); // Texto
            } catch(Exception $exception) {
                $drawException = $exception;
            }

            /* Here is the list of the arguments
            1 - Filename (empty : display on screen)
            2 - Background color */
            $archivo = 'assets\tmp\barcode.'.$tipo;
            $drawing = new BCGDrawing($archivo, $color_white);
            if($drawException) {
                $drawing->drawException($drawException);
            } else {
                $drawing->setBarcode($code);
                $drawing->draw();
            }

            // Identificación de formato y Crear imagen al vuelo
            switch ($tipo) {   
                case 'wbmp':  
                            // $ext  = 'wbmp'; 
                            // $mime = 'image/vnd.wap.wbmp'; 
                            // header('Content-Type: '.$mime);
                            // header('Content-Disposition: inline; filename="barcode.'.$ext.'"');
                            $drawing->finish(BCGDrawing::IMG_FORMAT_WBMP);
                            break;           
                case 'png': 
                            // $ext  = 'png'; 
                            // $mime = 'image/png';  
                            // header('Content-Type: '.$mime);
                            // header('Content-Disposition: inline; filename="barcode.'.$ext.'"');
                            $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
                            break;
                case 'gif': 
                            // $ext  = 'gif'; 
                            // $mime = 'image/gif';  
                            // header('Content-Type: '.$mime);
                            // header('Content-Disposition: inline; filename="barcode.'.$ext.'"');
                            $drawing->finish(BCGDrawing::IMG_FORMAT_GIF);
                            break;
                case 'jpg': 
                            // $ext  = 'jpg'; 
                            // $mime = 'image/jpeg'; 
                            // header('Content-Type: '.$mime);
                            // header('Content-Disposition: inline; filename="barcode.'.$ext.'"');
                            $drawing->finish(BCGDrawing::IMG_FORMAT_JPEG);
                            break;
                default :   return false; break;
            }
            return $archivo;
        }else{
            return false;
        }
    }
}
?>