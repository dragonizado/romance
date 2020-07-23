<?php
    
    namespace Dragonizado\Romance;

    class RomanceApi
    {

        protected $output_format = 'JSON';

        /** @var int product id when is loaded */
        protected $id_product = null;

        /** @var int  associations when is loaded */
        protected $associations = null;

        /** @var string Authentification key */
        protected $api_key = '';
    
        /** @var string Shop URL */
        protected $url = '';

        /** @var  string protocol of conection */
        protected $protocol = 'https://';
        
        /** @var string domain of conection */
        protected $domain = '';

        /** @var string type od consult MANUAL (CUSTOM) or AUTOMATIC (CONSTRUCT)*/
        protected $type = 'CONSTRUCT';

        /** @var  */
        protected $response = null;

        public function __construct($key,$domain,$debug = true){

            $this->api_key = $key;
            $this->domain = $domain;
            $this->debug = $debug;
            $this->version = 'unknown';
        }


        public  function fetch($conection_string = '',$manual = false){
            if($manual){
                $this->url = $conection_string;
            }else{
                $this->url = $this->protocol.$this->api_key.'@'.$this->domain.'/';
            }
            return  $this;
        }

        public function where($custom = 'products',$params = ''){
            if($params == ''){
                $this->url .= $custom;
            }else{
                $this->url .= $custom .'/'.$params;
            }
            return  $this;
        }

        public function filter($filter){
            $this->url .= "?".$filter;
            return $this;
        }

        public function json($initParam ='?'){
            $this->output_format = 'JSON';
            $this->url .= $initParam.'output_format='.$this->output_format ;
            return $this;
        }

        public function xml($initParam ='?'){
            $this->output_format = 'XML';
            $this->url .= $initParam.'output_format='.$this->output_format ;
            return $this;
        }

        public function product($id_product,$params = ''){
            $this->type = 'CUSTOM';
            
            if($params != ' '){
                $params = '?'.$params;
            }

            $this->url =  $this->protocol.$this->api_key.'@'.$this->domain.'/products/'.$id_product.$params;
            $response = simplexml_load_string(file_get_contents($this->url), 'SimpleXMLElement',LIBXML_NOCDATA);
            $this->id_product = $response->product->id;
            $this->associations = $response->product->associations;
            $this->response['product'] = $response->product;
            $this->response['domain'] = $this->protocol.$this->domain.'/' ;
            return $this;
        }

        public function productsFull($filter=''){
            $this->type = 'CUSTOM';

            if($filter != ' '){
                $filter = '&'.$filter;
            }

            $this->url =  $this->protocol.$this->api_key.'@'.$this->domain.'/products?display=full'.$filter;
            $response = simplexml_load_string(file_get_contents($this->url), 'SimpleXMLElement',LIBXML_NOCDATA);
            $this->response['products'] = $response->products;
            return $this;
        }

        public function specificPrices($displayParams  = 'full'){
            $this->securityProduct("No se puede buscar los precios de un producto sin antes buscar el producto.");
            $domain = $this->protocol.$this->api_key.'@'.$this->domain.'/specific_prices?display='.$displayParams.'&filter[id_product]='.$this->id_product;

            $response = simplexml_load_string(file_get_contents($domain), 'SimpleXMLElement',LIBXML_NOCDATA);

            $this->response['specific_prices'] = $response->specific_prices;

            return $this;
        }

        public function combinations($displayParams  = 'full'){
            $this->securityProduct("No se puede buscar combinaciones de un producto sin antes buscar el producto.");
            $this->validateAssociations();
            $response = [];
            $combinationstring ='[';
            $position_one = 0;
            foreach ($this->associations->combinations->combination as $key => $valueCombination) {
                $combinationstring .= $valueCombination->id;
                $position_one++;
                if($position_one > 0){
                    $combinationstring .= '|';
                }
            }
            $combinationstring .=']';

            $consult_combinations_url = $this->protocol.$this->api_key.'@'.$this->domain.'/combinations?display=full&filter[id]='.$combinationstring;
            $consult_combinations = simplexml_load_string(file_get_contents($consult_combinations_url), 'SimpleXMLElement',LIBXML_NOCDATA);
            
            $productOptValueString ='[';
            $position_two = 0;

            foreach ($this->associations->product_option_values->product_option_value as $key => $valueProductOptValue) {
                $productOptValueString .= $valueProductOptValue->id;
                $position_two++;
                if($position_two > 0){
                    $productOptValueString .= '|';
                }
            }

            $productOptValueString .=']';

            $consult_productOptValue_url = $this->protocol.$this->api_key.'@'.$this->domain.'/product_option_values?display=full&filter[id]='.$productOptValueString;
            $consult_productOptValue = simplexml_load_string(file_get_contents($consult_productOptValue_url), 'SimpleXMLElement',LIBXML_NOCDATA);


            $response['combination'] = $consult_combinations->combinations;
            $response['specific_price'] = $consult_productOptValue->product_option_values;
              

            $this->response['combinations'] = $response;
            return $this;
        }

        public function combinationsQuery($displayParams  = 'full'){
            $this->securityProduct("No se puede buscar combinaciones de un producto sin antes buscar el producto.");
            $this->validateAssociations();
            $response = [];
            $combinationstring ='[';
            $position_one = 0;
            foreach ($this->associations->combinations->combination as $key => $valueCombination) {
                $combinationstring .= $valueCombination->id;
                $position_one++;
                if($position_one > 0){
                    $combinationstring .= '|';
                }
            }
            $combinationstring .=']';

            $consult_combinations_url = $this->protocol.$this->api_key.'@'.$this->domain.'/combinations?display=full&filter[id]='.$combinationstring;
            $consult_combinations = simplexml_load_string(file_get_contents($consult_combinations_url), 'SimpleXMLElement',LIBXML_NOCDATA);
            
            $productOptValueString ='[';
            $position_two = 0;

            foreach ($this->associations->product_option_values->product_option_value as $key => $valueProductOptValue) {
                $productOptValueString .= $valueProductOptValue->id;
                $position_two++;
                if($position_two > 0){
                    $productOptValueString .= '|';
                }
            }

            $productOptValueString .=']';

            $consult_productOptValue_url = $this->protocol.$this->api_key.'@'.$this->domain.'/product_option_values?display=full&filter[id]='.$productOptValueString;
            $consult_productOptValue = simplexml_load_string(file_get_contents($consult_productOptValue_url), 'SimpleXMLElement',LIBXML_NOCDATA);


            $response['combination'] = $consult_combinations->combinations;
            $response['specific_price'] = $consult_productOptValue->product_option_values;
              

            $this->response['combinations'] = $response;
            return $this;
        }

        public function images(){
            $this->securityProduct("No se puede buscar imagenes de un producto sin antes buscar el producto.");
            $this->validateAssociations();
            $images = [];

            foreach($this->associations->images->image as  $valueAssocciation) {
                $url = $this->protocol.$this->domain.'/images/products/'.$this->id_product.'/'.$valueAssocciation->id;
                array_push($images,$url);
            }

            $this->response['images'] = $images;
            return $this;
        }

        public function combinationsUrl(){
            $this->securityProduct("No se puede buscar combinaciones de un producto sin antes buscar el producto.");
            $this->validateAssociations();
            $combinations = [];
            foreach ($this->associations->combinations->combination as $key => $valueCombination) {
                $url = $this->protocol.$this->domain.'/combinations/'.$valueCombination->id;
                array_push($combinations,$url);
            }
            $this->response['combinationsUrl'] = $combinations;
            return $this;
        }

        public function get(){
            $this->validateBasicConfig();
            if($this->type == 'CONSTRUCT'){
                try {
                    switch ($this->output_format) {
                        case 'JSON':
                            $response = json_encode( file_get_contents($this->url) , JSON_UNESCAPED_SLASHES);
                            break;
                        case 'XML':
                            $response = simplexml_load_string(file_get_contents($this->url), 'SimpleXMLElement', LIBXML_NOCDATA);
                        break;
                        default:
                            $response = file_get_contents($this->url);
                            break;
                    }
                    
                } catch (DragonizadoApiRomanceExeption $th) {
                    abort(403,$th);
                }
                return $response;
            }else{
                return $this->response;
            }
        }

        private function validateBasicConfig(){
            if($this->api_key == '' || $this->domain == '' || $this->api_key == null || $this->domain == null){
               abort(403, 'No se ha configurado los parametros para la conexion del API.');
            }
        }

        protected function securityProduct($message){
            if($this->id_product == null){
                abort(403,$message);
            }
        }

        protected function validateAssociations(){
            if($this->associations == null){
                abort(403,"No se ha encontrado un producto o este no cuenta con relaciones.");
            }
        }

        protected function parseXML($response)
        {
            if ($response != '')
            {
                libxml_clear_errors();
                libxml_use_internal_errors(true);
                $xml = simplexml_load_string($response,'SimpleXMLElement', LIBXML_NOCDATA);
                if (libxml_get_errors())
                {
                    $msg = var_export(libxml_get_errors(), true);
                    libxml_clear_errors();
                    abort(500,'La respuesta HTTP XML no es  parseable: '.$msg);
                }
                return $xml;
            }
            else
                throw new DragonizadoApiRomanceExeption('HTTP response is empty');
        }


    }
    