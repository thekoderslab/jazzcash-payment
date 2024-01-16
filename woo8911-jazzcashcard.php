<?php

/* JazzCashCard Payment Gateway Class */

class Jazz8911_JazzCashCard extends WC_Payment_Gateway
{

	function __construct()
	{

		$this->id = "jazzcashcard";

		$this->method_title = __("JazzCash Debit/Credit Card", 'jazzcashcard');

		$this->method_description = __("JazzCash Debit/Credit Card Payment Gateway Plug-in for WooCommerce", 'jazzcashcard');

		$this->title = __("JazzCash Debit/Credit Card", 'jazzcashcard');

		$dir = plugin_dir_url(__FILE__);

		$this->icon = apply_filters('woocommerce_gateway_icon', $dir . '/assets/card.png');

		$this->has_fields = TRUE;

		$this->init_form_fields();

		$this->init_settings();

		foreach ($this->settings as $setting_key => $value) {

			$this->$setting_key = $value;

		}

		add_action('admin_notices', array($this, 'do_ssl_check'));

		if (is_admin()) {

			add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

		}

		add_action('woocommerce_api_jazzcashresponse', array($this, 'jazzcash_response'));

		add_action('woocommerce_receipt_jazzcashcard', array($this, 'receipt_page'));

	} // END OF FUNCTION construct

	public function init_form_fields()
	{

		$this->form_fields = array(

			'enabled' => array(

				'title' => __('Enable/Disable', 'jazzcashcard'),

				'label' => __('Enable this payment gateway', 'jazzcashcard'),

				'type' => 'checkbox',

				'default' => 'yes'

			),

			'title' => array(

				'title' => __('Payment Gateway Title', 'jazzcashcard'),

				'type' => 'text',

				'desc_tip' => __('Payment title the customer will see during the checkout process.', 'jazzcashcard'),

				'default' => __('JazzCash Debit/Credit Card', 'jazzcashcard')

			),

			'description' => array(

				'title' => __('Payment Gateway Description', 'jazzcashcard'),

				'type' => 'textarea',

				'desc_tip' => __('Payment Gateway description', 'jazzcashcard'),

				'default' => __('Pay freely using JazzCash Debit/Credit Card.', 'jazzcashcard'),
				'css' => 'max-width:350px;'

			),

			'merchantID' => array(

				'title' => __('Merchant ID', 'jazzcashcard'),

				'type' => 'text',

				'desc_tip' => __('Provided by JazzCash', 'jazzcashcard')

			),

			'password' => array(

				'title' => __('Password', 'jazzcashcard'),

				'type' => 'text',

				'desc_tip' => __('Password.', 'jazzcashcard')

			),

			'returnURL' => array(

				'title' => __('Return URL', 'jazzcashcard'),

				'type' => 'text',

				'desc_tip' => __('Merchant Url for returning Transactions', 'jazzcashcard')

			),

			'expiryHours' => array(

				'title' => __('Transaction Expiry (Hours)', 'jazzcashcard'),

				'type' => 'number',

				'desc_tip' => __('Transaction Expiry (Hours)', 'jazzcashcard'),

				'default' => __('12', 'jazzcashcard')

			),

			'integritySalt' => array(

				'title' => __('Integrity Salt', 'jazzcashcard'),

				'type' => 'text',

				'desc_tip' => __('Provided by JazzCash', 'jazzcashcard')

			),

			'actionURL' => array(

				'title' => __('Action URL', 'jazzcashcard'),

				'type' => 'text',

				'desc_tip' => __('Provided by JazzCash', 'jazzcashcard')

			),

			'wsdlURL' => array(

				'title' => __('Web services / WSDL', 'jazzcashcard'),

				'type' => 'text',

				'desc_tip' => __('Web services URL', 'jazzcashcard')

			),

			'validateHash' => array(

				'title' => __('Validate Hash', 'jazzcashcard'),

				'label' => __('Validate Hash', 'jazzcashcard'),

				'type' => 'checkbox',

				'default' => 'no',

			)

		);

	} // END OF FUNCTION init_form_fields

