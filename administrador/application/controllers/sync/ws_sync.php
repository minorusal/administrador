<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ws_sync extends Base_controller{
	
	public $nusoap_server;
    public $nusoap_title  = "Admincontrol WS";
    public $uri_server    = 'http://192.168.230.121/admincontrol_ci/sync/ws_sync';
    public $limit_query   = 1000;

	public function ws_sync()
    {
        parent::__construct();
        $this->load->model('sync_model','db_model');
        $this->load->library("nuSoap"); 
        $this->nusoap_server = new soap_server();
        $this->nusoap_server->configureWSDL($this->nusoap_title, $this->uri_server);
        $this->nusoap_server->wsdl->schemaTargetNamespace = $this->uri_server;
    }
 
    public function SyncCatalogs()
    {  
        $this->defineComplexTypes_Catalogs();

        function process_downNews( $table_client, $last_id )
        {   
            $CI            =& get_instance();
            $packages      = array();
            $struct_server = $CI->get_structCatalog($table_client);
            $table_server  = $struct_server['table_server'];
            $columns       = $struct_server['struct'];
            $condicion     = array($struct_server['primary_key'], $last_id);
            $data          = $CI->db_model->get_data_news($table_server , $columns, $condicion, $CI->limit_query);
          
            if($data){
                foreach ($data as $items ) {
                   $packages[]['packages'] = implode('|', $items);
                }
            }else{
                return false;
            }
            return $packages;
        }

        function process_downUpdates( $table_client, $last_id, $timestamp )
        {   
            $CI                =& get_instance();
            $packages          = array();
            $struct_server     = $CI->get_structCatalog($table_client);
            $table_server      = $struct_server['table_server'];
            $columns           = $struct_server['struct'];
            $filter_timestamp  = array($struct_server['timestamp'], $timestamp);
            $filter_id         = array($struct_server['primary_key'], $last_id);
            $data              = $CI->db_model->get_data_updates($table_server ,$columns, $filter_id, $filter_timestamp, $CI->limit_query);
            
            if($data){
                foreach ($data as $items ) {
                   $packages[]['packages'] = implode('|', $items);
                }
            }else{
                return false;
            }
            return $packages;
        }
        $this->nusoap_server->service(file_get_contents("php://input"));
    }

    public function SyncSales(){
        $this->defineComplexTypes_Sales();

        function process_UpSales($sales){
            
            $CI =& get_instance();
            if(is_array( $sales )){
                foreach ( $sales as $items ) {
                    $id_punto_venta = $items['id_punto_venta'];

                    $sales_group[$items['id_venta']] = array(

                        'id_venta_local'               => $items['id_venta'],
                        'id_punto_venta'               => $items['id_punto_venta'],
                        'consecutivo'                  => $items['venta_consecutivo'],
                        'esquema'                      => $items['venta_esquema'],
                        'monto_subtotal'               => $items['venta_monto_subtotal'],
                        'monto_descuento'              => $items['venta_monto_descuento'],
                        'descuento_general_porcentaje' => $items['venta_porcentaje_subrrogacion'],
                        'monto_impuestos'              => $items['venta_monto_impuestos'],
                        'monto_subrrogacion'           => $items['venta_monto_subrrogacion'],
                        'porcentaje_subrrogacion'      => $items['venta_porcentaje_subrrogacion'],
                        'monto_total'                  => $items['venta_monto_total'],
                        'id_cliente'                   => $items['venta_id_cliente'],
                        'id_cajero'                    => $items['venta_id_vendedor'],
                        'timestamp'                    => $items['venta_fecha']
                    );

                    $sales_content[] = array(

                            'id_venta_local' => $items['id_venta'],
                            'id_punto_venta' => $items['id_punto_venta'],
                            'id_inventario'  => $items['articulo_id'],
                            'descripcion'    => $items['articulo_descripcion'],
                            'precio'         => $items['articulo_monto_precio'],
                            'descuento'      => $items['articulo_monto_descuento'],
                            'impuesto'       => $items['articulo_monto_impuesto'],
                            'cantidad'       => $items['articulo_cantidad'],
                            'total'          => $items['articulo_monto_total'],
                            'timestamp'      => $items['venta_fecha']
                        );

                    
                    $sales_pay[$items['id_venta']] = array(

                            'id_venta_local' =>  $items['id_venta'],
                            'id_punto_venta' =>  $items['id_punto_venta'],
                            'cambio'         =>  $items['venta_monto_cambio'],
                            'efectivo'       =>  $items['venta_monto_efectivo'],
                            'tarjeta'        =>  $items['venta_monto_tarjeta'],
                            'banco'          =>  $items['venta_tarjeta_banco'],
                            'tipo'           =>  $items['venta_tarjeta_tipo'],
                            'codigo'         =>  $items['venta_tarjeta_codigo'],
                            'vencimiento'    =>  $items['venta_tarjeta_vencimiento'],
                            'timestamp'      =>  $items['venta_fecha']
                    );

                    $sales_ticket[$items['id_venta']] = array(

                            'id_venta_local'  =>  $items['id_venta'],
                            'id_punto_venta'  =>  $items['id_punto_venta'],
                            'consecutivo'     =>  $items['venta_consecutivo'],
                            'comprobante'     =>  $items['comprobante'],
                            'timestamp'       =>  $items['venta_fecha']
                        );

                    if($items['id_cliente_factura'] !='' ){
                        $factures = true;
                        $sales_factures[$items['id_venta']] = array(

                            'id_venta_local'      =>  $items['id_venta'],
                            'id_punto_venta'      =>  $items['id_punto_venta'],
                            'id_cliente_factura'  =>  $items['id_cliente_factura'],
                            'consecutivo'         =>  $items['venta_consecutivo'],
                            'timestamp'           =>  $items['venta_fecha']
                        );  
                    }else{
                        $factures = false;
                    }
                    //$packages[]['packages'] = implode('|', $items);
                }   

                $struct_server = $CI->db_model->insert_packages('ventas', $sales_group);
                $struct_server = $CI->db_model->insert_packages('ventas_articulos', $sales_content);
                $struct_server = $CI->db_model->insert_packages('ventas_pagos', $sales_pay);
                $struct_server = $CI->db_model->insert_packages('ventas_comprobantes', $sales_ticket);
                $struct_server = ($factures) ? $CI->db_model->insert_packages('ventas_facturas', $sales_factures) : '';
                $condition = array( 'id_punto_venta=', $id_punto_venta );
                $last_sale = $CI->db_model->get_maxId('ventas', 'consecutivo', $condition);
                $packages[]['last_sale'] = $last_sale;
            }else{
                $packages[]['packages'] = 'error';
            }
            return $packages;
        }
        $this->nusoap_server->service(file_get_contents("php://input"));

    }

    public function defineComplexTypes_Sales()
    {

        $this->nusoap_server->wsdl->addComplexType(
                                                    'response',
                                                    'complexType',
                                                    'struct',
                                                    'all',
                                                    '',
                                                    array(
                                                        'last_sale' => array('name' => 'last_sale', 'type' => 'xsd:string' )
                                                    )
                                                ); 
        $this->nusoap_server->wsdl->addComplexType(
                                                    'dataQuery', 
                                                    'complexType', 
                                                    'array', 
                                                    '',
                                                    'SOAP-ENC:Array', 
                                                    array(),
                                                    array(),
                                                    'tns:response'
                                                 ); 

        $this->nusoap_server->wsdl->addComplexType(
                                                    'sales',
                                                    'complexType',
                                                    'struct',
                                                    'all',
                                                    '',
                                                    array(
                                                        'id_venta' => array('name'=> 'id_venta', 'type' => 'xsd:int'),
                                                        'id_punto_venta' => array('name'=> 'id_punto_venta', 'type' => 'xsd:int'),
                                                        'venta_consecutivo' => array('name'=> 'venta_consecutivo', 'type' => 'xsd:string'),
                                                        'venta_fecha' => array('name'=> 'venta_fecha', 'type' => 'xsd:string'),
                                                        'venta_esquema' => array('name'=> 'venta_esquema', 'type' => 'xsd:string'),
                                                        'venta_monto_subtotal' => array('name'=> 'venta_monto_subtotal', 'type' => 'xsd:string'),
                                                        'venta_monto_descuento_individual' => array('name'=> 'venta_monto_descuento_individual', 'type' => 'xsd:string'),
                                                        'venta_monto_descuento_general' => array('name'=> 'venta_monto_descuento_general', 'type' => 'xsd:string'),
                                                        'venta_monto_impuestos' => array('name'=> 'venta_monto_impuestos', 'type' => 'xsd:string'),
                                                        'venta_monto_subrrogacion' => array('name'=> 'venta_monto_subrrogacion', 'type' => 'xsd:string'),
                                                        'venta_monto_total' => array('name'=> 'venta_monto_total', 'type' => 'xsd:string'),
                                                        'venta_tipo_pago' => array('name'=> 'venta_tipo_pago', 'type' => 'xsd:string'),
                                                        'venta_monto_cambio' => array('name'=> 'venta_monto_cambio', 'type' => 'xsd:string'),
                                                        'venta_monto_efectivo' => array('name'=> 'venta_monto_efectivo', 'type' => 'xsd:string'),
                                                        'venta_monto_tarjeta' => array('name'=> 'venta_monto_tarjeta', 'type' => 'xsd:string'),
                                                        'venta_tarjeta_banco' => array('name'=> 'venta_tarjeta_banco', 'type' => 'xsd:string'),
                                                        'venta_tarjeta_tipo' => array('name'=> 'venta_tarjeta_tipo', 'type' => 'xsd:string'),
                                                        'venta_cliente' => array('name'=> 'venta_cliente', 'type' => 'xsd:string'),
                                                        'venta_vendedor' => array('name'=> 'venta_vendedor', 'type' => 'xsd:string'),
                                                        'articulo_id' => array('name'=> 'articulo_id', 'type' => 'xsd:int'),
                                                        'articulo_descripcion' => array('name'=> 'articulo_descripcion', 'type' => 'xsd:string'),
                                                        'articulo_cantidad' => array('name'=> 'articulo_cantidad', 'type' => 'xsd:string'),
                                                        'articulo_monto_precio' => array('name'=> 'articulo_monto_precio', 'type' => 'xsd:string'),
                                                        'articulo_monto_descuento' => array('name'=> 'articulo_monto_descuento', 'type' => 'xsd:string'),
                                                        'articulo_monto_impuesto' => array('name'=> 'articulo_monto_impuesto', 'type' => 'xsd:string'),
                                                        'articulo_monto_total' => array('name'=> 'articulo_monto_total', 'type' => 'xsd:string')
                                                    )
                                                );
  
        $this->nusoap_server->register(   
                                        'process_UpSales',
                                        array('sales'  => 'tns:sales'), 
                                        array('return' => 'tns:dataQuery'), 
                                        "urn:process_UpSales", 
                                        "urn:".$this->uri_server."/process_UpSales", 
                                        'rpc', 
                                        'encoded', 
                                        'syncSales'    
                                    );
    }

    public function defineComplexTypes_Catalogs()
    {
        $this->nusoap_server->wsdl->addComplexType(
                                                    'response',
                                                    'complexType',
                                                    'struct',
                                                    'all',
                                                    '',
                                                    array(
                                                        'packages' => array( 
                                                                          
                                                                            'name' => 'packages', 
                                                                            'type' => 'xsd:string'
                                                        )
                                                    )
        ); 
        $this->nusoap_server->wsdl->addComplexType(
                                                    'dataQuery', 
                                                    'complexType', 
                                                    'array', 
                                                    '',
                                                    'SOAP-ENC:Array', 
                                                    array(),
                                                    array(),
                                                    'tns:response'
        ); 
        $this->nusoap_server->register(
                                        'process_downNews', 
                                        array (
                                            'table_client' => "xsd:string",
                                            'last_id'      => "xsd:string"
                                        ), 
                                        array (
                                            "return" => "tns:dataQuery"
                                        ), 
                                        "urn:process_downNews", 
                                        "urn:".$this->uri_server."/process_downNews", 
                                        "rpc", 
                                        "encoded", 
                                        "Sync Catalogs"
        );
        $this->nusoap_server->register(
                                        'process_downUpdates', 
                                        array (
                                            'table_client' => "xsd:string",
                                            'last_id'      => "xsd:string",
                                            'timestamp'    => "xsd:string"
                                        ), 
                                        array (
                                            "return" => "tns:dataQuery"
                                        ), 
                                        "urn:process_downUpdates", 
                                        "urn:".$this->uri_server."/process_downUpdates", 
                                        "rpc", 
                                        "encoded", 
                                        "Sync Catalogs"
        );
    }

    public function get_structCatalog($key)
    {   
        $struct = array(
                        'descuentos' => array(  
                                             'table_server' => 'administracion_descuentos'
                                            ,'primary_key'  => 'id_administracion_descuentos'
                                            ,'timestamp'    => 'edit_timestamp'
                                            ,'struct'       =>  array(
                                                                    'id_administracion_descuentos'
                                                                    ,'descuento'
                                                                    ,'clave_corta'
                                                                    ,'valor_descuento'
                                                                    ,'descripcion'
                                                                    ,'edit_timestamp'
                                                                    ,'activo'
                                                                )
                        ),
                        'lineas' => array( 
                                             'table_server' => 'compras_lineas'
                                            ,'primary_key'  => 'id_compras_linea'
                                            ,'timestamp'    => 'edit_timestamp'
                                            ,'struct'       =>  array(
                                                                         'id_compras_linea'
                                                                        ,'linea'
                                                                        ,'clave_corta'
                                                                        ,'descripcion'
                                                                        ,'edit_timestamp'
                                                                        ,'activo'
                                                                )   
                        ),
                        'marcas' => array(  
                                             'table_server' => 'compras_marcas'
                                            ,'primary_key'  => 'id_compras_marca'
                                            ,'timestamp'    => 'edit_timestamp'
                                            ,'struct'       =>  array(
                                                                         'id_compras_marca'
                                                                        ,'marca'
                                                                        ,'clave_corta'
                                                                        ,'descripcion'
                                                                        ,'edit_timestamp'
                                                                        ,'activo'
                                                                )   
                        ),
                        'entidades' => array(   
                                                 'table_server' => 'administracion_entidades'
                                                ,'primary_key'  => 'id_administracion_entidad'
                                                ,'timestamp'    => 'edit_timestamp'
                                                ,'struct'       =>  array(
                                                                             'id_administracion_entidad'
                                                                            ,'ent_abrev'
                                                                            ,'clave_corta'
                                                                            ,'edit_timestamp'
                                                                            ,'activo'
                                                                        )   
                        )/*,
                        'clientes' => array(    
                                                 'table_server' => 'ventas_clientes'
                                                ,'primary_key'  => 'id_ventas_clientes'
                                                ,'timestamp'    => 'edit_timestamp'
                                                ,'struct' => array(
                                                                     'id_ventas_clientes'
                                                                    ,'nombre'
                                                                    ,'paterno'
                                                                    ,'materno'
                                                                    ,'razon_social'
                                                                    ,'clave_corta'
                                                                    ,'rfc'
                                                                    ,'descuento'
                                                                    ,'calle'
                                                                    ,'num_int'
                                                                    ,'num_ext'
                                                                    ,'colonia'
                                                                    ,'municipio'
                                                                    ,'entidad'
                                                                    ,'pais'
                                                                    ,'cp'
                                                                    ,'telefonos'
                                                                    ,'email'
                                                                    ,'timestamp_edit'
                                                                    ,'activo'
                                                    )   
                        )*/
                        
                    );

        return $struct[$key];
    }
}