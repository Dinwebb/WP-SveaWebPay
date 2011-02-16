<?php
/*
Plugin Name: SveaWebPay
Plugin URI: http://www.dinwebb.nu
Description: SveaWebPay payment gateway for wp e-commerce
Author: Spathon @Dinwebb
Version: 0.5
Author URI: http://www.dinwebb.nu/

Contact
patrik.spathon@gmail.com
Twitter
spathon
*/


$nzshpcrt_gateways[$num]['name'] = 'SveaWebPay';
$nzshpcrt_gateways[$num]['internalname'] = 'svea_web_pay';
$nzshpcrt_gateways[$num]['function'] = 'gateway_sveawebpay';
$nzshpcrt_gateways[$num]['form'] = "form_sveawebpay";
$nzshpcrt_gateways[$num]['submit_function'] = "submit_sveawebpay";


/*
 * @TODO
 * 
 * - Currency and language option? -> $wpsc_cart?
 * - - Need a little work with languages and currency (currently only SEK)
 */


# http://getshopped.org/resources/docs/get-involved/writing-a-new-payment-gateway/



/*
 *
 *   Load Language files
 *
 */
$plugin_path = dirname(plugin_basename(__FILE__)) . '/lang';
load_plugin_textdomain('wp_sveawebpay', false, $plugin_path );

/*
This function receives no parameters and returns a string with form elements ($output) within <tr> <td> </tr> <td>

tags, this is to make sure the fields appear within the admin section inside the table.

Note: There is no need for a <form> tag, as that is automatically done within the Wp-E-Commerce Plugin.
No doubt the gateway you chose to build will require at least a username and password, so as an example a basic submit function would look like this: 
*/
function form_sveawebpay(){

	
	/*
	 *   get the url if isset else use the default
	 */
	if(get_option('sveawebpay_url')){ 
		$url = get_option('sveawebpay_url');
	}else{
		$url = 'https://partnerweb.sveaekonomi.se/webpayhosted2/initiatepayment.aspx';
	}

	// get the payment method
	$payment = get_option('sveawebpay_paymentmethod');
	
	// Error message on failed transaction
	if(get_option('sveawebpay_error')){ 
		$error = get_option('sveawebpay_error');
	}else{
		$error = __('Sorry your transaction did not go through successfully, please try again.', 'wp_sveawebpay');
	}
	
	// Test mode 
	$testmode = get_option('sveawebpay_testmode');
	
	
	// start the HTML
	$output ='<tr><td style="padding: 0 10px 0 0">';

		// username
		$output.='
			<p>
				<label for="sveawebpay_username">'. __('Username', 'wp_sveawebpay') .'</label>
				<input name="sveawebpay_username" id="sveawebpay_username" type="text" value="'. get_option('sveawebpay_username') .'" />
			</p>
			';
	
	//cell
	$output .= '</td><td style="padding: 0 0 0 10px;">';
	
		// password
		$output.='
			<p>
				<label for="sveawebpay_password">'. __('Password', 'wp_sveawebpay') .'</label>
				<input name="sveawebpay_password" id="sveawebpay_password" type="text" value="'. get_option('sveawebpay_password') .'" />
			</p>
			';
	
	//row
	$output .= '</tr><tr><td style="padding: 0 10px 0 0;">';
			
		// sveawebpay url
		$output.='
			<p>
				<label for="sveawebpay_url">'. __('SveaWebPay url', 'wp_sveawebpay') .'</label>
				<input name="sveawebpay_url" id="sveawebpay_url" type="text" value="'. $url .'" />
			</p>
			';
			
	//cell
	$output .= '</td><td style="padding: 0 0 0 10px;">';
		
		// payment method
		$output.='
			<p>
				<label for="sveawebpay_paymentmethod">'. __('Payment method', 'wp_sveawebpay') .'</label><br />
				<select name="sveawebpay_paymentmethod" id="sveawebpay_paymentmethod">
					<option '. selected('0', $payment, false) .' value="0">'. __('All', 'wp_sveawebpay') .'</option>
					<option '. selected('internetbank', $payment, false) .' value="internetbank">'. __('Internetbank', 'wp_sveawebpay') .'</option>
					<option '. selected('card', $payment, false) .' value="card">'. __('Card', 'wp_sveawebpay') .'</option>
					<option '. selected('invoice', $payment, false) .' value="invoice">'. __('Invoice', 'wp_sveawebpay') .'</option>
					<option '. selected('partpayment', $payment, false) .' value="partpayment">'. __('Partpayment', 'wp_sveawebpay') .'</option>
				</select>
			</p>
			';
	
	
	
	//row
	$output .= '</tr><tr><td style="padding: 0 10px 0 0;">';
			
		// sveawebpay url
		$output.='
			<p>
				<label for="sveawebpay_shipping_tax">'. __('Shipping tax: (0-100)', 'wp_sveawebpay') .'</label>
				<input name="sveawebpay_shipping_tax" id="sveawebpay_shipping_tax" type="text" value="'. get_option('sveawebpay_shipping_tax') .'" />
			</p>
			';
			
	//cell
	$output .= '</td><td style="padding: 0 0 0 10px;">';
		
		// payment method
		$output.= '
			<p>
				<label for="sveawebpay_shipping_name">'. __('Shipping name:', 'wp_sveawebpay') .'</label>
				<input name="sveawebpay_shipping_name" id="sveawebpay_shipping_name" type="text" value="'. get_option('sveawebpay_shipping_name') .'" />
			</p>
			';
	
	
	//row
	$output .= '</tr><tr><td colspan="2">';
		
		
		
		// error message
		$output.='
			<p>
				<label for="sveawebpay_error">'. __('Error message if transaction fails', 'wp_sveawebpay') .'</label>
				<textarea name="sveawebpay_error" id="sveawebpay_error">'. $error .'</textarea>
			</p>
			';
		
		
		// test mode
		$output.='
			<div>
				<label for="sveawebpay_testmode">'. __('Test mode', 'wp_sveawebpay') .'</label>
				<select name="sveawebpay_testmode" id="sveawebpay_testmode">
					<option '. selected('false', $testmode, false) .' value="false">'. __('Off', 'wp_sveawebpay') .'</option>
					<option '. selected('true', $testmode, false) .' value="true">'. __('On', 'wp_sveawebpay') .'</option>
				</select>
				
			</div>
			';
		
		

	$output .='</td></tr>';

	return $output;

}