	public function payment_fields()
	{

		$dir = plugin_dir_url(__FILE__);

		?>

<script type="text/javascript">
jQuery('#payment_method_jazzcashcard').click(function() {

    jQuery("#MIGS").show();

});
</script>

<div id="custom_input">

    <div id="MIGS" class="text-for-jc" style="display:none;">

        <p>Any Local/International Visa/Master/Debit/Credit Card holder can pay online.</p>

        <p>Please make sure your card is activated for online shopping. For more information about card activation

            <a href="https://www.jazzcash.com.pk/digital-payments/" target="_blank">click here</a>

        </p>

        <p>You're almost done! </p>

        <p>To change or edit your order, go back. No changes will be allowed once you click "PLACE ORDER".</p>

    </div>

</div>

<?php

	} // END OF FUNCTION payment_fields

	function receipt_page($order)
	{

		echo '<p>' . __('Please wait while you are being redirected to JazzCash...', 'jazzcashcard') . '</p>';

		$dir = plugin_dir_url(__FILE__);

		echo '<p><img src="' . $dir . '/assets/jazz-cash.png" /></p>';

		echo $this->generate_jazzcashcard_form($order);

	} // END OF FUNCTION receipt_page

	public function generate_jazzcashcard_form($order_id)
	{

		global $woocommerce;

		$customer_order = new WC_Order($order_id);

		$_ActionURL = esc_html($this->actionURL);

		$_MerchantID = esc_html($this->merchantID);

		$_Password = esc_html($this->password);

		$_ReturnURL = esc_html($this->returnURL);

		$_IntegritySalt = esc_html($this->integritySalt);

		$_ExpiryHours = esc_html($this->expiryHours);

		$items = $customer_order->get_items();

		$product_name = array();

		foreach ($items as $item) {

			array_push($product_name, $item['name']);

		}

		$_Description = implode(", ", $product_name);

		$_TxnType = esc_html('MPAY'); // Leave it empty

		$_Language = esc_html('EN');

		$_Version = esc_html('2.0');

		$_Currency = esc_html('PKR');

		$_IsRegisteredCustomer = esc_html('NO');

		$_BillReference = $customer_order->get_order_number();

		$_AmountTmp = $customer_order->order_total * 100;

		$_AmtSplitArray = explode('.', $_AmountTmp);

		$_FormattedAmount = $_AmtSplitArray[0];

		date_default_timezone_set("Asia/karachi");

		$timestamp = microtime(true);

		$_milliseconds = sprintf("%05d", ($timestamp - floor($timestamp)) * 1000);

		$_TxnDateTime = date("YmdHis");

		$_ExpiryDateTime = date("YmdHis", strtotime('+' . $_ExpiryHours . ' hours', strtotime($_TxnDateTime)));

		$_TxnRefNumber = esc_html('T' . $_TxnDateTime . $_milliseconds);

		$ppmpf1 = esc_html('');

		$ppmpf2 = esc_html('');

		$ppmpf3 = esc_html('');

		$ppmpf4 = esc_html('');

		$ppmpf5 = esc_html('');

		$BankID = esc_html(''); // Leave it empty

		$SubMerchantID = esc_html(''); // Leave it empty

		$ProductID = esc_html(''); // Leave it empty

		$_Description = $_TxnRefNumber;

		$HashArray = [$_FormattedAmount, $BankID, $_BillReference, $_Description, $_IsRegisteredCustomer, $_Language, $_MerchantID, $_Password, $ProductID, $_ReturnURL, $_Currency, $_TxnDateTime, $_ExpiryDateTime, $_TxnRefNumber, $_TxnType, $_Version, $ppmpf1, $ppmpf2, $ppmpf3, $ppmpf4, $ppmpf5];

		$SortedArray = $_IntegritySalt;
		for ($i = 0; $i < count($HashArray); $i++) {
			if ($HashArray[$i] != 'undefined' and $HashArray[$i] != null and $HashArray[$i] != "") {

				$SortedArray .= "&" . $HashArray[$i];
			}
		}

		$_Securehash = hash_hmac('sha256', $SortedArray, $_IntegritySalt);

		$jazzcashcard_args = array(

			'pp_Version' => $_Version,

			'pp_TxnType' => $_TxnType,

			'pp_Language' => $_Language,

			'pp_MerchantID' => $_MerchantID,

			'pp_SubMerchantID' => $SubMerchantID,

			'pp_Password' => $_Password,

			'pp_TxnRefNo' => $_TxnRefNumber,

			'pp_Amount' => $_FormattedAmount,

			'pp_TxnCurrency' => $_Currency,

			'pp_TxnDateTime' => $_TxnDateTime,

			'pp_BillReference' => $_BillReference,

			'pp_Description' => $_Description,

			'pp_IsRegisteredCustomer' => $_IsRegisteredCustomer,

			'pp_BankID' => $BankID,

			'pp_ProductID' => $ProductID,

			'pp_TxnExpiryDateTime' => $_ExpiryDateTime,

			'pp_ReturnURL' => $_ReturnURL,

			'pp_SecureHash' => $_Securehash,

			'ppmpf_1' => $ppmpf1,

			'ppmpf_2' => $ppmpf2,

			'ppmpf_3' => $ppmpf3,

			'ppmpf_4' => $ppmpf4,

			'ppmpf_5' => $ppmpf5

		);
		global $wpdb;

		$table_name = $wpdb->prefix . "jazz_cash_order_ref";

		$wpdb->insert($table_name, array('order_id' => $order_id, 'TxnType' => 'MIGS', 'TxnRefNo' => $_TxnRefNumber));

		$jazzcashcard_string = '';

		foreach ($jazzcashcard_args as $key => $value) {

			$jazzcashcard_string .= $key . '&' . $value . ':';

		}

		$jazzcashcard_args_array = array();

		foreach ($jazzcashcard_args as $key => $value) {

			$jazzcashcard_args_array[] = "<input type='hidden' name='" . $key . "' value='" . $value . "' />";

		}

		$form = '<form action = "' . $_ActionURL . '" id = "jazzcashcardPostForm" name = "JazzCashCardForm" method = "post">';

		$form .= implode('', $jazzcashcard_args_array);

		$logMessage = "\n***************************************************"
			. "\nOrder No# :: " . $_TxnRefNumber
			. "\nOrder Time :: " . date('Y-m-d h:i:s');
		jazzCashC_logs($logMessage);

		$form .= '</form>

						<script type="text/javascript">

							setTimeout(function() {

								document.getElementById("jazzcashcardPostForm").submit();

							}, 5*1000);

						</script>';

		return $form;

	} // END OF FUNCTION generate_jazzcashcard_form

