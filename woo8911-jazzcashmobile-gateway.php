<?php

/*

   Plugin Name: JazzCash Mobile Account

   Plugin URI: https://www.jazzcash.com.pk/mobile-account/

   Description: Enjoy online shopping with JazzCash Mobile Account. WP_V: 6.1.1 and WC_V: 7.3.0.

   Version: 1.0.2

   Author: AKSA-SDS Development Team

   Author URI: https://aksa-sds.com/

   */

// INCLUDE GATEWAY CLASS AND REGISTER PAYMENT GATEWAY WITH WOOCOMMERCE

add_action('plugins_loaded', 'jazzcashmobile_init', 0);

function jazzcashmobile_init()
{

	// IF THE PARENT WC_PAYMENT_GATEWAY CLASS DOESN'T EXIST. IT MEANS WOOCOMMERCE IS NOT INSTALLED ON THE SITE, SO DO NOTHING

	if (!class_exists('WC_Payment_Gateway')) {

		//return false;

	}

	include_once('woo8911-jazzcashmobile.php');

	add_filter('woocommerce_payment_gateways', 'add_jazzcashmobile_gateway');

	function add_jazzcashmobile_gateway($methods)
	{

		$methods[] = 'Jazz8911_JazzcashMobile';

		return $methods;

	}

} // END OF FUNCTION jazzcashmobile_init

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'jazzcashmobile_action_links');

function jazzcashmobile_action_links($links)
{

	$plugin_links = array('<a href="' . admin_url('admin.php?page=wc-settings&tab=checkout') . '">' . __('Settings', 'jazzcashmobile') . '</a>');

	// MERGE NEW LINK WITH THE DEFAULT ONES

	return array_merge($plugin_links, $links);

} // END OF FUNCTION jazzcashmobile_action_links

?>