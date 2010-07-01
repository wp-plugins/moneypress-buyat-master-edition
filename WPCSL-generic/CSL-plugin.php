<?php
require_once('CSL-settings_class.php');
require_once('CSL-notifications_class.php');
require_once('CSL-license_class.php');
require_once('CSL-cache_class.php');

/**
 * This class does most of the heavy lifting for creating a plugin.
 * It takes a hash as its one constructor argument, which can have the
 * following keys and values:
 *
 *     * 'name' :: The name of the plugin.
 *
 *     * 'prefix' :: A string used to prefix all of the Wordpress
 *       settings for the plugin.
 *
 *     * 'url' :: The URL for the product page at Cyber Sprocket Labs.
 *
 *     * 'driver_defaults' :: A hash where the keys are the names of
 *       support options for a Panhandler driver, and the values are
 *       the names of Wordpress settings which will provide the
 *       default values for those driver options.  See the method
 *       'get_supported_options()' in the Panhandler code for a
 *       description of driver options.  The names of the settings
 *       should not include the prefix, i.e. write
 *
 *           'driver_defaults' => array(
 *               'keywords' => 'keywords'
 *           )
 *
 *       instead of
 *
 *           'driver_defaults' => array(
 *               'keywords' => 'csl-mp-ebay-keywords'
 *           )
 *
 */
class wpCSL_plugin {

  function __construct($params) {
    foreach ($params as $name => $value) {
      $this->$name = $value;
    }


    $this->notifications_config = array(
                                        'prefix' => $this->prefix,
                                        'name' => $this->name,
                                        'url' => 'options-general.php?page='.$this->prefix.'-options',
                                        );

    $this->settings_config = array(
                                   'prefix' => $this->prefix,
                                   'plugin_url' => $this->plugin_url,
                                   'name' => $this->name,
                                   'url' => $this->url,
                                   'paypal_button_id' => $this->paypal_button_id
                                   );

    $this->license_config = array(
                                  'prefix' => $this->prefix
                                  );

    $this->cache_config = array(
                                'prefix' => $this->prefix,
                                'path' => $this->cache_path
                                );

    $this->initialize();
  }

  /* Method: CSL_ARRAY_FILL_KEYS
   * Our own version of the php5.2 array_fill_keys
   * So we can hopefully stay with php5.1 compatability
   */
  function csl_array_fill_keys($target,$value='') {
    if(is_array($target)) {
        foreach($target as $key => $val) {
            $filledArray[$val] = is_array($value) ? $value[$key] : $value;
        }
    }
    return $filledArray;
  }

  function create_notifications($class = 'none') {
    switch ($class) {
    case 'none':
      break;

    case 'wpCSL_notifications':
    case 'default':
    default:
      $this->notifications = new wpCSL_notifications($this->notifications_config);

    }
  }

  function create_settings($class = 'none') {
    switch ($class) {
    case 'none':
      break;

    case 'wpCSL_settings':
    case 'default':
    default:
      $this->settings = new wpCSL_settings($this->settings_config);

    }
  }

  function create_license($class = 'none') {
    switch ($class) {
    case 'none':
      break;

    case 'wpCSL_license':
    case 'default':
    default:
      $this->license = new wpCSL_license($this->license_config);

    }
  }

  function create_cache($class = 'none') {
    switch ($class) {
    case 'none':
      break;

    case 'wpCSL_cache':
    case 'default':
    default:
      $this->cache = new wpCSL_cache($this->cache_config);

    }
  }


  function create_options_page() {
    add_options_page($this->name . ' Options', $this->name, 'administrator', $this->prefix . '-options', array($this->settings, 'render_settings_page'));
  }

  function create_objects() {
    if (isset($this->use_obj_defaults) && $this->use_obj_defaults) {
      $this->create_notifications('default');
      $this->create_settings('default');
      $this->create_license('default');
      $this->create_cache('default');
    } else {
      if (isset($this->notifications_obj_name))
        $this->create_notifications($this->notifications_obj_name);
      if (isset($this->settings_obj_name))
        $this->create_settings($this->settings_obj_name);
      if (isset($this->license_obj_name))
        $this->create_license($this->license_obj_name);
      if (isset($this->cache_obj_name))
        $this->create_cache($this->cache_obj_name);
    }
  }

  // What did you say? Refactoring what now? I don't know what that is
  function add_refs() {
    // Notifications doesn't require any other objects yet

    // Settings
    if (isset($this->settings)) {
      if (isset($this->notifications) && !isset($this->settings->notifications))
        $this->settings->notifications = &$this->notifications;
      if (isset($this->license) && !isset($this->settings->license))
        $this->settings->license = &$this->license;
      if (isset($this->cache) && !isset($this->settings->cache))
        $this->settings->cache = &$this->cache;
    }

    // Cache
    if (isset($this->cache)) {
      if (isset($this->settings) && !isset($this->cache->settings))
        $this->cache->settings = &$this->settings;
      if (isset($this->notifications) && !isset($this->cache->notifications))
        $this->cache->notifications = &$this->notifications;
    }

    // License
    if (isset($this->license)) {
      if (isset($this->notifications) && !isset($this->license->notifications))
        $this->license->notifications = &$this->notifications;
    }

  }