	function process_payment($order_id)
	{

		global $woocommerce;

		$order = new WC_Order($order_id);

		return array('result' => 'success', 'redirect' => $order->get_checkout_payment_url(TRUE));

	} // END OF FUNCTION process_payment

	public function validate_fields()
	{

		return TRUE;

	} // END OF FUNCTION validate_fields

	public function do_ssl_check()
	{

		if ($this->enabled == "yes") {

			if (get_option('woocommerce_force_ssl_checkout') == "no") {

				echo "<div class=\"error\"><p>" .

					sprintf(
						__("<strong>%s</strong> is enabled and WooCommerce is not forcing the SSL certificate on your checkout page." .

							"Please ensure that you have a valid SSL certificate and that you are <a href=\"%s\">forcing the checkout pages to be secured.</a>"),

						$this->method_title,
						admin_url('admin.php?page=wc-settings&tab=checkout')
					) . "</p></div>";

			}

		}

	} // END OF FUNCTION do_ssl_check

	public function callback_handler()
	{

		global $woocommerce;

		try {
		} catch (Exception $e) {
		}

	} // END OF FUNCTION callback_handler

	public function jazzcash_response()
	{

		// RESPONSE WILL LAND HERE AFTER REDIRECTING FROM ACTION URL

		global $woocommerce;

		try {

			$comment = "";

			$sortedResponseArray = array();

			if (!empty($_POST)) {

				foreach ($_POST as $key => $val) {

					$comment .= $key . "[" . $val . "],<br/>";

					$sortedResponseArray[$key] = $val;

				}

			}

			$_MerchantID = esc_html($this->merchantID);

			$_Password = esc_html($this->password);

			$_IntegritySalt = esc_html($this->integritySalt);

			$_ValidateHash = esc_html($this->validateHash);

			$_ResponseMessage = esc_html($this->getEmptyIfNullFromPOST('pp_ResponseMessage'));

			$pp_TxnType = esc_html($this->getEmptyIfNullFromPOST('pp_TxnType'));

			$_ResponseCode = esc_html($this->getEmptyIfNullFromPOST('pp_ResponseCode'));

			$_TxnRefNo = esc_html($this->getEmptyIfNullFromPOST('pp_TxnRefNo'));

			$_BillReference = esc_html($this->getEmptyIfNullFromPOST('pp_BillReference'));

			$_SecureHash = esc_html($this->getEmptyIfNullFromPOST('pp_SecureHash'));

			$line_no = 0;

			$array = array();

			if (strtolower($_ValidateHash) == 'yes') {

				if (!$this->isNullOrEmptyString($_SecureHash)) {

					unset($sortedResponseArray['pp_SecureHash']);

					ksort($sortedResponseArray);

					$sortedResponseValuesArray = array();

					array_push($sortedResponseValuesArray, $_IntegritySalt);

					foreach ($sortedResponseArray as $key => $val) {

						if (!$this->isNullOrEmptyString($val)) {

							array_push($sortedResponseValuesArray, $val);

						}

					}
					$sortedResponseValuesForHash = implode('&', $sortedResponseValuesArray);

					$CalSecureHash = hash_hmac('sha256', $sortedResponseValuesForHash, $_IntegritySalt);

					if (strtolower($CalSecureHash) == strtolower($_SecureHash)) {

						$isResponseOk = true;

					} else {

						$isResponseOk = false;

						$comment .= "Secure Hash mismatched.";

					}

				} else {

					$isResponseOk = false;

					$comment .= "Secure Hash is empty.";

				}

			} else {

				$isResponseOk = true;

			}


			$logMessage = "\nResponse " . $isResponseOk . "\nResponse Code " . $_ResponseCode . "\n pp_TxnType " . $pp_TxnType . "\n\n  Envelop " . $comment;

			jazzCashC_logs($logMessage);

			$order = new WC_Order($_BillReference);

			if ($isResponseOk) {

				global $wpdb;

				$post_table = $wpdb->prefix . "posts";


				if ($_ResponseCode == '000') {

					if ($pp_TxnType == 'MWALLET') {

						$wpdb->update($post_table, array('post_status' => 'wc-mwSuccess'), array('ID' => $order->get_id()));

						foreach ($order->get_items() as $item_id => $item) {

							$product = $item->get_product();

							$qty = $item->get_quantity();

							wc_update_product_stock($product, $qty, 'decrease');

						}

						$woocommerce->cart->empty_cart();

					} else if ($pp_TxnType == 'MIGS' || $pp_TxnType == 'MPAY') {

						$wpdb->update($post_table, array('post_status' => 'wc-cardSuccess'), array('ID' => $order->get_id()));

						foreach ($order->get_items() as $item_id => $item) {

							$product = $item->get_product();

							$qty = $item->get_quantity();

							wc_update_product_stock($product, $qty, 'decrease');

						}

						$woocommerce->cart->empty_cart();

					}

				} else if ($_ResponseCode == '110') {

					$wpdb->update($post_table, array('post_status' => 'wc-mwFailure'), array('ID' => $order->get_id()));

				} else if ($_ResponseCode == '124') {

					$wpdb->update($post_table, array('post_status' => 'wc-migsPending'), array('ID' => $order->get_id()));

					foreach ($order->get_items() as $item_id => $item) {

						$product = $item->get_product();

						$qty = $item->get_quantity();

						wc_update_product_stock($product, $qty, 'decrease');

					}

					$woocommerce->cart->empty_cart();

				} else if ($_ResponseCode == '129') {

					$wpdb->update($post_table, array('post_status' => 'wc-migsDropped'), array('ID' => $order->get_id()));

				} else if ($_ResponseCode == '134') {

					$wpdb->update($post_table, array('post_status' => 'wc-timeOut'), array('ID' => $order->get_id()));

				} else if ($_ResponseCode == '156') {

					$wpdb->update($post_table, array('post_status' => 'wc-mwFailure'), array('ID' => $order->get_id()));

				} else if ($_ResponseCode == '157') {

					if ($pp_TxnType == 'MWALLET') {

						$wpdb->update($post_table, array('post_status' => 'wc-mwPending'), array('ID' => $order->get_id()));

					} else if ($pp_TxnType == 'MIGS' || $pp_TxnType == 'MPAY') {

						$wpdb->update($post_table, array('post_status' => 'wc-migsPending'), array('ID' => $order->get_id()));

					}

					foreach ($order->get_items() as $item_id => $item) {

						$product = $item->get_product();

						$qty = $item->get_quantity();

						wc_update_product_stock($product, $qty, 'decrease');

					}

					$woocommerce->cart->empty_cart();
				} else if ($_ResponseCode == '999') {

					$wpdb->update($post_table, array('post_status' => 'wc-mwFailure'), array('ID' => $order->get_id()));

				} else {

					if ($_ResponseCode == '349') {

						$wpdb->update($post_table, array('post_status' => 'wc-timeOut'), array('ID' => $order->get_id()));

					} else if ($pp_TxnType == 'MWALLET') {

						$wpdb->update($post_table, array('post_status' => 'wc-mwFailure'), array('ID' => $order->get_id()));

					} else if ($pp_TxnType == 'MIGS' || $pp_TxnType == 'MPAY') {

						$wpdb->update($post_table, array('post_status' => 'wc-migsFailure'), array('ID' => $order->get_id()));

					}

				}

				wp_redirect($this->get_return_url($order));

				exit();

			} else {

				wp_redirect($this->get_return_url($order));

				exit();

			}

		} catch (Exception $e) {

			$order = new WC_Order($_BillReference);

			wp_redirect($this->get_return_url($order));

			exit();

		}

	} // END OF FUNCTION jazzcash_response