/*
submit_my_new_gateway()

Now that you have a form, you must validate and save the content of the form into your WordPress setup. 
All gateways save their configuration details in the WordPress options functions.
*/

function submit_sveawebpay(){

	// Save the username
	if($_POST['sveawebpay_username'] != null) update_option('sveawebpay_username', $_POST['sveawebpay_username']);

	// Save the password
	if($_POST['sveawebpay_password'] != null) update_option('sveawebpay_password', $_POST['sveawebpay_password']);

	// Save the sveawebpay url
	if($_POST['sveawebpay_url'] != null) update_option('sveawebpay_url', $_POST['sveawebpay_url']);
	
	// Save the payment method
	if($_POST['sveawebpay_paymentmethod'] != null) update_option('sveawebpay_paymentmethod', $_POST['sveawebpay_paymentmethod']);
	
	// Save the shipping tax
	if($_POST['sveawebpay_shipping_tax'] != null) update_option('sveawebpay_shipping_tax', $_POST['sveawebpay_shipping_tax']);
	
	// Save the shipping Name
	if($_POST['sveawebpay_shipping_name'] != null) update_option('sveawebpay_shipping_name', $_POST['sveawebpay_shipping_name']);
	
	// Save the Error message
	if($_POST['sveawebpay_error'] != null) update_option('sveawebpay_error', $_POST['sveawebpay_error']);
	
	// Save the Testmode
	if($_POST['sveawebpay_testmode'] != null) update_option('sveawebpay_testmode', $_POST['sveawebpay_testmode']);

	return true;

}






/*

The gateway



*/

