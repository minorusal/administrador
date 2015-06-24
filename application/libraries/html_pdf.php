<?php
/**
* Descripción: 	Crea PDF con contenido HTML usando libreria TCPDF
* @author: 		Oscar Madonado
* Creación:		2015-06-18
* Modificación:	2015-06-19_OM
*/
// Clases requeridas
// require_once (APPPATH.'third_party/tcpdf/tcpdf.php'); 
require_once(APPPATH.'third_party/html2pdf/html2pdf.class.php');

class html_pdf extends config_vars{

	protected $path_img, $path_tmp; #Paths
	private $pdf; 
	private $orientation,$unit,$format,$unicode,$encoding,$diskcache,$pdfa; #PDF caracteristicas de documento
	private $creador,$autor,$titulo,$subject,$keywords; # Atributos de documento
	private $font;
	public $respuesta;

	public function __construct($orientation=false, $unit=false, $format=false, $unicode=false, $encoding=false, $diskcache=false, $pdfa=false){
		// Variables
		$this->load_vars('assets/cfg/general.cfg');
		$this->path_img 	 	= $this->cfg['path_img'];
		$this->path_tmp 	 	= $this->cfg['path_tmp'];
		$this->load_vars('assets/cfg/codeigniter.cfg');
		$this->load_vars('assets/cfg/pdf.cfg');
		$this->respuesta 		= false;
		// Instancia TCPDF
		$this->orientation 		= (isset($orientation))?$this->var['pdf']['pdf_orientation']:$orientation;
		$this->unit 			= (isset($unit))?$this->var['pdf']['pdf_unit']:$unit;
		$this->format 			= (isset($format))?$this->var['pdf']['pdf_format']:$format;
		$this->unicode 			= (isset($unicode))?$this->var['pdf']['pdf_unicode']:$unicode;
		$this->encoding 		= (isset($encoding))?$this->var['pdf']['pdf_encoding']:$encoding;
		$this->diskcache 		= (isset($diskcache))?$this->var['pdf']['pdf_diskcache']:$diskcache;
		$this->pdfa 			= (isset($pdfa))?$this->var['pdf']['pdf_pdfa']:$pdfa;
		$this->pdf 				= new HTML2PDF($this->orientation, $this->format, 'es');
		// Atributos
		$this->creador 			= (isset($this->var['pdf']['pdf_creador']))?$this->var['pdf']['pdf_creador']:'';
		$this->autor 			= (isset($this->var['pdf']['pdf_autor']))?$this->var['pdf']['pdf_autor']:'';
		$this->titulo			= (isset($this->var['pdf']['pdf_title']))?$this->var['pdf']['pdf_title']:'';
		$this->subject 			= (isset($this->var['pdf']['pdf_subject']))?$this->var['pdf']['pdf_subject']:'';
		$this->keywords 		= (isset($this->var['pdf']['pdf_keywords']))?$this->var['pdf']['pdf_keywords']:'';
		// Font
		$this->font 			= (isset($this->var['pdf']['pdf_font_name']))?$this->var['pdf']['pdf_font_name']:'';
		// Headers
	}

	function crear($data=array()){
		#Contenido HTML
		// dump_var($this->cfg['base_url'].$this->cfg['path_tmp']);
		$html 	= (empty($data['html']))?'Sin contenido.':$data['html'];
		#Output | I => Standar output ; D => Download ; F => Save to localfile ; FD => Save to localfile and download ; S => Return as string ; E => base64 mime multi-part email attachment
		$output = (empty($data['output']))?'I':strtoupper($data['output']);
		#Debug
		$debug 	= (empty($data['debug']))?false:true;
		#Nombre de archivo
		$tmp_path = realpath($this->cfg['path_tmp']).'/';
		$tmp_dir = ($output=='FD' || $output=='F')?$tmp_path:'';
		$archivo = (empty($data['archivo']))?date('Ymd-His').'.pdf':$data['archivo'].'.pdf';
		#Original
        $this->pdf->setDefaultFont($this->font);
        $this->pdf->writeHTML($html, $debug);
        if($PDFFile=$this->pdf->Output($tmp_dir.$archivo, $output)){$this->respuesta = true;}
	}
}
?>