	protected function complete_order($order, $posted_data)
	{

		$approval_code = get_value('approval_code', $posted_data);

		$order->payment_complete();

		$this->woocommerce()->cart->empty_cart();

	} // END OF FUNCTION complete_order

	function showMessage($content)
	{

		return '<div class="box ' . $this->msg['class'] . '-box" >' . $this->msg['message'] . '</div>' . $content;

	} // END OF FUNCTION showMessage

	protected function isNullOrEmptyString($question)
	{

		return (!isset($question) || trim($question) === '');

	} // END OF FUNCTION isNullOrEmptyString

	protected function getEmptyIfNullFromPOST($key)
	{

		if (!isset($_POST[$key]) || trim($_POST[$key]) == "") {

			return "";

		} else {

			return $_POST[$key];

		}

	} // END OF FUNCTION getEmptyIfNullFromPOST

	protected function getEmptyIfNull($key)
	{

		if (!isset($key) || trim($key) == "") {

			return "";

		} else {

			return $key;

		}

	} // END OF FUNCTION getEmptyIfNull

} // END OF CLASS JazzCashCard

add_action('wp_enqueue_scripts', 'jc_card_adding_scripts');

function jc_card_adding_scripts()
{

	wp_register_script('buttons', plugins_url('js/buttons.js', __FILE__));

	wp_enqueue_script('buttons');

} // END OF FUNCTION jc_card_adding_scripts

