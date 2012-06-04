<?php
if (class_exists('BuyatDriver') === false) {

    /**
     * This file implements the Panhandler interface for BuyAt.
     */
    if (class_exists('BuyatAPIClient') === false) {
        class PanhandlerDriverLoader extends PanhandlerError {}
        try {
            require_once('BuyatAPIClient.php');
            require_once('Entities.php');
        }
        catch (PanhandlerDriverLoader $exception) {
            add_action('admin_notices', array($exception, 'getMessage'));
            exit(1);
        }    
    }
    
    
    final class BuyatDriver implements Panhandles {
    
        //// PRIVATE MEMBERS ///////////////////////////////////////
        
        // Stuff we don't let out of the "box", at least not easily
        //
        private $api_client;            // The BuyatAPI Client Object set by constructor only
        private $api_key;               // The API Key given to the client by BuyAT.
        
        // Can be changed by calling program
        //
        private $keywords       = array();      // List of keywords to fetch
        private $page_number    = 1;            // Which page number to fetch
        private $programme_id;                  // The vendor ID (665 = Ticketmaster, 1386 = Fathead, 1388 = Ghirardelli)
        private $return         = 10;           // Products we return. can be changed by set_maximum_product_count()
        private $sort_order     = 'asc';        // Default sort order
        private $sort_type      = 'relevance';  // Default sort type
        
        /**
         * Support options.
         * The stuff that can be passed by the calling program, i.e. via  a WordPress shortcode.
         * programme_id is here for things like the Master Edition, but is often force-fed at 
         * creation by the head-end program.
         */
        private $supported_options = array(
            'keywords',
            'page_number',
            'programme_id',            
            'return',
            'sort_type',
            'sort_order',
        );
            
                
        //// CONSTRUCTOR ///////////////////////////////////////////
    
        /**
         * CONSTRUCTOR
         *
         * We have to pass in the API Key as we need
         * this to fetch product information.
         */
        public function __construct($api_key, $programme_id = null) {
            $this->api_key = $api_key;
            if (isset($programme_id)) { $this->programme_id = $programme_id; }
            $this->api_client = new BuyatAPIClient($api_key);
        }
    
        //// INTERFACE METHODS /////////////////////////////////////
           
        /**
         * Method: GET_PRODUCTS()
         * Panhandler required method.
         */
        public function get_products($options = null) {
                        
            // Check the incoming options to ensure they are supported
            // by this interface.
            //
            if (is_array($options)) { 
                foreach (array_keys($options) as $name) {
                    if (in_array($name, $this->supported_options) === false) {
                        throw new PanhandlerNotSupported("Received unsupported option $name");
                    }
                }    
            }
            
            // Set any options that are defaults in this interface
            // but were not passed in.
            foreach ($this->supported_options as $name) {
                if (!isset($options[$name]) && isset($this->$name)) {
                    $options[$name] = $this->$name;
                }
            }    
            
            // Properly encode the keywords
            //
            if (isset($options['keywords'])) {
                if (! is_array($options['keywords'])) {
                    $options['keywords'] = array($options['keywords']);
                }
                $options['keywords'] = urlencode(implode(' ', $options['keywords']));
            }
            
            // Force Programme ID if set in constructor
            //
            if (isset($this->programme_id)) {
             $options['programme_id'] = $this->programme_id;
             return $this->extract_products(
                $this->api_client->searchProducts(
                    array($options['keywords']),
                    $options['page_number'],
                    $options['return'],
                    $options['sort_type'],
                    $options['sort_order'],
                    $options['programme_id']
                    )
                );             
            }
			//If the account isn't valid, programme_id is non-existant
			//and will run this part..
			else {
                        print('<pre>');
                        print('Notice:<br />');
                        print('There\'s a possible error with your BuyAT account.<br />');
                        print('Please check it to make sure it is active and functioning.');
                        print('</pre>');
                };
        }
        
        /**
         * METHOD: GET_SUPPORTED_OPTIONS()
         * Returns the supported $options that get_products() accepts.
         */
        public function get_supported_options() {
            return $this->supported_options;
        }
        
        /**
         * METHOD: SET_DEFAULT_OPTION_VALUES()
         *
         * Called by the interface methods which take an $options hash.
         * This method sets the appropriate private members of the object
         * based on the contents of hash.  It looks for the keys in
         * $supported_options * and assigns the value to the private
         * members with the same names.  See the documentation for each of
         * those members for a description of their acceptable values,
         * which this method does not try to enforce.
         *
         * Returns no value.
         */
        private function set_default_options($options) {
            foreach ($this->supported_options as $name) {
                if (isset($options[$name])) {
                    $this->$name = $options[$name];
                }
            }
        }
        
        /**
         * METHOD: SET_MAXIMUM_PRODUCT_COUNT()
         *
         */
        public function set_maximum_product_count($count) {
            $this->return = $count;
        }
        
        /**
         * METHOD: SET_RESULTS_PAGE()
         *
         */
        public function set_results_page($page_number) {
            $this->page_number = $page_number;
        }           
        

        //// PUBLIC METHODS /////////////////////////////////////

        
        /**
         * Set options back to initial state after contruction
         */
        public function reset_option_values() {
            $this->set_default_options($this->initial_option_state);            
        }
        
        
        public function set_default_option_values($options) {
            $this->set_default_options($options);
        }
        
        
        /**
         * Added to return our current options state to help
         * calling classes remember what they started with.
         * Need this because our properties are private.
         */
        public function fetch_supported_option_values() {            
            foreach ($this->supported_options as $key) {
                $return_hash[$key] = $this->$key;
            }
            return (array) $return_hash;
        }        

        
        //// PRIVATE METHODS ///////////////////////////////////////
        
    
        /**
         * Takes a BuyAt Product object representing an <item> node in search
         * results and returns a PanhandlerProduct object for that item.
         */
        private function convert_item($item) {
            $product            = new PanhandlerProduct();
            $product->name       = $item->getProductName();
            $product->price      = $item->getOnlinePrice();
            $product->description = $item->getDescription();
            $product->web_urls   = array($item->getProductURL());
            $product->image_urls = array($item->getImageURL().'&KeepThis=true&TB_iframe=true&height=400&width=600');
            return $product;
        }
    
        /**
         * Takes a BuyAt Product objects array representing all keyword search
         * results and returns an array of PanhandlerProduct objects
         * representing every item in the results.
         */
        private function extract_products($buyatproducts) {
            $products = array();
            
            foreach ($buyatproducts['products'] as $item) {
                $products[] = $this->convert_item($item);
            }
    
            return $products;
        }
        
    }

}

?>
