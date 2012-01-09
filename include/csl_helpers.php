<?php
/****************************************************************************
 ** file: csl_helpers.php
 **
 ** Helper functions for this plugin.
 ***************************************************************************/


/**************************************
 ** function: setup_admin_interface_for_mpbuy
 **
 ** Builds the interface elements used by WPCSL-generic for the admin interface.
 **/
function setup_admin_interface_for_mpbuy() {
    global $MP_buyat_plugin;     

    //-------------------------
    // Navbar Section
    //-------------------------
    //    
    $MP_buyat_plugin->settings->add_section(
        array(
            'name' => 'Navigation',
            'div_id' => 'mp_navbar',
            'description' => $MP_buyat_plugin->helper->get_string_from_phpexec(MP_BUYAT_PLUGINDIR.'/templates/navbar.php'),
            'is_topmenu' => true,
            'auto' => false
        )
    );    
    
        
    //-------------- HOW TO USE SECTION
    
    $MP_buyat_plugin->settings->add_section(
        array(
            'name' => 'How to Use',
            'description' =>
            '<p>To use MoneyPress : BuyAt Edition you only need to add a simple '                   .
            'shortcode to any page where you want to show NY Times Store products.  An example '              .
            'of the shortcode is <code>[mp-buyat keywords="jewelry"]</code>. '     .
            'Putting this code on a page would show ten products from various BuyAt vendors' .
            'The list will include links to each item and their current price.  If you want '        .
            'to change how many products are shown, you can either change the default value below ' .
            'or you can change it in the shortcode itself, e.g. <code>[mp-buyat '            .
            'return="5" keywords="jewelry"]</code>, which would only show five items.</p>' 
        )
    );
    
    //-------------- STORE CONFIGURATION SECTION
    
    $MP_buyat_plugin->settings->add_section(
        array(
            'name'        => 'Data Feed Configuration',
            'description' => 'Information you need to enter in order to get this MoneyPress plugin talking to the data servers.'
        )
    );
    
    $MP_buyat_plugin->settings->add_item('Data Feed Configuration', 'BuyAt API Key', 'api_key', 'text', false,
                                      'Your BuyAt API Key.  You can use our demo key 01-8565fdd6e88a0738cf0f05692ff5398f until you get your own key.  '.
                                      'This is a shared demo key and should not be used to run your plugin. ');
    
    
    //-------------- PRODUCT DISPLAY SECTION
    
    $MP_buyat_plugin->settings->add_section(
        array(
            'name'        => 'Product Display',
            'description' => 'The values that are entered here are the defaults whenever you use a shortcode.' .
                             'You can override these settings via the shortcode qualifiers when you put the code into a page or post.<br/><br/>'
        )
    );
    $MP_buyat_plugin->settings->add_item('Product Display', 'Programme ID: '             , 'programme_id','text' ,false  ,'The default programme ID (vendor code) to use as the primary filter for product listings.  Default: blank (match any vendor).');
    $MP_buyat_plugin->settings->add_item('Product Display', 'Keywords: '                 , 'keywords'    ,'text' ,false  ,'Search keywords, space separated. Matches against product name, description and category with various weightings. Results are logical ORd but are weighted by relevance.  Normally left blank and specified in each page/post shortcode.  Default: blank (match all).');
    $MP_buyat_plugin->settings->add_item('Product Display', 'How many to show at once? ' , 'return'      ,'text' ,false  ,'How many items do you want displayed on your page by default? Default: 10.');
    $MP_buyat_plugin->settings->add_item('Product Display', 'What type of sorting? '     , 'sort_type'   ,'text' ,false  ,'Field name to order by (product_sku, product_name, online_price, brand_name or relevance). Default: relevance.');
    $MP_buyat_plugin->settings->add_item('Product Display', 'Sort order: '               , 'sort_order'  ,'text' ,false  ,'Sort results in ascending (asc) or descending (desc) order? Default: asc.');
    $MP_buyat_plugin->settings->add_item('Product Display', 'Page number: '              , 'page_number' ,'text' ,false  ,'What page number to start on? This parameter should almost always be left unset.  Default: 1');
    
      
}

/**************************************
 ** function: setup_stylesheet_for_mpbuy
 **
 ** Setup the CSS for the product pages.
 **/
function setup_stylesheet_for_mpbuy() {
    global $MP_buyat_plugin;
    $MP_buyat_plugin->themes->assign_user_stylesheet();    
}

/**************************************
 ** function: setup_ADMIN_stylesheet_for_mpbuy
 **
 ** Setup the CSS for the admin page.
 **/
function setup_ADMIN_stylesheet_for_mpbuy() {            
    if ( file_exists(MP_BUYAT_PLUGINDIR.'css/admin.css')) {
        wp_register_style('csl_mpbuy_admin_css', MP_BUYAT_PLUGINURL .'/css/admin.css'); 
        wp_enqueue_style ('csl_mpbuy_admin_css');
    }      
}


/**************************************
 ** function: setup_admin_option_pages_for_mpbuy
 **
 ** Setup the option pages for the admin interface.
 **/
function setup_admin_option_pages_for_mpbuy() {
    global $MP_buyat_plugin;     
    add_submenu_page(
        'csl-mp-buy-options',
        __("Settings: Plus", MP_BUYAT_PREFIX), 
        __("Settings: Plus", MP_BUYAT_PREFIX), 
        'administrator', 
        MP_BUYAT_PLUGINDIR.'/settings_plus.php'
    );             
 }




 