add_action('wp_enqueue_scripts', 'jc_card_adding_styles');

function jc_card_adding_styles()
{

	wp_register_style('jc_stylesheet', plugins_url('css/jc-buttons.css', __FILE__));

	wp_enqueue_style('jc_stylesheet');

} // END OF FUNCTION jc_card_adding_styles

add_action('woocommerce_checkout_update_order_meta', 'jc_card_payment_update_order_meta');

function jc_card_payment_update_order_meta($order_id)
{

	if ($_POST['payment_method'] != 'jazzcashcard')

		return;

} // END OF FUNCTION jc_card_payment_update_order_meta

add_action('woocommerce_admin_order_data_after_billing_address', 'jc_card_checkout_field_display_admin_order_meta', 10, 1);

function jc_card_checkout_field_display_admin_order_meta($order)
{

	$method = get_post_meta($order->get_id(), '_payment_method', TRUE);

	if ($method != 'jazzcashcard')

		return;

	$TxnType_show = 'JazzCash - Card';

	echo '<p><strong>' . __('Transaction Type') . ':</strong> ' . $TxnType_show . '</p>';

} // END OF FUNCTION jc_card_checkout_field_display_admin_order_meta

global $wpdb;

$table_name = $wpdb->prefix . "jazz_cash_order_ref";

