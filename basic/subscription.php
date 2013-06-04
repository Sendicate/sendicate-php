<?php
/**
 * 
 * 
 * 
*/

// Init the error logging report, just in case...
ini_set('error_reporting', E_ALL);
error_reporting(E_ALL);
ini_set('log_errors',TRUE);
ini_set('html_errors',FALSE);
ini_set('error_log','errors.log');
ini_set('display_errors',FALSE);

include_once('lib/wrapper.php');



// Your List ID
$_list_id	= 'xxxxxxxx';

// Your API Token
$_api_token = 'xxxxxxxxxxxxxxxxxxxx';



if($_POST) {

	// Initialize variables...
	$_subscriber_name = 'Guest Subscriber';
	$_subscriber_email = false;


	// Validate if Name exists
	if(isset($_POST['subscriber_name'])) {
		if($_POST['subscriber_name'] != '') {
			$_subscriber_name = $_POST['subscriber_name'];
		}
	}

	// Validate if Name exists
	if(isset($_POST['subscriber_email'])) {
		$_subscriber_email = $_POST['subscriber_email'];
	}

	// Uncoment and reuse this line in order to add a new field
	// if(isset($_POST['NEW_FIELDNAME'])) {
	// 	$_new_field_name = $_POST['NEW_FIELDNAME'];
	// }
	

	// Add validations for all fields that are required to post to Sendicate API
	// Otherwise we need to fail
	if(!$_subscriber_email) {
		echo 'Please insert a valid email and try again...';
		die();
	}

	
	// Add your own validations above this comment.

	/**
	 * Build params to send
	 * Uncoment last line to add one more field to the params.
	 * 
	 * Replace "fieldname_in_sendicate" with your Sendicate field name.
	 * Replace "$_new_field_name" with the name of the php variable in previous lines.
	 */
	$_params = array(
		'name'		=> $_subscriber_name
		,'email'	=> $_subscriber_email
		//,'fieldname_in_sendicate' => $_new_field_name
	);



	/**
	 * That's it! You don't need to keep changing things.
	 * Accept them in the way how they are, and be free...
	 */
	
	// Validate if List ID exists
	if(!$_list_id) {
		echo 'No List ID configured.';
		die();
	}


	$_sendicate = new Sendicate_Wrapper($_api_token);
	$_sendicate->setDebug(false);

	$_url 		= 'lists/' . $_list_id . '/subscribers';


	$_sendicate->callServer('POST', $_url, $_params);
	
	$response = $_sendicate->getResponse();

	if($response && $response->imported = 1) {
		echo 'Thanks for your subscription!';
	} else {
		echo 'Something fails when posting to Sendicate API, check the log details.';
		echo $response->errors;
	}


} else {
	echo 'No data was received, check your form code.';

}

die();

