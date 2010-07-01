<?php

/**
 * We need the generic WPCSL plugin class, since that is the
 * foundation of much of our plugin.  So here we make sure that it has
 * not already been loaded by another plugin that may also be
 * installed, and if not then we load it.
 */
if (class_exists('wpCSL_plugin', false) === false) {
    require_once(MP_BUYAT_PLUGINDIR.'WPCSL-generic/CSL-plugin.php');
}

//// SETTINGS ////////////////////////////////////////////////////////

/**
 * This section defines the settings for the admin menu.
 * MP: CafePress Edition = 1
 * MP: Commission Junction Edition = 2
 * MP: eBay Edition = 3
 * MP: BuyAt Master Edition = 4
 * MP: Ticketmaster Edition = 5
 * MP: NY Times Store Edition = 6
 */

$MP_nytstore_plugin = new wpCSL_plugin(
    array(
        'use_obj_defaults'       => true,
        'prefix'                 => 'csl-mp-buyat',
        'name'                   => 'MoneyPress : BuyAt Master Edition',
        'url'                    => 'http://www.cybersprocket.com/products/moneypress-buyat-edition/',
        'paypal_button_id'       => '9NGBSBJLV2XNL',
        'plugin_path'            => MP_BUYAT_PLUGINDIR,
        'plugin_url'             => MP_BUYAT_PLUGINURL,
        'cache_path'             => MP_BUYAT_PLUGINDIR . 'cache',
        'driver_name'            => 'Buyat',
        'driver_args'            => array(get_option('csl-mp-buyat-api_key')),
        'shortcodes'             => array('mp-buyat','mp_buyat'),
        'wp_filter_id'           => '4'
    )
);


//-------------- HOW TO USE SECTION

$MP_nytstore_plugin->settings->add_section(
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
        'return="5" keywords="jewelry"], which would only show five items.</p>' 
    )
);

//-------------- STORE CONFIGURATION SECTION

$MP_nytstore_plugin->settings->add_section(
    array(
        'name'        => 'Data Feed Configuration',
        'description' => 'Information you need to enter in order to get this MoneyPress plugin talking to the data servers.'
    )
);

$MP_nytstore_plugin->settings->add_item('Data Feed Configuration', 'BuyAt API Key', 'api_key', 'text', false,
                                  'Your BuyAt API Key.  You can use our demo key 01-8565fdd6e88a0738cf0f05692ff5398f until you get your own key.  '.
                                  'This is a shared demo key and should not be used to run your plugin. ');


//-------------- PRODUCT DISPLAY SECTION

$MP_nytstore_plugin->settings->add_section(
    array(
        'name'        => 'Product Display',
        'description' => 'The values that are entered here are the defaults whenever you use a shortcode.' .
                         'You can override these settings via the shortcode qualifiers when you put the code into a page or post.<br/><br/>'
    )
);
$MP_nytstore_plugin->settings->add_item('Product Display', 'Programme ID: '             , 'programme_id','text' ,false  ,'The default programme ID (vendor code) to use as the primary filter for product listings.  Default: blank (match any vendor).');
$MP_nytstore_plugin->settings->add_item('Product Display', 'Keywords: '                 , 'keywords'    ,'text' ,false  ,'Search keywords, space separated. Matches against product name, description and category with various weightings. Results are logical ORd but are weighted by relevance.  Normally left blank and specified in each page/post shortcode.  Default: blank (match all).');
$MP_nytstore_plugin->settings->add_item('Product Display', 'How many to show at once? ' , 'return'      ,'text' ,false  ,'How many items do you want displayed on your page by default? Default: 10.');
$MP_nytstore_plugin->settings->add_item('Product Display', 'What type of sorting? '     , 'sort_type'   ,'text' ,false  ,'Field name to order by (product_sku, product_name, online_price, brand_name or relevance). Default: relevance.');
$MP_nytstore_plugin->settings->add_item('Product Display', 'Sort order: '               , 'sort_order'  ,'text' ,false  ,'Sort results in ascending (asc) or descending (desc) order? Default: asc.');
$MP_nytstore_plugin->settings->add_item('Product Display', 'Page number: '              , 'page_number' ,'text' ,false  ,'What page number to start on? This parameter should almost always be left unset.  Default: 1');

?>
