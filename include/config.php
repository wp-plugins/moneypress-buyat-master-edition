<?php

/**
 * We need the generic WPCSL plugin class, since that is the
 * foundation of much of our plugin.  So here we make sure that it has
 * not already been loaded by another plugin that may also be
 * installed, and if not then we load it.
 */
if (defined('MP_BUYAT_PLUGINDIR')) {
    if (class_exists('wpCSL_plugin__mpbuy', false) === false) {
        require_once(MP_BUYAT_PLUGINDIR.'WPCSL-generic/classes/CSL-plugin.php');
    }
    
    //// SETTINGS ////////////////////////////////////////////////////////
    
    global $MP_buyat_plugin;
    $MP_buyat_plugin = new wpCSL_plugin__mpbuy(
        array(
            'prefix'                => MP_BUYAT_PREFIX,
            'css_prefix'            => 'csl_themes',
            'name'                  => 'MoneyPress BuyAt Edition',
            'url'                   => 'http://www.cybersprocket.com/products/moneypress-buyat-edition/',
            'support_url'           => 'http://redmine.cybersprocket.com/projects/moneypress-buyat/wiki',
            'purchase_url'          => 'http://www.cybersprocket.com/products/moneypress-buyat-edition/',
            'cache_path'            => MP_BUYAT_PLUGINDIR . 'cache',
            'plugin_url'            => MP_BUYAT_PLUGINURL,
            'plugin_path'           => MP_BUYAT_PLUGINDIR,
            'basefile'              => MP_BUYAT_BASENAME,
            
            'has_packages'          => true,
            
            'use_obj_defaults'      => true,
            
            'driver_name'           => 'Buyat',
            'driver_type'           => 'Panhandler',
            'driver_args'           => array(
                'api_key' => get_option('csl-mp-buyat-api_key'),
                'plus_pack_enabled' => get_option(MP_BUYAT_PREFIX.'-MPBUY-PLUS-isenabled')
                ),            
            
            'shortcodes'            => array('mp-buyat','mp_buyat')
        )
    );
    
    
    // Setup our optional packages
    //
    add_options_packages_for_mpbuy();         
}

/**************************************
 ** function: list_options_packages_for_mpbuy
 **
 ** Setup the option package list.
 **/
function add_options_packages_for_mpbuy() {
    global $MP_buyat_plugin;   
    
    // Add : Plus Pack
    //
    $MP_buyat_plugin->license->add_licensed_package(
            array(
                'name'              => 'Plus Pack',
                'help_text'         => 'A variety of enhancements are provided with this package.  ' .
                                       'See the <a href="'.$MP_buyat_plugin->purchase_url.'" target="Cyber Sprocket">product page</a> for details.  If you purchased this add-on ' .
                                       'come back to this page to enter the license key to activate the new features.',
                'sku'               => 'MPBUY-PLUS',
                'paypal_button_id'  => '9NGBSBJLV2XNL',
                'paypal_upgrade_button_id' => ''
            )
        );

    if ($MP_buyat_plugin->license->packages['Plus Pack']->isenabled_after_forcing_recheck()) {
        $MP_buyat_plugin->themes_enabled = true;
    }       
}