$my_products_db_version = '1.0.0';

$charset_collate = $wpdb->get_charset_collate();

if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {

	$sql = "CREATE TABLE `" . $table_name . "`(`id` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY, `order_id` INT(10) NOT NULL,

													`TxnType` VARCHAR(50) NOT NULL, `TxnRefNo` VARCHAR(50) NOT NULL) $charset_collate";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	dbDelta($sql);

	add_option('my_db_version', $my_products_db_version);

}

add_action('init', 'register_jc_card_new_order_statuses');

function register_jc_card_new_order_statuses()
{

	register_post_status(
		'wc-cardSuccess',
		array(

			'label' => _x('Card Payment Success/ Ready for Shipment', 'Order status', 'woocommerce'),

			'public' => TRUE,

			'exclude_from_search' => false,

			'show_in_admin_all_list' => TRUE,

			'show_in_admin_status_list' => TRUE,

			'label_count' => _n_noop('Card Payment Success/ Ready for Shipment <span class="count">(%s)</span>', 'Card Payment Success/ Ready for Shipment<span class="count">(%s)</span>', 'woocommerce')

		)

	);

	register_post_status(
		'wc-timeOut',
		array(

			'label' => _x('Transaction Time Out', 'Order status', 'woocommerce'),

			'public' => TRUE,

			'exclude_from_search' => false,

			'show_in_admin_all_list' => TRUE,

			'show_in_admin_status_list' => TRUE,

			'label_count' => _n_noop('Transaction Time Out <span class="count">(%s)</span>', 'Transaction Time Out<span class="count">(%s)</span>', 'woocommerce')

		)

	);

	register_post_status(
		'wc-migsPending',
		array(

			'label' => _x('The transaction is pending. Please verify the order through the Jazzcash portal.', 'Order status', 'woocommerce'),

			'public' => TRUE,

			'exclude_from_search' => false,

			'show_in_admin_all_list' => TRUE,

			'show_in_admin_status_list' => TRUE,

			'label_count' => _n_noop('The transaction is pending. Please verify the order through the Jazzcash portal.<span class="count">(%s)</span>', 'The transaction is pending. Please verify the order through the Jazzcash portal.<span class="count">(%s)</span>', 'woocommerce')

		)

	);

	register_post_status(
		'wc-migsFailure',
		array(

			'label' => _x('Card Failure', 'Order status', 'woocommerce'),

			'public' => TRUE,

			'exclude_from_search' => false,

			'show_in_admin_all_list' => TRUE,

			'show_in_admin_status_list' => TRUE,

			'label_count' => _n_noop('Card Failure<span class="count">(%s)</span>', 'Card Failure<span class="count">(%s)</span>', 'woocommerce')

		)

	);

	register_post_status(
		'wc-mwFailure',
		array(

			'label' => _x('MWALLET Failure', 'Order status', 'woocommerce'),

			'public' => true,

			'exclude_from_search' => false,

			'show_in_admin_all_list' => true,

			'show_in_admin_status_list' => true,

			'label_count' => _n_noop('MWALLET Failure<span class="count">(%s)</span>', 'MWALLET Failure<span class="count">(%s)</span>', 'woocommerce')

		)
	);
	register_post_status(
		'wc-migsDropped',
		array(

			'label' => _x('Transaction is Dropped', 'Order status', 'woocommerce'),

			'public' => TRUE,

			'exclude_from_search' => false,

			'show_in_admin_all_list' => TRUE,

			'show_in_admin_status_list' => TRUE,

			'label_count' => _n_noop('Transaction is Dropped<span class="count">(%s)</span>', 'Transaction is Dropped<span class="count">(%s)</span>', 'woocommerce')

		)

	);


} // END OF FUNCTION register_jc_card_new_order_statuses

