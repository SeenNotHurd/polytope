<?php
/*
MarketPress Paytrail Gateway Plugin
Author: Thomas Hurd (booncon oy)
*/

require_once("Verkkomaksut_Module_Rest.php");

class MP_Gateway_Paytrail extends MP_Gateway_API {

  //private gateway slug. Lowercase alpha (a-z) and dashes (-) only please!
  var $plugin_name = 'paytrail';
  
  //name of your gateway, for the admin side.
  var $admin_name = '';
  
  //public name of your gateway, for lists and such.
  var $public_name = '';
  
  //url for an image for your checkout method. Displayed on method form
  var $method_img_url = '';

  //url for an submit button image for your checkout method. Displayed on checkout form if set
  var $method_button_img_url = '';
  
  //whether or not ssl is needed for checkout page
  var $force_ssl = false;
  
  //always contains the url to send payment notifications to if needed by your gateway. Populated by the parent class
  var $ipn_url;
  
  //whether if this is the only enabled gateway it can skip the payment_form step
  var $skip_form = false;
  
  //only required for global capable gateways. The maximum stores that can checkout at once
  var $max_stores = 1;

  // Paytrail vars
  var $API_merchant_id = '';
  var $API_merchant_secret = ''; 
  var $API_urlset = '';

  /****** Below are the public methods you may overwrite via a plugin ******/

  /**
   * Runs when your class is instantiated. Use to setup your plugin instead of __construct()
   */
  function on_creation() {
    global $mp;
    $settings = get_option('mp_settings');

    //set names here to be able to translate
    $this->admin_name = __('Paytrail (formerly Suomen Verkkomaksut)', 'mp');
    $this->public_name = __('Paytrail', 'mp');

    // //set paypal vars
    // /** @todo Set all array keys to resolve Undefined indexes notice */;
    // if ( $mp->global_cart )
    //   $settings = get_site_option( 'mp_network_settings' );

    $this->successURL = $settings['gateways']['paytrail']['successURL'];
    $this->failURL = $settings['gateways']['paytrail']['failURL'];
    $this->confirmURL = $settings['gateways']['paytrail']['confirmURL'];
    $this->pendingURL = $settings['gateways']['paytrail']['pendingURL'];
    // $this->API_Username = $settings['gateways']['paypal-express']['api_user'];
    // $this->API_Password = $settings['gateways']['paypal-express']['api_pass'];
    // $this->API_Signature = $settings['gateways']['paypal-express']['api_sig'];
    // $this->currencyCode = $settings['gateways']['paypal-express']['currency'];
    // $this->locale = $settings['gateways']['paypal-express']['locale'];
    // $this->returnURL = mp_checkout_step_url('confirm-checkout');
    // $this->cancelURL = mp_checkout_step_url('checkout') . "?cancel=1";
    // $this->version = "69.0"; //api version

    // redefine vars for testing mode.
    if ($settings['gateways']['paytrail']['mode'] == 'live') {
      $this->API_merchant_id = $settings['gateways']['paytrail']['api_merchant_id'];
      $this->API_merchant_secret = $settings['gateways']['paytrail']['api_merchant_secret'];;
    } else {
      // Test credentials
      $this->API_merchant_id = '13466';
      $this->API_merchant_secret = '6pKF4jkv97zmqBJ3ZL8gUw5DfT2NMQ';
    }
  }

  /**
   * Return fields you need to add to the payment screen, like your credit card info fields.
   *  If you don't need to add form fields set $skip_form to true so this page can be skipped
   *  at checkout.
   *
   * @param array $cart. Contains the cart contents for the current blog, global cart if $mp->global_cart is true
   * @param array $shipping_info. Contains shipping info and email in case you need it
   */
  function payment_form($cart, $shipping_info) {
    //global $mp;

    echo "<pre>
    Billing info same as shipping?
    OR==
    Company Name
    First name
    Last name
    email (from shipping info)
    Tel
    Mob
    Street address 1
    Street Address 2
    Post code suburb
    State? (not sent)
    Country

    </pre>";

    // TODO: add form for billing address according to requirements of Paytrail
  }
    
