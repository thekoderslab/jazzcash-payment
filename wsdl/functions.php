<?php
require dirname ( dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) ) . '/wp-config.php';
function DoUpdatePaymentStatus($pp_Version, $pp_TxnType, $pp_BankID, $pp_Password, $pp_TxnRefNo, $pp_TxnDateTime, $pp_ResponseCode, $pp_ResponseMessage, $pp_AuthCode, $pp_RetreivalReferenceNo, $pp_SecureHash, $pp_ProductID, $pp_SettlementExpiry) {
	global $db_obj;
	// check for required parameter
	$required_params = [ 
			'pp_Version' => $pp_Version,
			'pp_TxnType' => $pp_TxnType,
			'pp_Password' => $pp_Password,
			'pp_TxnRefNo' => $pp_TxnRefNo,
			'pp_TxnDateTime' => $pp_TxnDateTime,
			'pp_ResponseCode' => $pp_ResponseCode,
			'pp_RetreivalReferenceNo' => $pp_RetreivalReferenceNo 
	];
	foreach ( $required_params as $in => $iv ) {
		if (! isset ( $iv ) or empty ( $iv )) {
			return "012 Missing mandatory parameter(s) " . $in;
			exit ();
		}
	}
	// get the payment token from response
	$payment_token = $pp_RetreivalReferenceNo;
	$password = $pp_Password;
	$application_id = $pp_TxnRefNo;
	if (in_array ( $pp_ResponseCode, [ 
			'000',
			'121',
			'200' 
	] )) {
		
		global $wpdb;
		global $woocommerce;
		$table_name = $wpdb->prefix . "jazz_cash_order_ref";
		$post_table = $wpdb->prefix . "posts";
		$order_query = $wpdb->get_row ( "SELECT * FROM `" . $table_name . "` WHERE `TxnRefNo` = '" . $pp_TxnRefNo . "'" );
		$order_id = $order_query->order_id;
		/*
		 *
		 * $order = new WC_Order($order_id);
		 * $order->update_status('wc-otcSuccess');
		 */
		$wpdb->update ( $post_table, array (
				'post_status' => 'wc-otcSuccess' 
		), array (
				'ID' => $order_id 
		), array (
				'%s' 
		), array (
				'%d' 
		) );
		$order = new WC_Order ( $order_id );
		foreach ( $order->get_items () as $item_id => $item ) {
			// Get an instance of corresponding the WC_Product object
			$product = $item->get_product ();
			$qty = $item->get_quantity (); // Get the item quantity
			wc_update_product_stock ( $product, $qty, 'decrease' );
		}
		$woocommerce->cart->empty_cart ();
		// return $order_query->id_order."000 |status updated successfully|";
		return "000 |status updated successfully|";
	} else {
		return "101 |invalid merchant details or invalid response code|";
	}
}