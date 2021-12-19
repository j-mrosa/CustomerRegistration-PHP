<?php

$projectRoot = $_SERVER['DOCUMENT_ROOT'] . '/jrosa/CustomerRegistrationPlatform';
require_once 'ConnectionManager.php';
require_once ($projectRoot . '/entity/Store.php');

class StoreAccessor {
    //fields:
    //sql strings
    private $getByCodeStr = "select * from Stores where storeCode = :storeCode";
    private $deleteStr = "delete from Stores where StoreCode = :StoreCode";
    private $insertStr = "insert into Stores values (:storeCode, :storeAddress, :phone)";
    private $updateStr = "update Stores set address = :storeAddress, phone = :phone where storeCode = :storeCode";
    //connection
    private $conn = NULL;
    //statements
    private $getByIDStmt = NULL;
    private $deleteStmt = NULL;
    private $insertStmt = NULL;
    private $updateStmt = NULL;

//constructor - throw exception if there is a problem with connection manager or prepared statements
    public function __construct() {
        //instantiate a new connection
        $cm = new ConnectionManager();

        //try to connect to db
        $this->conn = $cm->connect_db();
        if (is_null($this->conn)) {
            throw new Exception("no connection");
        }

        //try to prepare the statements
        $this->getByCodeStmt = $this->conn->prepare($this->getByCodeStr);
        if (is_null($this->getByCodeStmt)) {
            throw new Exception("bad statement: '" . $this->getByCodeStr . "'");
        }

        $this->deleteStmt = $this->conn->prepare($this->deleteStr);
        if (is_null($this->deleteStmt)) {
            throw new Exception("bad statement: '" . $this->deleteStr . "'");
        }

        $this->insertStmt = $this->conn->prepare($this->insertStr);
        if (is_null($this->insertStmt)) {
            throw new Exception("bad statement: '" . $this->insertStr . "'");
        }

        $this->updateStmt = $this->conn->prepare($this->updateStr);
        if (is_null($this->updateStmt)) {
            throw new Exception("bad statement: '" . $this->updateStr . "'");
        }
    }

    //functions:

    /**
     * Gets stores by executing a SQL "select" statement. An empty array
     * is returned if there are no results, or if the query contains an error.
     * 
     * @param String $selectString a valid SQL "select" statement
     * @return array of Store objects
     */
    private function getStoresByQuery($selectStr) {
        $result = [];

        //try to prepare and excute the select string sent in
        try {
            $stmt = $this->conn->prepare($selectStr);
            $stmt->execute();
            $dbResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

            //loop through the db results
            foreach ($dbResults as $r) {
                //create a customer obj with the data
                $obj = new Store($r['storeCode'], $r['storeAddress'], $r['phone']);

                //add customer obj to the array
                array_push($result, $obj);
            }
        } catch (Exception $ex) {
            //if there's an exception, empty array will be returned
            $result = [];
        } finally {
            if (!is_null($stmt)) {
                $stmt->closeCursor();
            }
        }
        
        return $result;
    }
    
    /**
     * Get all stores
     * 
     * @return array of store objects, possibly empty
     */
    public function getAllStores() {
        return $this->getStoresByQuery("select * from stores");
    }

    /**
     * Get store with the specified code.
     * 
     * @param Integer $code = code of the store to retrieve 
     * @return the store object with specified code, or NULL if not found
     */
    public function getStoreByCode($code) {
        $result = NULL;

        try {
            $this->getByCodeStmt->bindParam(":storeCode", $code);
            $this->getByCodeStmt->execute();
            $dbresults = $this->getByCodeStmt->fetch(PDO::FETCH_ASSOC); // not fetchAll

            if ($dbresults) {
                //instatiate a new store object
                $result = new Store($dbresults['storeCode'], $dbresults['storeAddress'], $dbresults['phone']);
            }
        }
        catch (Exception $e) {
            $result = NULL;
        }
        finally {
            if (!is_null($this->getByCodeStmt)) {
                $this->getByCodeStmt->closeCursor();
            }
        }

        return $result;
    }
    
    /**
     * Deletes a store
     * @param $store = object EQUAL TO the store in db to delete
     * @return boolean = indicates whether the store was deleted
     */
    public function deleteStore($store) {
        $success = false;

        $storeCode = $store->getStoreCode(); // only the code is needed

        try {
            $this->deleteStmt->bindParam(":storeCode", $storeCode);
            $success = $this->deleteStmt->execute();
        }
        catch (PDOException $e) {
            $success = false;
        }
        finally {
            if (!is_null($this->deleteStmt)) {
                $this->deleteStmt->closeCursor();
            }
            return $success;
        }
    }
    
    /**
     * Inserts a store into the db
     * 
     * @param $store = object of type Store
     * @return boolean = indicates if the store was inserted
     */
    public function insertItem($store) {
        $success = false;

        $storeCode = $store->getStoreCode();
        $phone = $store->getPhone();
        $address = $store->getAddress();

        try {
            $this->insertStmt->bindParam(":storeCode", $storeCode);
            $this->insertStmt->bindParam(":phone", $phone);
            $this->insertStmt->bindParam(":storeAddress", $address);
            $success = $this->insertStmt->execute();
        }
        catch (PDOException $e) {
            $success = false;
        }
        finally {
            if (!is_null($this->insertStmt)) {
                $this->insertStmt->closeCursor();
            }
            return $success;
        }
    }
    
    /**
     * Updates a store in the db
     *  
     * @param $store = object of type Store with new values to replace db current values
     * @return boolean = indicates if the store was updated
     */
    public function updateStore($store) {
        $success = false;
        
        $storeCode = $store->getStoreCode();
        $phone = $store->getPhone();
        $address = $store->getAddress();

        try {
            $this->updateStmt->bindParam(":storeCode", $storeCode);
            $this->updateStmt->bindParam(":phone", $phone);
            $this->updateStmt->bindParam(":storeAddress", $address);
            $success = $this->updateStmt->execute();
        }
        catch (PDOException $e) {
            $success = false;
        }
        finally {
            if (!is_null($this->updateStmt)) {
                $this->updateStmt->closeCursor();
            }
            return $success;
        }
    }
}

//end of class

    