  function initialize() {
    $this->create_objects();
    $this->add_refs();
    if (isset($this->driver_name))
      $this->load_driver();
    $this->add_wp_actions();
  }

  function add_wp_actions() {
    if ( is_admin() ) {
      add_action('admin_menu', array($this, 'create_options_page'));
      add_action('admin_init', array($this, 'admin_init'));
      add_action('admin_notices', array($this->notifications, 'display'));
    } else {
      // non-admin enqueues, actions, and filters
      add_action('wp_head', array($this, 'checks'));
      add_filter('wp_print_scripts', array($this, 'user_header_js'));
      add_filter('wp_print_styles', array($this, 'user_header_css'));
    }

    // Only add shortcodes if there is a driver to use
    if (isset($this->driver)) {
      // Custom shortcodes
      if (isset($this->shortcodes)) {
        if (is_array($this->shortcodes)) {
          foreach ($this->shortcodes as $shortcode) {
            add_shortcode($shortcode, array($this, 'shortcode_show_items'));
          }
        } else add_shortcode($this->shortcodes, array($this, 'shortcode_show_items'));
      }

      // Automatic shortcodes
      // This should cover any basic typos involving dashes or underscores
      add_shortcode($this->prefix.'_show-items', array($this, 'shortcode_show_items'));
      add_shortcode($this->prefix.'_show_items', array($this, 'shortcode_show_items'));
      add_shortcode($this->prefix.'-show-items', array($this, 'shortcode_show_items'));
      add_shortcode($this->prefix.'-show_items', array($this, 'shortcode_show_items'));
    }
  }

  function admin_init() {
    $this->add_display_settings();
    $this->settings->register();
    $this->checks();
  }

  function checks() {
    if (isset($this->cache)) {
      $this->cache->check_cache();
    }

    if (isset($this->license)) {
      $this->license->check_product_key();
    }
  }

  function load_driver() {
    if (!class_exists('PanhandlerProduct')) {
      require_once($this->plugin_path . 'Panhandler/Panhandler.php');
    }

    try {
      require_once($this->plugin_path . 'Panhandler/Drivers/'. $this->driver_name .'.php');
    }
    catch (PanhandlerError $e) {
      $this->notifications->add_notice(1, $e->getMessage());
    }

    if (class_exists($this->driver_name.'Driver')) {
      try {
        $reflectionDriver = new ReflectionClass($this->driver_name . 'Driver');
        $this->driver = $reflectionDriver->newInstanceArgs( ((isset($this->driver_args)) ? $this->driver_args : array()) );
      }
      catch (Exception $e) {
        $this->notifications->add_notice(1, $e->getMessage());
      }
    }
  }

  function add_display_settings() {
    if (get_option($this->prefix.'-locale')) {
      setlocale(LC_MONETARY, get_option($this->prefix.'-locale'));
    }

    $this->settings->add_section(array(
                                   'name' => 'Display Settings',
                                   'description' => ''
                                   ));
    if (exec('locale -a', $locales)) {
        $locale_custom = '';

      foreach ($locales as $locale) {
        $locale_custom .= "<option".((get_option($this->prefix.'-locale') == $locale) ? ' selected' : '').">$locale</option>\n";
      }

      $this->settings->add_item('Display Settings', 'Locale', $this->prefix.'-locale', 'custom', false, null,
                                "<select name=\"{$this->prefix}-locale\">
                                 $locale_custom
                                 </select>"
                                );
    }

    if (function_exists('money_format')) {
      $this->settings->add_item('Display Settings', 'Money Format', $this->prefix.'-money_format', 'custom', false, null,
                                "<select name=\"{$this->prefix}-money_format\">
<option value=\"%!i\"".((get_option($this->prefix.'-money_format') == '%!i') ? ' selected' : '').">". money_format('%!i', 1234.56) ."</option>
<option value=\"%!^i\"".((get_option($this->prefix.'-money_format') == '%!^i') ? ' selected' : '').">". money_format('%!^i', 1234.56) ."</option>
<option value=\"%!=*(#10.2n\"".((get_option($this->prefix.'-money_format') == '%!=*(#10.2n') ? ' selected' : '').">". money_format('%!=*(#10.2n', 1234.56) ."</option>
<option value=\"%!=*^-14#8.2i\"".((get_option($this->prefix.'-money_format') == '%!=*^-14#8.2i') ? ' selected' : '').">". money_format('%!=*^-14#8.2i', 1234.56) ."</option>
</select>
<div>This is based on your current locale, which is set to <code>". setlocale(LC_MONETARY, 0) ."</code></div>"
                                     );
    }
  }