  /**
   * Use this to process any fields you added. Use the $_POST global,
   *  and be sure to save it to both the $_SESSION and usermeta if logged in.
   *  DO NOT save credit card details to usermeta as it's not PCI compliant.
   *  Call $mp->cart_checkout_error($msg, $context); to handle errors. If no errors
   *  it will redirect to the next step.
   *
   * @param array $cart. Contains the cart contents for the current blog, global cart if $mp->global_cart is true
   * @param array $shipping_info. Contains shipping info and email in case you need it
   */
  function process_payment_form($cart, $shipping_info) {
    //global $mp;

    //Process into $_session the required data from the payment form.


    //wp_die( __("You must override the process_payment_form() method in your {$this->admin_name} payment gateway plugin!", 'mp') );
  }
    
  /**
   * Return the chosen payment details here for final confirmation. You probably don't need
   *  to post anything in the form as it should be in your $_SESSION var already.
   *
   * @param array $cart. Contains the cart contents for the current blog, global cart if $mp->global_cart is true
   * @param array $shipping_info. Contains shipping info and email in case you need it
   */
  function confirm_payment_form($cart, $shipping_info) {
    // Let's get the marketpress vars and settign vars.
    global $mp;
    $settings = get_option('mp_settings');

    // Next we can define some variables that we will use later on.

    // Now now we check if we are coming from a failed payment.
    if(!isset($_GET["ORDER_NUMBER"]) && !isset($_GET["TIMESTAMP"]) && !isset($_GET["PAID"]) && !isset($_GET["METHOD"]) && !isset($_GET["RETURN_AUTHCODE"])) {
    } else {
      // Grab the info from Verkko Maksut
      $module = new Verkkomaksut_Module_Rest($this->API_merchant_id, $this->API_merchant_secret);
      if($module->confirmPayment($_GET["ORDER_NUMBER"], $_GET["TIMESTAMP"], $_GET["PAID"], $_GET["METHOD"], $_GET["RETURN_AUTHCODE"])) {
        // Payment receipt is valid
        // echo $_GET["ORDER_NUMBER"].'<br>';
        // echo $_GET["TIMESTAMP"].'<br>';
        // echo $_GET["PAID"].'<br>';
        // echo $_GET["METHOD"].'<br>';
        // echo $_GET["RETURN_AUTHCODE"].'<br>';
      } else {
        echo '<div class="mp_checkout_error">' . __('Your Paytrail payment was cancelled or failed.', 'mp') . '</div>';
        // Payment receipt was not valid, possible payment fraud attempt
        // echo $_GET["ORDER_NUMBER"].'<br>';
        // echo $_GET["TIMESTAMP"].'<br>';
        // echo $_GET["PAID"].'<br>';
        // echo $_GET["METHOD"].'<br>';
        // echo $_GET["RETURN_AUTHCODE"].'<br>';
      }
    }

    // Create the parts of the JSON string to be sent to Paytrail

    // First the URLs, taken from the settings.
    $urlset = new Verkkomaksut_Module_Rest_Urlset(
        home_url($successURL), // return url for successful payment
        home_url($failURL), // return url for failed payment
        home_url($confirmURL),  // url for payment confirmation from Paytrail server
        home_url($pendingURL) // pending url is not in use
    );

    // We create the 'profile' or contact array for the purchaser, which we take from the payment form function above
    $contact = new Verkkomaksut_Module_Rest_Contact(
        $shipping_info["name"],                             // first name
        $shipping_info["name"],                           // surname
        $shipping_info["email"],      // email address
        $shipping_info["address1"].$shipping_info["address2"],                    // street address
        $shipping_info["zip"],                            // postal code
        $shipping_info["city"],                         // post office
        $shipping_info["country"],                               // maa (ISO-3166)
        $shipping_info["phone"],                        // telephone number
        $shipping_info["phone"],                                 // mobile phone number
        "Demo Company Ltd"                  // company name
    );

    // We make some changes to the array contents
    // Generate an order number early for the payment use.
    $orderNumber = strtoupper($mp->generate_order_id());

    echo '<pre>';
    print_r($_SESSION);
    echo '</pre>';

    // If the current session language is one of the three supported, we use that for the payment, otherwise we use the selected default.
    // TODO: see if there is an array somewhere to pull this from, or then we make one, and otherwise we give an option in settings
        // to choose a default language to fallback to

    // First we get some variables sorted.

    // Calculate Shipping TODO: move to shipping section.
    //$shipping_cost = calculate_shipping($price, $total, $cart, $shipping_info['address1'], $shipping_info['address2'], $shipping_info['city'], $shipping_info['state'], $shipping_info['zip'], $shipping_info['country'], $selected_option);

    $payment = new Verkkomaksut_Module_Rest_Payment_E1($orderNumber, $urlset, $contact);

    // Adding one or more product rows to the payment
    foreach( $cart as $key => $product ) {
      print_r($product);
      $payment->addProduct(
          $product[0]['name'],                     // product title
          $product[0]['SKU'],                            // product code
          $product[0]['quantity'],                             // product quantity
          $product[0]['price'],                            // product price (/apiece)
          // TODO: if product has special tax rate, insert, otherwise use normal rate.
          $settings['tax']['rate']*100,                            // Tax percentage
          // TODO: calculate from diff prices (get product function?)
          "0.00",                             // Discount percentage 
          Verkkomaksut_Module_Rest_Product::TYPE_NORMAL // Product type     
      );
    }
    // Adding cart-wide discounts
    // TODO: Get prices based on calculation
    //$shipping_applied = false;
    //if($shipping_applied) {
      $payment->addProduct(
          "discount",                     // product title
          "code",                            // product code
          "1",                             // product quantity
          "-20.00",                            // product price (/apiece)
          $settings['tax']['rate']*100,                            // Tax percentage
          "0.00",                             // Discount percentage
          Verkkomaksut_Module_Rest_Product::TYPE_NORMAL // Product type     
      );
    //}

    // Adding handling charges, if any
    // if($shipping_cost > 0) {
    //   $payment->addProduct(
    //       "Shipping",                     // product title
    //       "",                            // product code
    //       "1",                             // product quantity
    //       $shipping_cost,                            // product price (/apiece)
    //       $settings['tax']['rate']*100,                            // Tax percentage // TODO: check if applied
    //       "0.00",                             // Discount percentage
    //       Verkkomaksut_Module_Rest_Product::TYPE_POSTAL // Product type     
    //   );
    // }

    // Adding Shipping Charges
    // TODO: Get prices based on calculation
    if($shipping_cost > 0) {
      $payment->addProduct(
          "Shipping",                     // product title
          "",                            // product code
          "1",                             // product quantity
          $shipping_cost,                            // product price (/apiece)
          $settings['tax']['rate']*100,                            // Tax percentage // TODO: check if applied
          "0.00",                             // Discount percentage
          Verkkomaksut_Module_Rest_Product::TYPE_POSTAL // Product type     
      );
    }

    // Changing payment default settings, changing payment method selection page language into English
    //here the he default language is Finnish. See other options
    // from PHP class.
    // TODO: Change to match user language of website, otherwise English as default
    // TODO: move this up or the rest down?
    $payment->setLocale("en_US");

    // Sending payment to Paytrail service and handling possible errors
    $module = new Verkkomaksut_Module_Rest($API_merchant_id, $API_merchant_secret);
    try {
        $result = $module->processPayment($payment);
    }
    catch(Verkkomaksut_Exception $e) {
        // processing the error
        // Error description available $e->getMessage()
        die("Error in creating payment to Paytrail service");

    }

    // Use payment url and token as you wish // This will auto redirect.
    //header("Location: {$result->url}");

    // Remove/modify confirm button to actually make the payment, create a new page which has the jquery embedded, OR do we add this to the bottom of the page? or two options?


    // TODO: change this to the button at the base of the page, add a setting to add this to the same page
    echo '<p id="payment"><a href="'.$result->url.'">Go to payments</a></p>';
    //wp_die( __("You must override the confirm_payment_form() method in your {$this->admin_name} payment gateway plugin!", 'mp') );
  }

