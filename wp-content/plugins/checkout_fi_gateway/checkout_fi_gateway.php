<?php

/*
 * Plugin Name: Woocommerce Checkout.fi Payment Gateway
 * Plugin URI:  
 * Description: Adds Checkout.fi Payment Gateway to Woocommerce
 * Author:      booncon oy
 * Author URI:  https://pixels.booncon.com
 *
 * Version:    0.1
 * Requires at least:   3.3
 * Tested up to:        3.7.1
 *
 * Text Domain:         woocommerce
 * Domain Path:         /languages/
 *
 * License: GPLv2
 */

//if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function dpm($data) {
  echo '<pre>';
  print_r($data);
  echo '</pre>';
}

global $woocommerce;
//dpm($woocommerce);

add_action( 'plugins_loaded', 'init_checkout_fi' );

function init_checkout_fi() {
  class WC_Gateway_Checkout_Fi extends WC_Payment_Gateway {

    // Define variables

    private $version    = "0001";
    private $language   = "FI";
    private $country    = "FIN";
    private $currency   = "EUR";
    private $device     = "1";
    private $content    = "1";
    private $type     = "0";
    private $algorithm    = "3";
    private $merchant   = "";
    private $password   = "";
    private $stamp      = 0;
    private $amount     = 0;
    private $reference    = "";
    private $message    = "";
    private $return     = "";
    private $cancel     = "";
    private $reject     = "";
    private $delayed    = "";
    private $delivery_date    = "";
    private $firstname    = "";
    private $familyname   = "";
    private $address    = "";
    private $postcode   = "";
    private $postoffice   = "";
    private $status     = "";
    private $email      = "";
    /**
     * Constructor for the gateway.
     *
     * @access public
     * @return void
     */
    public function __construct() {
      
      $this->id = 'checkout_fi';
      $this->icon = apply_filters('woocommerce_checkout_fi_icon', '');
      $this->has_fields = false;
      $this->method_title = __( 'Checkout.fi', 'woocommerce' );
      $this->method_description = __('The Checkout.fi payment gateway allows payment directly from Finnish banks or via credit card.', 'woocommerce');

      // Load the settings.
      $this->init_form_fields();
      $this->init_settings();

      // Define user set variables
      $this->title = $this->get_option( 'title' );
      $this->description = $this->get_option( 'description' );
      $this->merchant = $this->get_option( 'merchant' ); // merchant id
      $this->password = $this->get_option( 'password' ); // security key (about 80 chars)

      // Actions
      add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

    }

    /**
     * Initialise Gateway Settings Form Fields
     *
     * @access public
     * @return void
     */
    function init_form_fields() {

      $this->form_fields = array(
        'enabled' => array(
          'title' => __( 'Enable/Disable', 'woocommerce' ),
          'type' => 'checkbox',
          'label' => __( 'Enable the Checkout.fi payment gateway', 'woocommerce' ),
          'default' => 'no'
        ),
        'title' => array(
          'title' => __( 'Title', 'woocommerce' ),
          'type' => 'text',
          'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
          'default' => __( 'Checkout.fi', 'woocommerce' ),
          'desc_tip'      => true,
        ),
        'description' => array(
          'title' => __( 'Customer Message', 'woocommerce' ),
          'type' => 'textarea',
          'default' => ''
        ),
        'merchant' => array(
          'title' => __( 'Merchant ID', 'woocommerce' ),
          'type' => 'text',
          'description' => __( 'Your Checkout.fi merchant identification number.', 'woocommerce' ),
          'default' => __( '', 'woocommerce' ),
          //'desc_tip' => true,
        ),
        'password' => array(
          'title' => __( 'Merchant Password', 'woocommerce' ),
          'type' => 'text',
          'description' => __( 'Your merchant password for Checkout.fi.', 'woocommerce' ),
          'default' => __( '', 'woocommerce' ),
          //'desc_tip'      => true,
        )
      );
    }

    /**
     * generates MAC and prepares values for creating payment
     */ 
    public function getCheckoutObject($data) {
      // overwrite default values
      foreach($data as $key => $value) 
      {
        $this->{$key} = $value;
      }

      $mac = strtoupper(md5("{$this->version}+{$this->stamp}+{$this->amount}+{$this->reference}+{$this->message}+{$this->language}+{$this->merchant}+{$this->return}+{$this->cancel}+{$this->reject}+{$this->delayed}+{$this->country}+{$this->currency}+{$this->device}+{$this->content}+{$this->type}+{$this->algorithm}+{$this->delivery_date}+{$this->firstname}+{$this->familyname}+{$this->address}+{$this->postcode}+{$this->postoffice}+{$this->password}"));
      $post['VERSION']    = $this->version;
      $post['STAMP']      = $this->stamp;
      $post['AMOUNT']     = $this->amount;
      $post['REFERENCE']    = $this->reference;
      $post['MESSAGE']    = $this->message;
      $post['LANGUAGE']   = $this->language;
      $post['MERCHANT']   = $this->merchant;
      $post['RETURN']     = $this->return;
      $post['CANCEL']     = $this->cancel;
      $post['REJECT']     = $this->reject;
      $post['DELAYED']    = $this->delayed;
      $post['COUNTRY']    = $this->country;
      $post['CURRENCY']   = $this->currency;
      $post['DEVICE']     = $this->device;
      $post['CONTENT']    = $this->content;
      $post['TYPE']     = $this->type;
      $post['ALGORITHM']    = $this->algorithm;
      $post['DELIVERY_DATE']    = $this->delivery_date;
      $post['FIRSTNAME']    = $this->firstname;
      $post['FAMILYNAME']   = $this->familyname;
      $post['ADDRESS']    = $this->address;
      $post['POSTCODE']   = $this->postcode;
      $post['POSTOFFICE']   = $this->postoffice;
      $post['MAC']      = $mac;

      $post['EMAIL']      = $this->email;
      $post['PHONE']      = $this->phone;

      return $post;
    }

    function process_payment( $order_id ) {
      global $woocommerce;
      
      $order = new WC_Order( $order_id );

      // Mark as on-hold (we're awaiting the cheque)
      $order->update_status('on-hold', __( 'Awaiting cheque payment', 'woocommerce' ));

      // Reduce stock levels
      $order->reduce_order_stock();

      // Remove cart
      $woocommerce->cart->empty_cart();

      // Return thankyou redirect
      return array(
        'result' => 'success',
        'redirect' => $this->get_return_url( $order )
      );
    }
  }
}

function add_checkout_fi( $methods ) {
  $methods[] = 'WC_Gateway_Checkout_Fi'; 
  return $methods;
}

add_filter( 'woocommerce_payment_gateways', 'add_checkout_fi' );

?>