function gateway_sveawebpay($seperator, $sessionid){


	//$wpdb is the database handle,
	//$wpsc_cart is the shopping cart object
	global $wpdb, $wpsc_cart;
	
	
	#echo '<pre>'; print_r($wpsc_cart); echo '</pre>'; die();
	
	//This grabs the purchase log id from the database
	//that refers to the $sessionid
	$purchase_log = $wpdb->get_row(
	"SELECT * FROM `".WPSC_TABLE_PURCHASE_LOGS.
	"` WHERE `sessionid`= ".$sessionid." LIMIT 1"
	,ARRAY_A) ;
	
	
	$orderId = $purchase_log['id'];
	

	
	// SveaWebPay settings
	$data = array(
		//'Clientid' => 75021,
		'Username' => get_option('sveawebpay_username'),
		//'Row1AmountExVAT' => 100, // Order row, amount excluding VAT. *1
		//'Row1VATPercentage' => 25, // Order row, VAT percentage of the amount. *1
		//'Row1Description' => 'En+bok', // string(40) YES Order row, description of the merchandise. *1
		//'Row1Quantity' => 2, // number(10) YES Order row, the number of units being purchased. *1
		'OrderId' => $orderId, //$purchase_log['id'], // string(20) YES A string uniquely identifying an order within merchant’s web shop.
		'ResponseURL'   =>  get_bloginfo('url'),
		'CancelURL' => get_bloginfo('url'),
		'Testmode'      =>  get_option('sveawebpay_testmode'),
		'Language' => 'SV', // string(2) YES As per ISO 639-1 alpha-2.
		'Country' => 'SE', //string(2) YES As per ISO 3166-1 alpha-2.
		'Currency' => 'SEK' // string(3) YES As per ISO 4217.
	
	);
	
	
	
	
	
	// should check for the right currency and if it works with sveawebpay
	
	/*
		Supported Languages
		For more information see e.g.: http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
		Language Code		Language name
		
		SV 					Swedish
		NB 					Norwegian Bokmål
		FI 					Finnish
		DA 					Danish
		EN 					English
		
		
		Supported Countries
		For more information see e.g.: http://en.wikipedia.org/wiki/ISO_3166-1_alpha-2
		Country Code 		Country name
		
		SE 					Sweden
		NO					Norway
		FI 					Finland
		DK 					Denmark
		GB 					United Kingdom
		
		
		Supported Currencies
		For more information see e.g.: http://en.wikipedia.org/wiki/ISO_4217
		Currency Code 		Currency name
		
		SEK 				Swedish krona/kronor
		NOK 				Norwegian krone
		EUR 				Euro (European Union countries)
		DKK 				Danish krona
	*/
	
	
	
	
	// the selected payment method
	$paymentMethod = get_option('sveawebpay_paymentmethod');
	// add payment method if any is selected
	// if none is selected the user can select when they get to sveawebpay
	if($paymentMethod){
		$data['Paymentmethod'] =  $paymentMethod; //internetbank, card, invoice, partpayment
	}
	

	// Ordered Products
	// loop thru all products
	$current_row = 1;
	foreach($wpsc_cart->cart_items as $i => $Item) {
		
		// Get the price without tax
		// $Item->tax were sometimes showing wrong :S
		if(isset($Item->custom_tax_rate)){
			$tax = $Item->custom_tax_rate;
		}else{
			$tax = $wpsc_cart->tax_percentage;
		}
		
		$tax1 = ($tax/100.00);
		$tax2 = ($tax1 + 1);
		
		$price = ($Item->unit_price / $tax2);
	
        $data['Row'.$current_row.'AmountExVAT']    = number_format($price,2, '.', ''); // product price (no tax)
        $data['Row'.$current_row.'VATPercentage']  = number_format($tax); // tax (int)
        $data['Row'.$current_row.'Description']    = urlencode($Item->product_name); // Product name
        $data['Row'.$current_row.'Quantity']       = $Item->quantity; // Quantity
		
		$current_row++;
		
	}
	
	// add shipping cost
	if(!empty($wpsc_cart->base_shipping) && $wpsc_cart->base_shipping > 0){
	
		// tax?
		$s_tax = get_option('sveawebpay_shipping_tax');
		$base = $wpsc_cart->base_shipping;
		
		if(isset($s_tax) && $s_tax > 0 && $s_tax < 100){
			$tax = ($s_tax/100.00);
			$tax2 = ($tax + 1);
			$price = ($base / $tax2);
		}else{
			$price = $base;
			$s_tax = 0;
		}
		
		$name = get_option('sveawebpay_shipping_name');
		if(empty($name)){
			$name = urlencode($wpsc_cart->selected_shipping_option);
		}
		
		
		$data['Row'.$current_row.'AmountExVAT']    = number_format($price,2, '.', ''); // product price (no tax)
        $data['Row'.$current_row.'VATPercentage']  = $s_tax; // tax 0 for shipping?   ?   ?   ?
        $data['Row'.$current_row.'Description']    = $name; // Shipping
        $data['Row'.$current_row.'Quantity']       = 1; // Quantity
	}
	
	#echo '<pre>'; print_r($data); echo '</pre>'; die();

	// put all keys and values together key=value
	foreach($data as $key => $value) {
		$hosted_params_array[] = $key.'='.$value;
	}


	// Password to sveawebpay account
	$pwd = get_option('sveawebpay_password'); //Testinstallation
	
	//https://partnerweb.sveaekonomi.se/webpayhosted2/InitiatePayment.aspx
	// URL to sveaWebPay
	$swp = get_option('sveawebpay_url');
	
	/*
	 * Create the url where to redirect
	 *
	 */
	// the url + parameters
	$process_md5_check = $swp.'?'. mb_convert_encoding(implode('&', $hosted_params_array), 'utf-8');
	// the url and password converted to md5 for security
	$md5 = md5($process_md5_check.$pwd);
	// the final url with the md5 code
	$redirect_url = $process_md5_check.'&MD5='.$md5;
	
	$url = $redirect_url;
	while (strstr($url, '&&')) $url = str_replace('&&', '&', $url);
	while (strstr($url, '&amp;&amp;')) $url = str_replace('&amp;&amp;', '&amp;', $url);
	// header locates should not have the &amp; in the address it breaks things
	while (strstr($url, '&amp;')) $url = str_replace('&amp;', '&', $url);
	
	
	
	
	// Set the transaction id to a unique value for reference in the system.
	#$transaction_id = uniqid(md5(rand(1,666)), true);
	
	// update the transaction id in the database
	$wpdb->query("UPDATE `".WPSC_TABLE_PURCHASE_LOGS."` SET 
				`processed` = '1', 
				`transactid` = '".$md5 ."', 
				`date` = '".time()."'
				WHERE `sessionid` = ".$sessionid." LIMIT 1");
	
	// send the user to SveaWebPay
	header('Location: ' . $url);
	die();

}











/*
 * Callback function when the user returns from SweaWebPay
 *
 *
 */
add_action('init', 'sveawebpay_callback');
function sveawebpay_callback()
{
	global $wpdb;
	

	
	
	
	// *************************
	// *** CONFIRMED PAYMENT ***
	// *************************
	if(isset($_GET['Success']) && $_GET['Success'] == 'true'){
		
		
		/*
		 *   Start by checking if the md5 is correct
		 */
		
		// user password
		$pwd = get_option('sveawebpay_password'); //Testinstallation
		
		// Remove the md5 from the query
		$raw_query = explode('&MD5=', $_SERVER['QUERY_STRING']);
		// the query without md5
		$clean_query = $raw_query[0];
		// the md5 from sveawebpay
		$md5_check = $raw_query[1];
		// the new url with password
		$md5_string  = get_bloginfo('url') .'?'. $clean_query. $pwd;
		
		
		// check if the md5 is correct
		if (md5($md5_string) != $md5_check) {
			die("You can't do that!");
		}else{
		
			$order = $_GET['OrderId'];
			
			// action used for success
			do_action( 'sveawebpaySuccess', $order );
			
			
			// set the payment as paid
			$wpdb->query("UPDATE `".WPSC_TABLE_PURCHASE_LOGS."` SET 
						`processed` = '2', 
						`date` = '".time()."'
						WHERE `id` = ".$order." LIMIT 1");
			//transaction_results($sessionid,false,'',$transaction_id);
			
			// get the session id for redirect
			$purchase_log = $wpdb->get_row(
				"SELECT `sessionid` FROM `".WPSC_TABLE_PURCHASE_LOGS.
				"` WHERE `id`= ".$order." LIMIT 1") ;
				
			$sessionid = $purchase_log->sessionid;
			
			// redirect the user to the transaction page
			$transact_url = get_option('transact_url');
			
			// if empty // just in case
			if(empty($transact_url)) $transact_url = get_bloginfo('url');
			
			// if permalinks
			$separator = "?";
			if (strpos($transact_url,"?")!=false) $separator = "&";
			
			header("Location: ".$transact_url. $separator ."sessionid=".$sessionid);
		}
	}
	
	// *************************
	// *** CANCELLED PAYMENT ***
	// *************************
	if(isset($_GET['Success']) && $_GET['Success'] == 'false'){
	
		$order = $_GET['OrderId'];

		//$log_id = $wpdb->get_var("SELECT `id` FROM `".WPSC_TABLE_PURCHASE_LOGS."` WHERE `transactid`='$MD5' LIMIT 1");
		$log_id = $wpdb->get_var("SELECT `id` FROM `".WPSC_TABLE_PURCHASE_LOGS."` WHERE `id`='$order' LIMIT 1");
		
		$delete_log_form_sql = "SELECT * FROM `".WPSC_TABLE_CART_CONTENTS."` WHERE `purchaseid`='$log_id'";
	
		$cart_content = $wpdb->get_results($delete_log_form_sql,ARRAY_A);
		foreach((array)$cart_content as $cart_item){
			$cart_item_variations = $wpdb->query("DELETE FROM `".WPSC_TABLE_CART_ITEM_VARIATIONS."` WHERE `cart_id` = '".$cart_item['id']."'", ARRAY_A);
		}
		$wpdb->query("DELETE FROM `".WPSC_TABLE_CART_CONTENTS."` WHERE `purchaseid`='$log_id'");
		$wpdb->query("DELETE FROM `".WPSC_TABLE_SUBMITED_FORM_DATA."` WHERE `log_id` IN ('$log_id')");
		$wpdb->query("DELETE FROM `".WPSC_TABLE_PURCHASE_LOGS."` WHERE `id`='$log_id' LIMIT 1");
		
		
		// set an error message
		$_SESSION['WpscGatewayErrorMessage'] = get_option('sveawebpay_error');
		
		// redirect the user to the checkout page
		// if the checkout page isn't set // had problems with this
		$transact_url = get_option('checkout_url');
		if(empty($transact_url)){
			$transact_url = get_option('shopping_cart_url');
		}
		// if neither checkout or cart page is set send to start page
		if(empty($transact_url)){
			$transact_url = get_bloginfo('url');
		}

		header("Location: ".$transact_url);
	}
}