  /**
   * Use this to do the final payment. Create the order then process the payment. If
   *  you know the payment is successful right away go ahead and change the order status
   *  as well.
   *  Call $mp->cart_checkout_error($msg, $context); to handle errors. If no errors
   *  it will redirect to the next step.
   *
   * @param array $cart. Contains the cart contents for the current blog, global cart if $mp->global_cart is true
   * @param array $shipping_info. Contains shipping info and email in case you need it
   */
  function process_payment($cart, $shipping_info) {
    // This step is unnecessary for us.
    //wp_die( __("You must override the process_payment() method in your {$this->admin_name} payment gateway plugin!", 'mp') );
  }

  /**
   * Runs before page load incase you need to run any scripts before loading the success message page
   */
  function order_confirmation($order) {

    // look at process_payment in paypal for a process to create payment info and then create the order.

    $module = new Verkkomaksut_Module_Rest($API_merchant_id, $API_merchant_secret);
    if($module->confirmPayment($_GET["ORDER_NUMBER"], $_GET["TIMESTAMP"], $_GET["PAID"], $_GET["METHOD"], $_GET["RETURN_AUTHCODE"])) {
      // Payment receipt is valid
      // TODO: Do I save this data somewhere? Look at paypal
      // TODO: look at how the return data is supposed to be used.
      // If needed, the used payment method can be found from the variable $_GET["METHOD"]
      // and order number for the payment from the variable $_GET["ORDER_NUMBER"]
      echo '<pre>success</pre>';
      echo $_GET["ORDER_NUMBER"].'<br>';
      echo $_GET["TIMESTAMP"].'<br>';
      echo $_GET["PAID"].'<br>';
      echo $_GET["METHOD"].'<br>';
      echo $_GET["RETURN_AUTHCODE"].'<br>';
    }
    else {
      // Payment receipt was not valid, possible payment fraud attempt
      echo '<pre>fail</pre>';
      echo $_GET["ORDER_NUMBER"].'<br>';
      echo $_GET["TIMESTAMP"].'<br>';
      // echo $_GET["PAID"].'<br>';
      // echo $_GET["METHOD"].'<br>';
      echo $_GET["RETURN_AUTHCODE"].'<br>';
    }

    // if approved then change status of order to paid
    // wp_die( __("You must override the order_confirmation() method in your {$this->admin_name} payment gateway plugin!", 'mp') );
  }