  function display_products($products) {
    foreach ($products as $product) {
      if (is_a($product, 'PanhandlerProduct')) {

        $product_output[] = "<div class=\"{$this->prefix}-product\">";
        $product_output[] = "<h3>{$product->name}</h3>";
        // --- DISABLED ---
        // This check takes entirely too long and I have yet to find
        // any method that works properly *and* quickly.
        //
        // Clicking on the zoom link for a url of an image that's not
        // there causes thickbox to hang indefinitely, therefore we only
        // show the link (and the image) if the file exists.
//        if (wpCJ_url_exists($product->image_urls[0])) {
          $product_output[] = "<div class=\"{$this->prefix}-left\">";
          $product_output[] = "<a href=\"{$product->web_urls[0]}\" target=\"newinfo\">";
          $product_output[] = "<img src=\"{$product->image_urls[0]}\" alt=\"{$product->name}\" title=\"{$product->name}\" />";
          $product_output[] = "</a>";
          $product_output[] = "<a class=\"thickbox\" href=\"{$product->image_urls[0]}\">+zoom</a>";
          $product_output[] = "</div>";
//        }
        $product_output[] = "<span>";
        $product_output[] = "<p>{$product->description}</p>";
        $product_output[] = "<p>";
        $product_output[] = $product->currency ."\n";
        $product_output[] = "$<a href=\"{$product->web_urls[0]}\" target=\"newinfo\">";
        if (function_exists('money_format') && get_option($this->prefix.'-money_format') && (get_option($this->prefix.'-money_format') != '')) {
          $product_output[] = money_format(get_option($this->prefix.'-money_format'), (float)$product->price);
        } else {
          $product_output[] = number_format((float)$product->price, 2);
        }
        $product_output[] =  "</a>";
        $product_output[] = "</p>";
        $product_output[] = "</span>\n</div>";

      }
    }

    return implode("\n", $product_output);
  }


  /* Method: SHORTCODE_SHOW_ITEMS
  *
  * Shows the products in a formatted output on the page wherever the shortcode appears.
  * This is the default output, custom shortcodes and functions can be put in the main
  * calling function.
  *
  */
  function shortcode_show_items($atts, $content = NULL) {
          global $current_user;
          get_currentuserinfo();

          if ( ($current_user->wp_capabilities['administrator']) || ($current_user->user_level == '10') || get_option($this->prefix.'-purchased')) {

                  // Filter out erroneous attributes
                  if (is_array($atts)) {
                          $atts = array_intersect_key( $atts, $this->csl_array_fill_keys( $this->driver->get_supported_options(), 'temp' ) );
                  }

                  // We need some user defaults

                  // If there's a custom array set, use that to populate the list
                  if (isset($this->driver_defaults) && is_array($this->driver_defaults)) {
                          $defaults = $this->apply_driver_defaults($this->driver_defaults);
                  } else {
                          // Otherwise, grab all of the user defaults from wordpress
                          foreach($this->driver->get_supported_options() as $key) {
                                  if (get_option($this->prefix .'-'. $key)) {
                                          $defaults[$key] = get_option($this->prefix .'-'. $key);
                                  }
                          }
                  }

                  // Send them to the driver (if they exist)
                  if (isset($defaults)) {
                          $this->driver->set_default_option_values($defaults);
                  }

                  if (isset($this->cache) && get_option($this->prefix.'-cache_enable')) {
                          if (!($products = $this->cache->load(md5(implode(',',$atts)))) ) {
                                  $products = $this->driver->get_products($atts);
                          }
                  } else {
                          try {
                                  $products = $this->driver->get_products($atts);
                          }
                          // Deal with errors
                          // These should probably be posted to the notifications system...
                          catch (PanhandlerError $error) {
                                  return $error->message;
                          }
                  }

                  if (is_a($products, 'PanhandlerError')) return $products->message;
                  else {
                          if (isset($this->cache) && get_option($this->prefix.'-cache_enable')) {
                                  $this->cache->save(md5(implode(',',$atts)), $products);
                          }
                  }

                  // If there are products, display them
                  if (count($products) > 0) {
                          return $this->display_products($products);
                  } else return "No products found";
          }
  }

  function user_header_js() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('thickbox');
  }

  function user_header_css() {

    if (isset($this->css_url))
      wp_enqueue_style('wpcjcss', $this->css_url);
    else if (isset($this->plugin_url)) wp_enqueue_style($this->prefix.'css', $this->plugin_url . '/css/'.$this->prefix.'.css');
    wp_enqueue_style('thickbox');
  }

  // Populate an array with values from wordpress if they exist, will
  // propogate through an array structure recursively
  function apply_driver_defaults(&$defaults) {
          foreach ($defaults as $key => $value) {
                  if (is_array($value)) {
                          $results[$key] = $this->apply_driver_defaults($value);
                  }
                  else {
                          if (get_option($this->prefix .'-'.$value)) {
                                  $results[$value] = get_option($this->prefix .'-'.$value);
                          }
                  }
          }

          return $results;
  }
}

?>