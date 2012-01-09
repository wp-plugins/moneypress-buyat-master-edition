<?php
/*
  Plugin Name: MoneyPress : BuyAt Master Edition
  Plugin URI: http://www.cybersprocket.com/products/moneypress-buyat-edition/
  Description: MoneyPress BuyAt Master Edition allows you to quickly and easily display products from any BuyAt advertising partner on any page or post via a simple shortcode.
  Author: Cyber Sprocket Labs
  Version: 1.1.1
  Author URI: http://www.cybersprocket.com/
  License: GPL3

  
 Copyright (C) 2011 Cyber Sprocket Labs <info@cybersprocket.com>      

 This program is free software; you can redistribute it and/or        
 modify it under the terms of the GNU General Public License          
 as published by the Free Software Foundation; either version 3       
 of the License, or (at your option) any later version.               

 This program is distributed in the hope that it will be useful,      
 but WITHOUT ANY WARRANTY; without even the implied warranty of       
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        
 GNU General Public License for more details.                         

 You should have received a copy of the GNU General Public License    
 along with this program. If not, see <http://www.gnu.org/licenses/>. 
 
 */

 
/// DEBUGGING
/* error_reporting(E_ALL); */
/* ini_set('display_errors', '1'); */



if (defined('MP_BUYAT_PLUGINDIR') === false) {
    define('MP_BUYAT_PLUGINDIR', plugin_dir_path(__FILE__));
}

if (defined('MP_BUYAT_PLUGINURL') === false) {
    define('MP_BUYAT_PLUGINURL', plugins_url('',__FILE__));
}



if (defined('MP_BUYAT_PLUGINDIR') === false) {
    define('MP_BUYAT_PLUGINDIR', plugin_dir_path(__FILE__));
}

if (defined('MP_BUYAT_PLUGINURL') === false) {
    define('MP_BUYAT_PLUGINURL', plugins_url('',__FILE__));
}

if (defined('MP_BUYAT_BASENAME') === false) {
    define('MP_BUYAT_BASENAME', plugin_basename(__FILE__));
}

if (defined('MP_BUYAT_PREFIX') === false) {
    define('MP_BUYAT_PREFIX', 'csl-mp-buyat');
}

if (defined('MP_BUYAT_ADMINPAGE') === false) {
    define('MP_BUYAT_ADMINPAGE', get_option('siteurl') . '/wp-admin/admin.php?page=' . MP_BUYAT_PLUGINDIR );
}



// Include our needed files
//
require_once(MP_BUYAT_PLUGINDIR . '/include/config.php');
require_once(MP_BUYAT_PLUGINDIR . '/include/csl_helpers.php');



// actions
add_action('wp_print_styles', 'setup_stylesheet_for_mpbuy');
add_action('admin_print_styles','setup_ADMIN_stylesheet_for_mpbuy');
add_action('admin_init','setup_admin_interface_for_mpbuy',10);