  /**
   * Filters the order confirmation email message body. You may want to append something to
   *  the message. Optional
   *
   * Don't forget to return!
   */
  function order_confirmation_email($msg, $order) {
    return $msg;
  }
  
  /**
   * Return any html you want to show on the confirmation screen after checkout. This
   *  should be a payment details box and message.
   *
   * Don't forget to return!
   */
  function order_confirmation_msg($content, $order) {
    //wp_die( __("You must override the order_confirmation_msg() method in your {$this->admin_name} payment gateway plugin!", 'mp') );
  }
  
  /**
   * Echo a settings meta box with whatever settings you need for you gateway.
   *  Form field names should be prefixed with mp[gateways][plugin_name], like "mp[gateways][plugin_name][mysetting]".
   *  You can access saved settings via $settings array.
   */
  function gateway_settings_box($settings) {
    global $mp;
    ?>

    <div id="mp_paytrail" class="postbox">
      <h3 class='hndle'><span><?php _e('Paytrail Checkout Settings', 'mp'); ?></span></h3>
      <div class="inside">
        <span class="description"><?php _e('Paytrail description. <a target="_blank" href="https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_ECGettingStarted">More Info &raquo;</a>', 'mp') ?></span>
        <?php if($settings['currency'] != 'EUR') { ?>
        <span class="description mp_checkout_error"><?php _e('Your Marketpress store\'s currency is not set to Euros. Paytrail can only process orders whose currency is in Euros. Please change this by going to the \'General Settings\' tab', 'mp') ?></span>
        <?php } ?>
        <table class="form-table">
          <tr<?php echo ($mp->global_cart) ? ' style="display:none;"' : ''; ?>>
            <th scope="row"><?php _e('Paytrail mode', 'mp') ?></th>
            <td>
            <select name="mp[gateways][paytrail][mode]">
              <option value="sandbox"<?php selected($settings['gateways']['paytrail']['mode'], 'sandbox') ?>><?php _e('Test', 'mp') ?></option>
              <option value="live"<?php selected($settings['gateways']['paytrail']['mode'], 'live') ?>><?php _e('Live', 'mp') ?></option>
            </select>
            </td>
          </tr>
          <tr<?php echo ($mp->global_cart) ? ' style="display:none;"' : ''; ?>>
            <th scope="row"><?php _e('Paytrail API Credentials', 'mp') ?></th>
            <td>
              <span class="description"><?php _e('You need an account with Paytrail. <a target="_blank" href="https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_ECAPICredentials">Instructions &raquo;</a>', 'mp') ?></span>
              <p><label><?php _e('API Merchant ID', 'mp') ?><br />
              <input value="<?php echo esc_attr($settings['gateways']['paytrail']['api_merchant_id']); ?>" size="10" name="mp[gateways][paytrail][api_merchant_id]" type="text" />
              </label></p>
              <p><label><?php _e('API Merchant Secret', 'mp') ?><br />
              <input value="<?php echo esc_attr($settings['gateways']['paytrail']['api_mechant_secret']); ?>" size="20" name="mp[gateways][paytrail][api_merchant_secret]" type="text" />
              </label></p>
            </td>
          </tr>
          <tr<?php echo ($mp->global_cart) ? ' style="display:none;"' : ''; ?>>
            <th scope="row"><?php _e('Paytrail API URLs', 'mp') ?></th>
            <td>
              <span class="description"><?php _e('Here you can customise URLs', 'mp') ?></span>
              <p><label><?php _e('Success URL', 'mp') ?><br />
              <input value="<?php echo esc_attr($settings['gateways']['paytrail']['successURL']); ?>" size="40" name="mp[gateways][paytrail][successURL]" type="text" />
              </label></p>
              <p><label><?php _e('Failure URL', 'mp') ?><br />
              <input value="<?php echo esc_attr($settings['gateways']['paytrail']['failURL']); ?>" size="40" name="mp[gateways][paytrail][failURL]" type="text" />
              </label></p>
              <p><label><?php _e('Notify URL', 'mp') ?><br />
              <input value="<?php echo esc_attr($settings['gateways']['paytrail']['confirmURL']); ?>" size="40" name="mp[gateways][paytrail][confirmURL]" type="text" />
              </label></p>
              <p><label><?php _e('Pending URL (NOT IN USE)', 'mp') ?><br />
              <input value="<?php echo esc_attr($settings['gateways']['paytrail']['pendingURL']); ?>" size="40" name="mp[gateways][paytrail][pendingURL]" type="text" />
              </label></p> 
            </td>
          </tr>
        </table>
      </div>
    </div>

    <?php

  }
  
