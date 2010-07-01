<?php
/*
  Plugin Name: MoneyPress : BuyAt Master Edition
  Plugin URI: http://www.cybersprocket.com/products/moneypress-buyat-edition/
  Description: MoneyPress BuyAt Master Edition allows you to quickly and easily display products from any BuyAt advertising partner on any page or post via a simple shortcode.
  Author: Cyber Sprocket Labs
  Version: 1.01
  Author URI: http://www.cybersprocket.com/
  License: GPL3

*/

/*	Copyright 2010  Cyber Sprocket Labs (info@cybersprocket.com)

        This program is free software; you can redistribute it and/or modify
        it under the terms of the GNU General Public License as published by
        the Free Software Foundation; either version 3 of the License, or
        (at your option) any later version.

        This program is distributed in the hope that it will be useful,
        but WITHOUT ANY WARRANTY; without even the implied warranty of
        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
        GNU General Public License for more details.

        You should have received a copy of the GNU General Public License
        along with this program; if not, write to the Free Software
        Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


if (defined('MP_BUYAT_PLUGINDIR') === false) {
    define('MP_BUYAT_PLUGINDIR', plugin_dir_path(__FILE__));
}

if (defined('MP_BUYAT_PLUGINURL') === false) {
    define('MP_BUYAT_PLUGINURL', plugins_url('',__FILE__));
}

require_once('include/config.php');

if (class_exists('PanhandlerProduct') === false) {
    try {
        require_once('Panhandler/Panhandler.php');
    }
    catch (PanhandlerMissingRequirement $exception) {
        add_action('admin_notices', array($exception, 'getMessage'));
        exit(1);
    }
}

if (class_exists('BuyatDriver') === false) {
    try {
        require_once('Panhandler/Drivers/Buyat.php');
    }
    catch (PanhandlerMissingRequirement $exception) {
        add_action('admin_notices', array($exception, 'getMessage'));
        exit(1);
    }
}

add_filter('wp_print_styles', 'MP_buyat_user_css');
function MP_buyat_user_css() {
    wp_enqueue_style('mp_buyat_css', plugins_url('css/mp-buyat.css', __FILE__));
}
?>