add_filter('wc_order_statuses', 'jc_card_new_wc_order_statuses');

function jc_card_new_wc_order_statuses($order_statuses)
{

	$order_statuses['wc-cardSuccess'] = _x('Card Payment Success/ Ready for Shipment', 'Order status', 'woocommerce');

	$order_statuses['wc-timeOut'] = _x('Transaction Time Out', 'Order status', 'woocommerce');

	$order_statuses['wc-migsPending'] = _x('The transaction is pending. Please verify the order through the Jazzcash portal.', 'Order status', 'woocommerce');

	$order_statuses['wc-migsFailure'] = _x('Card Failure', 'Order status', 'woocommerce');

	$order_statuses['wc-mwFailure'] = _x('MWALLET Failure', 'Order status', 'woocommerce');

	$order_statuses['wc-migsDropped'] = _x('Transaction is Dropped', 'Order status', 'woocommerce');

	return $order_statuses;

} // END OF FUNCTION jc_card_new_wc_order_statuses

add_filter('the_title', 'woo_title_order_received_card', 10, 2);

function woo_title_order_received_card($title, $id)
{

	if (function_exists('is_order_received_page') && is_order_received_page() && get_the_ID() === $id) {

		$title = "";

	}

	return $title;

} // END OF FUNCTION woo_title_order_received_card

add_filter('woocommerce_thankyou_order_received_text', 'wpb_card_thankyou', 10, 2);

function wpb_card_thankyou($thankyoutext, $order)
{
	$order_status = $order->get_status();

	$successCase = 'Thanks, Your Order has been Completed.';

	$failureCase = 'Sorry! Your Order has been failed.';

	$pendingCase = "Transaction is pending. Please Login JazzCash App > Select My Account > Payment Request > Select Transaction and Enter MPIN.";

	if ($order_status == 'timeOut') {

		$order_message = '<span style="color:red;" >' . esc_html($failureCase) . '</span>';

	}
	if ($order_status == 'cardSuccess') {

		$order_message = '<span style="color:green;" >' . esc_html($successCase) . '</span>';

	}
	if ($order_status == 'migsPending') {

		$order_message = '<span style="color:green;" >' . esc_html($pendingCase) . '</span>';

	}
	if ($order_status == 'migsFailure') {


		$order_message = '<span style="color:red;" >' . esc_html($failureCase) . '</span>';

	}
	if ($order_status == 'mwSuccess') {

		$order_message = '<span style="color:green;" >' . esc_html($successCase) . '</span>';

	}
	if ($order_status == 'mwFailure') {



		$order_message = '<span style="color:red;" >' . esc_html($failureCase) . '</span>';

	}

	$added_text = $order_message;

	$logMessage = "\nReturn Status :: " . $order_status
		. "\nOrder Message :: " . $order_message;

	jazzCashC_logs($logMessage);

	unset($_COOKIE['jazzCashRequestData']);

	return $added_text;

} // END OF FUNCTION wpb_card_thankyou


function jazzCashC_logs($message)
{
	global $wpdb;

	$pluginPath = plugin_dir_path(__FILE__);

	$filePath = $pluginPath . $wpdb->prefix . 'JazzCash_logs.txt';

	if (!file_exists($filePath)) {
		touch($filePath); // Create the file if it doesn't exist
	}
	$file = fopen($filePath, "a");
	if ($file) {
		fwrite($file, $message);
		fclose($file);
		return true;
	}
	return false;
} //End of function jazzCash_logs

?>