  /**
   * Filters posted data from your settings form. Do anything you need to the $settings['gateways']['plugin_name']
   *  array. Don't forget to return!
   */
  function process_gateway_settings($settings) {

    return $settings;
  }
  
  /**
   * Use to handle any payment returns to the ipn_url. Do not display anything here. If you encounter errors
   *  return the proper headers. Exits after.
   */
  function process_ipn_return() {

  }
  
  /****** Do not override any of these private methods please! ******/
  
  //populates ipn_url var
  function _generate_ipn_url() {
    $settings = get_option('mp_settings');
    $this->ipn_url = home_url($settings['slugs']['store'] . '/payment-return/' . $this->plugin_name);
  }
  
  //populates ipn_url var
  function _payment_form_skip($var) {
    return $this->skip_form;
  }
  
  //creates the payment method selections
  function _payment_form_wrapper($content, $cart, $shipping_info) {
    global $mp, $mp_gateway_active_plugins;
    
    if (count((array)$mp_gateway_active_plugins) > 1 && $_SESSION['mp_payment_method'] != $this->plugin_name)
      $hidden = ' style="display:none;"';
      
    $content .= '<div class="mp_gateway_form" id="' . $this->plugin_name . '"' . $hidden . '>';
    $content .= $this->payment_form($cart, $shipping_info);

    $content .= '<p class="mp_cart_direct_checkout">';
    $content .= '<input type="submit" name="mp_payment_submit" id="mp_payment_confirm" value="' . __('Continue Checkout &raquo;', 'mp') . '" />';
    $content .= '</p></div>';
    
    return $content;
  }
  
