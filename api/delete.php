<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
/* Add this on all pages on top. */
set_include_path($_SERVER['DOCUMENT_ROOT'].'/'.PATH_SEPARATOR.$_SERVER['DOCUMENT_ROOT'].'/library/classes/');
/*** Standard includes */
require_once 'request/request.php';
// Return data.
$return = array('code' => 500, 'message' => '');
// Make sure the name of the table to be queueed is selected
if(!isset($_GET['entity'])) {
	$return['message'] = 'Please select the entity to be requested';
	echo json_encode($return);
	exit;
} else if(trim($_GET['entity']) == '') {
	$return['message'] = 'Please select the entity to be requested';
	echo json_encode($return);
	exit;
} else if(!preg_match('/^[a-z]+/', trim($_GET['entity']))) {
	$return['message'] = 'Please make sure that the name parameter only has letters';
	echo json_encode($return);
	exit;
} else if(trim($_GET['entity']) != Table::MEMBER && trim($_GET['entity']) != Table::ANIMAL) {
	$return['message'] = 'Invalid entity given.';
	echo json_encode($return);
	exit;
}

// Get the object
$requestObject = new Request(trim($_GET['entity']));

// Validate the id 
if(!isset($_GET['id'])) {
	$return['message'] = 'Please add the id to be updated';
	echo json_encode($return);
	exit;
} else if(trim($_GET['id']) == '') {
	$return['message'] = 'Please add the id to be updated';
	echo json_encode($return);
	exit;
} else {
	/* Check if cellphone already exists. */
	$memberData = $requestObject->_object->single(trim($_GET['id']));
	if(!$memberData) {
		$return['message'] = 'We could not find the member';
		echo json_encode($return);
		exit;			
	}
}
// Delete item
$return	= $requestObject->_object->delete(array('id' => $memberData['id']));

if((int)$return['code'] != 200) {
	$return['message'] = $return['message'];
	echo json_encode($return);
	exit;	
}

// Lets get the data if any.
echo json_encode($return);
exit;
$return = $requestObject = $data = $animalData = null;
unset($return, $requestObject, $data, $animalData);
?>