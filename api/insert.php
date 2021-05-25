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

// Validate depending on the table enum given.
if(trim($_GET['entity']) == Table::MEMBER) {
	// Validate the name 
	if(!isset($_GET['name'])) {
		$return['message'] = 'Please add the name of the member';
		echo json_encode($return);
		exit;
	} else if(trim($_GET['name']) == '') {
		$return['message'] = 'Please add the name of the member';
		echo json_encode($return);
		exit;
	}
	// Validate the cellphone number
	if(!isset($_GET['cellphone'])) {
		$return['message'] = 'Please add the cellphone number of the member';
		echo json_encode($return);
		exit;
	} else if(trim($_GET['cellphone']) == '') {
		$return['message'] = 'Please add the cellphone number of the member';
		echo json_encode($return);
		exit;
    } else if($requestObject->_object->validateNumber(trim($_GET['cellphone'])) == '') {
        $errors[] = 'Please add a valid cellphone number, it must be 10 digits and starts with a zero';
    }  else {
        /* Check if cellphone already exists. */
        $checkCellphone = $requestObject->_object->getByCellphone(trim($_GET['cellphone']));
        if($checkCellphone) {
            $return['message'] = 'The cellphone is already bing used, please choose another one';
			echo json_encode($return);
			exit;			
        }
    }
	// Validate the email address. It is not required, so we will check only if its populated
	if(isset($_GET['email']) && trim($_GET['email']) != '') {
		if($requestObject->_object->validateEmail(trim($_GET['email'])) == '') {
            $return['message'] = 'Please add a valid email address';
			echo json_encode($return);
			exit;	
		} else {
			/* Check if email already exists. */
			$checkEmail = $requestObject->_object->getByEmail(trim($_GET['email']));
			if($checkEmail) {
				$return['message'] = 'The email is already bing used, please choose another one';
				echo json_encode($return);
				exit;			
			}
		}
    }
	// Insert the item.
	$data				= array();				
	$data['name']		= trim($_GET['name']);
	$data['cellphone']	= trim($_GET['cellphone']);
	$data['email']		= trim($_GET['email']);

	$return			= $requestObject->_object->insert($data);

	if((int)$return['code'] != 200) {
		$return['message'] = $return['message'];
		echo json_encode($return);
		exit;	
	}
	
} else if(trim($_GET['entity']) == Table::ANIMAL) {
	// Validate the name 
	if(!isset($_GET['name'])) {
		$return['message'] = 'Please add the name of the member';
		echo json_encode($return);
		exit;
	} else if(trim($_GET['name']) == '') {
		$return['message'] = 'Please add the name of the member';
		echo json_encode($return);
		exit;
	}
	// Insert the item.
	$data			= array();				
	$data['name']	= trim($_GET['name']);
	$return			= $requestObject->_object->insert($data);

	if((int)$return['code'] != 200) {
		$return['message'] = $return['message'];
		echo json_encode($return);
		exit;	
	}	
}
// Lets get the data if any.
echo json_encode($return);
exit;
$return = $requestObject = $data = null;
unset($return, $requestObject, $data);
?>