  //calls the order_confirmation() method on the correct page
  function _checkout_confirmation_hook() {
    global $wp_query, $mp;

    if ($wp_query->query_vars['pagename'] == 'cart') {
      if ($wp_query->query_vars['checkoutstep'] == 'confirmation')
        do_action( 'mp_checkout_payment_pre_confirmation_' . $_SESSION['mp_payment_method'], $mp->get_order($_SESSION['mp_order']) );
    }
  }
  
  //DO NOT override the construct! instead use the on_creation() method.
  function MP_Gateway_API() {
    $this->__construct();
  }

  function __construct() {
  
    $this->_generate_ipn_url();
    
    //run plugin construct
    $this->on_creation();
    
    //check required vars
    if (empty($this->plugin_name) || empty($this->admin_name) || empty($this->public_name))
      wp_die( __("You must override all required vars in your {$this->admin_name} payment gateway plugin!", 'mp') );

    add_filter( 'mp_checkout_payment_form', array(&$this, '_payment_form_wrapper'), 10, 3 );
    add_action( 'template_redirect', array(&$this, '_checkout_confirmation_hook') );
    add_filter( 'mp_payment_form_skip_' . $this->plugin_name, array(&$this, '_payment_form_skip') );
    add_action( 'mp_payment_submit_' . $this->plugin_name, array(&$this, 'process_payment_form'), 10, 2 );
    add_filter( 'mp_checkout_confirm_payment_' . $this->plugin_name, array(&$this, 'confirm_payment_form'), 10, 2 );
    add_action( 'mp_payment_confirm_' . $this->plugin_name, array(&$this, 'process_payment'), 10, 2 );
    add_filter( 'mp_order_notification_' . $this->plugin_name, array(&$this, 'order_confirmation_email'), 10, 2 );
    add_action( 'mp_checkout_payment_pre_confirmation_' . $this->plugin_name, array(&$this, 'order_confirmation') );
    add_filter( 'mp_checkout_payment_confirmation_' . $this->plugin_name, array(&$this, 'order_confirmation_msg'), 10, 2 );
    add_action( 'mp_gateway_settings', array(&$this, 'gateway_settings_box') );
    add_filter( 'mp_gateway_settings_filter', array(&$this, 'process_gateway_settings') );
    add_action( 'mp_handle_payment_return_' . $this->plugin_name, array(&$this, 'process_ipn_return') );
  } 
}

/**
 * Use this function to register your gateway plugin class
 *
 * @param string $class_name - the case sensitive name of your plugin class
 * @param string $plugin_name - the sanitized private name for your plugin
 * @param string $admin_name - pretty name of your gateway, for the admin side.
 * @param bool $global optional - whether the gateway supports global checkouts
 */

mp_register_gateway_plugin( 'MP_Gateway_Paytrail', 'paytrail', __('Paytrail', 'mp'), false, false);

?>