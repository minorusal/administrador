<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Descripción:  Crea imagen de código QR en formato PGN
* @author:      Oscar Maldonado - O3M
* Creación:     2015-05-26
*/
// Clases requeridas
require_once (APPPATH.'third_party/qrcode/qrlib.php'); 

class codeqr extends QRcode{

    protected $archivo; #Archivo
    protected $errorCorrectionLevel, $matrixPointSize, $margin; #Formato
    
    function __construct(){
        $this->archivo              = 'assets\tmp\qrcode.png';
        $this->errorCorrectionLevel = 'L'; #'L','M','Q','H'
        $this->matrixPointSize      = 4; # 1-10
        $this->margin               = 0;
    }

    public function create($texto=''){
        if($texto){
            $this->png($texto, $this->archivo, $this->errorCorrectionLevel, $this->matrixPointSize, $this->margin);           
            return $this->archivo;
        }else{
            return false;
        }
    }
}
?>