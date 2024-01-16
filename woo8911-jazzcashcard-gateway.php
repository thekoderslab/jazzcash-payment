<?php

	/*

	Plugin Name: JazzCash Debit/Credit Card

	Plugin URI: https://www.jazzcash.com.pk/digital-payments/

	Description: Enjoy online shopping with JazzCash Debit/Credit Card. WP_V: 6.1.1 and WC_V: 7.3.0.

	Version: 1.0.2

	Author: AKSA-SDS Development Team

	Author URI: https://aksa-sds.com/

	*/

	// INCLUDE GATEWAY CLASS AND REGISTER PAYMENT GATEWAY WITH WOOCOMMERCE

	add_action('plugins_loaded', 'jazzcashcard_init', 0);

	function jazzcashcard_init() {

		// IF THE PARENT WC_PAYMENT_GATEWAY CLASS DOESN'T EXIST. IT MEANS WOOCOMMERCE IS NOT INSTALLED ON THE SITE, SO DO NOTHING

		if(!class_exists('WC_Payment_Gateway')) {

			//return false;

		}

		include_once('woo8911-jazzcashcard.php');

		add_filter('woocommerce_payment_gateways', 'add_jazzcashcard_gateway');

		function add_jazzcashcard_gateway($methods) {

			$methods[] = 'Jazz8911_JazzCashCard';

			return $methods;

		}

	} // END OF FUNCTION jazzcashcard_init

	add_filter('plugin_action_links_' . plugin_basename( __FILE__ ), 'jazzcashcard_action_links');

	function jazzcashcard_action_links($links) {

		$plugin_links = array('<a href="'.admin_url('admin.php?page=wc-settings&tab=checkout').'">'. __('Settings', 'jazzcashcard').'</a>');

		// MERGE NEW LINK WITH THE DEFAULT ONES

		return array_merge($plugin_links, $links);

	} // END OF FUNCTION jazzcashcard_action_links

?>