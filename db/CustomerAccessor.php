<?php

$projectRoot = $_SERVER['DOCUMENT_ROOT'] . '/jrosa/CustomerRegistrationPlatform';
require_once 'ConnectionManager.php';
require_once (dirname(__FILE__). '/entity/Customer.php');
require_once (dirname(__FILE__). "/utils/ChromePhp.php");

class CustomerAccessor {

    //fields:
    //sql strings
    private $getByIDStr = "select * from Customers where customerID = :customerID";
    private $deleteStr = "delete from Customers where customerID = :customerID";
    private $insertStr = "insert into Customers values (null, :firstName, :lastName, :dob, :phone, :email, :loyalty, :favStore)";
    private $updateStr = "update Customers set firstName = :firstName, lastName = :lastName, dateOfBirth = :dob, phone = :phone, " .
            "email = :email, loyaltyMember = :loyalty, favoriteStore = :favStore where customerID = :customerID" ;
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
        $this->getByIDStmt = $this->conn->prepare($this->getByIDStr);
        if (is_null($this->getByIDStmt)) {
            throw new Exception("bad statement: '" . $this->getByIDStr . "'");
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
        //ChromePhp::log("oi2");
        if (is_null($this->updateStmt)) {
            throw new Exception("bad statement: '" . $this->updateStr . "'");
        }
    }

    //functions:

    /**
     * Gets customers by executing a SQL "select" statement. An empty array
     * is returned if there are no results, or if the query contains an error.
     * 
     * @param String $selectString a valid SQL "select" statement
     * @return array of Customer objects
     */
    private function getCustomersByQuery($selectStr) {
        $result = [];

        //try to prepare and excute the select string sent in
        try {
            $stmt = $this->conn->prepare($selectStr);
            $stmt->execute();
            $dbResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

            //loop through the db results
            foreach ($dbResults as $r) {
                //create a customer obj with the data
                $obj = new Customer($r['customerID'], $r['firstName'], $r['lastName'], $r['dateOfBirth'],
                        $r['phone'], $r['email'], $r['loyaltyMember'], $r['favoriteStore']);

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

        //return the array of customer objects
        return $result;
    }

    /**
     * Gets all customers.
     * 
     * @return array of customer objects, possibly empty
     */
    public function getAllCustomers() {
        //call the other function
        return $this->getCustomersByQuery("select * from Customers");
    }

    /**
     * Gets the customer with an specific ID.
     * 
     * @param Integer $id = the ID of the customer we want to retrieve 
     * @return the customer obj with the specified ID, or NULL if not found
     */
    private function getCustomerByID($id) {
        $result = NULL;

        try {
            $this->getByIDStmt->bindParam(":customerID", $id);
            $this->getByIDStmt->execute();
            $dbresults = $this->getByIDStmt->fetch(PDO::FETCH_ASSOC); //not fetch all - because is only one result
            //if (!is_null($dbresults)){
            if ($dbresults) {
                //create a customer obj with the data
                $result = new Customer($r['customerID'], $r['firstName'], $r['lastName'], $r['dateOfBirth'],
                        $r['phone'], $r['email'], $r['loyaltyMember'], $r['favoriteStore']);
            }
        } catch (Exception $e) {
            $result = NULL;
        } finally {
            if (!is_null($this->getByIDStmt)) {
                $this->getByIDStmt->closeCursor();
            }
        }

        return $result;
    }

    /**
     * Deletes a customer.
     * @param customer object with same ID as the ID of the customer to delete
     * @return boolean = indicates if customer was deleted
     */
    public function deleteCustomer($customer) {
        $success = false;

        $customerID = $customer->getCustomerID(); // only the ID is needed

        try {
            $this->deleteStmt->bindParam(":customerID", $customerID);
            $success = $this->deleteStmt->execute(); // this doesn't mean what you think it means
            $rc = $this->deleteStmt->rowCount();
        } catch (PDOException $e) {
            $success = false;
        } finally {
            if (!is_null($this->deleteStmt)) {
                $this->deleteStmt->closeCursor();
            }
            return $success;
        }
    }
    
    /**
     * Inserts a customer into the db
     * 
     * @param $customer = an object of type Customer
     * @return boolean = indicates if customer was inserted
     */
    public function insertCustomer($customer) {
        $success = false;

        //$customerID = $customer->getCustomerID();
        $firstName = $customer->getFirstName();
        $lastName = $customer->getLastName();
        $dob = $customer->getDob();
        $phone = $customer->getPhone();
        $email = $customer->getEmail();
        //$loyalty = $customer->getLoyalty();
        //ChromePhp::log($loyalty);
        $loyalty = ($customer->getLoyalty() === true) ? 1 : 0;
        $favStore = $customer->getFavStore();

        try {
            //$this->insertStmt->bindParam(":customerID", $customerID);
            $this->insertStmt->bindParam(":firstName", $firstName);
            $this->insertStmt->bindParam(":lastName", $lastName);
            $this->insertStmt->bindParam(":dob", $dob);
            $this->insertStmt->bindParam(":phone", $phone);
            $this->insertStmt->bindParam(":email", $email);            
            $this->insertStmt->bindParam(":loyalty", $loyalty);

            $this->insertStmt->bindParam(":favStore", $favStore);
            
            //execute and check if it was succesfull
            $success = $this->insertStmt->execute();
        }
        catch (PDOException $e) {
            $success = false;
            ChromePhp::log($e->getMessage());
        }
        finally {
            if (!is_null($this->insertStmt)) {
                $this->insertStmt->closeCursor();
            }
            return $success;
        }
    }
    
    /**
     * Updates a customer in the db
     * 
     * @param $customer = object of type Customer, containing values to replace the db current values
     * @return boolean = indicates if the customer was updated
     */
    public function updateCustomer($customer) {
        $success = false;

        $customerID = $customer->getCustomerID();
        $firstName = $customer->getFirstName();
        $lastName = $customer->getLastName();
        $dob = $customer->getDob();
        $phone = $customer->getPhone();
        $email = $customer->getEmail();
        $loyalty = ($customer->getLoyalty() === true) ? 1 : 0;
        $favStore = $customer->getFavStore();
        
        
        try {
            
            $this->updateStmt->bindParam(":customerID", $customerID);
            $this->updateStmt->bindParam(":firstName", $firstName);
            $this->updateStmt->bindParam(":lastName", $lastName);
            $this->updateStmt->bindParam(":dob", $dob);
            $this->updateStmt->bindParam(":phone", $phone);
            $this->updateStmt->bindParam(":email", $email);  
            //$loy = $loyalty == true ? 1 : 0;
            $this->updateStmt->bindParam(":loyalty", $loyalty);
            $this->updateStmt->bindParam(":favStore", $favStore);

            //execute and check if it was succesfull
            $success = $this->updateStmt->execute();// this doesn't mean what you think it means

        }
        catch (PDOException $e) {
            $success = false;
            ChromePhp::log($e->getMessage());
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


