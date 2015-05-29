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
        config_vars::load_vars();
        $this->archivo              = $this->cfg['path_tmp'].'qrcode.png';
        $this->errorCorrectionLevel = $this->cfg['qrcode_error_correction_level']; #'L','M','Q','H'
        $this->matrixPointSize      = $this->cfg['qrcode_point_size']; # 1-10
        $this->margin               = $this->cfg['qrcode_margin'];
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