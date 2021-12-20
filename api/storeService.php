<?php
$projectRoot = filter_input(INPUT_SERVER, "DOCUMENT_ROOT") . '/CustomerRegistrationPlatform';
require_once($projectRoot . '/db/StoreAccessor.php');
require_once ($projectRoot . '/utils/ChromePhp.php');

$method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
if ($method === "GET") {
    doGet();
}
else if ($method === "POST") {
    doPost();
}
else if ($method === "DELETE") {
    doDelete();
}
else if ($method === "PUT") {
    doPut();
}

function doGet() {
    // url = "storeService/stores" ==> get all stores
    if (!filter_has_var(INPUT_GET, 'storeid')) {
        try {
            $sa = new StoreAccessor();
            $results = $sa->getAllStores();
            $results = json_encode($results, JSON_NUMERIC_CHECK);
            echo $results;
        }
        catch (Exception $e) {
            echo "ERROR " . $e->getMessage();
        }
    }
    // url = "storeService/stores/XXX" where XXX is a store ID ==> get just the store with the matching ID
    else {
        ChromePhp::log(filter_input(INPUT_GET, 'storeid'));
    }
}

function doDelete() {
}

// aka CREATE
function doPost() {
}

// aka UPDATE
function doPut() {
}

