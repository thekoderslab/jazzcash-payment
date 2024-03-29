<?php
	//header('location:UpdateOrderPaymentStatus60.php'); exit();
	//call library
	//echo "<br /><br />BASE URL: ". $_SERVER['DOCUMENT_ROOT']. "<br /><br />"; exit();
	//echo "<br /><br />BASE URL: ". dirname( ( ( ( ( __FILE__ ) ) ) ) ). "<br /><br />"; exit();
	require_once dirname( __FILE__ )."/includes/nusoap-0.9.5/lib/nusoap.php";
	require_once dirname( __FILE__ )."/functions.php";
	$server = new nusoap_server();
	$server->configureWSDL("UpdateOrderPaymentStatus6", "urn:UpdateOrderPaymentStatus6");
	$server->register(
		'DoUpdatePaymentStatus',
		[
			'pp_Version'					=> 'xsd:string',
			'pp_TxnType'					=> 'xsd:string',
			'pp_BankID'						=> 'xsd:string',
			'pp_Password'					=> 'xsd:string',
			'pp_TxnRefNo'					=> 'xsd:string',
			'pp_TxnDateTime'				=> 'xsd:string',
			'pp_ResponseCode'				=> 'xsd:string',
			'pp_ResponseMessage'			=> 'xsd:string',
			'pp_AuthCode'					=> 'xsd:string',
			'pp_RetreivalReferenceNo'		=> 'xsd:string',
			'pp_SecureHash'					=> 'xsd:string',
			'pp_ProductID'					=> 'xsd:string',
			'pp_SettlementExpiry'			=> 'xsd:string'
		],
		['DoUpdatePaymentStatusResult'		=> 'xsd:string']
	);
	$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA)? $HTTP_RAW_POST_DATA : '';
	$server->service(file_get_contents("php://input"));
	// exit();
?>