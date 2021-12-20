<?php
$projectRoot = $_SERVER['DOCUMENT_ROOT'] . '/CustomerRegistrationPlatform';
require_once ($projectRoot . '/db/CustomerAccessor.php');
require_once ($projectRoot . '/entity/Customer.php');
require_once ($projectRoot . '/utils/ChromePhp.php');

$method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');

if ($method === "GET") {
    doGet();
} else if ($method === "POST") {
    doPost();
} else if ($method === "DELETE") {
    doDelete();
} else if ($method === "PUT") {
    doPut();
}

function doGet() {
    
    // individual
    //checks if this parameter exists -> if id was sent
    if (filter_has_var(INPUT_GET, 'customerid')) {  // same as $_GET['customerID']
        // Individual gets not implemented.
        ChromePhp::log("Sorry, individual gets not allowed!");
    }
    // collection
    //if id is not a parameter sent, it means it's requesting all items
    else {
        try {
           
        
            $ca = new CustomerAccessor();
            $results = $ca->getAllCustomers();
            $results = json_encode($results, JSON_NUMERIC_CHECK);
            
            //sends back a json encoded array of results
            echo $results;
        } catch (Exception $e) {
            echo "ERROR " . $e->getMessage();
        }
    }
}

function doDelete() {
    //check if there's a parameter itemid
    if (filter_has_var(INPUT_GET, 'customerid')) {
        $customerid = filter_input(INPUT_GET, 'customerid'); // same as $_GET['itemid']
        // Only the ID of the item matters for a delete,
        // but the accessor expects an object, 
        // so we need a dummy object.
        $customerObj = new Customer($customerid, "any", "any", "any", "any", "any", 1, "201");

        // delete the object from DB
        $ca = new CustomerAccessor();
        
        //this method only accepts an object, that's why we need to create the dummy obj:
        $success = $ca->deleteCustomer($customerObj);
        
        //returns a boolean
        echo $success;
    } else {
        // Bulk deletes not implemented.
        ChromePhp::log("Sorry, bulk deletes not allowed!");
    }
}

// aka CREATE
function doPost() {
    //checks if there's an id sent as parameter
    if (filter_has_var(INPUT_GET, 'customerid')) {
        // The details of the item to insert will be in the request body.
        //gets data sent with the request:
        $body = file_get_contents('php://input');
        $contents = json_decode($body, true);

        /*
        "customerID": inID,
        "firstName": inFirstName,
        "lastName": inLastName,
        "dob": inDob,
        "phone": inPhone,
        "email": inEmail,
        "loyalty": inLoyalty,
        "favStore": inFavStore
         */
        // create a customer object, using data received
        $customerObj = new Customer($contents['customerID'], $contents['firstName'], $contents['lastName'], $contents['dob'], $contents['phone'], $contents['email'], $contents['loyalty'], $contents['favStore']);

        // add the object to DB
        $ca = new CustomerAccessor();
        $success = $ca->insertCustomer($customerObj);
        
        //returns a boolean
        echo $success;
    } else {
        // Bulk inserts not implemented.
        ChromePhp::log("Sorry, bulk inserts not allowed!");
    }
}


// aka UPDATE
function doPut() {
    //checks if the id was sent as parameter
    if (filter_has_var(INPUT_GET, 'customerid')) {
        // The details of the item to update will be in the request body.
        //gets data sent with the request:
        $body = file_get_contents('php://input');
        $contents = json_decode($body, true);

        // create a MenuItem object
        $customerObj = new Customer($contents['customerID'], $contents['firstName'], $contents['lastName'], $contents['dob'], $contents['phone'], $contents['email'], $contents['loyalty'], $contents['favStore']);


        // update the object in the  DB
        $ca = new CustomerAccessor();
        $success = $ca->updateCustomer($customerObj);
        echo $success;
    } else {
        // Bulk updates not implemented.
        ChromePhp::log("Sorry, bulk updates not allowed!");
    }
}