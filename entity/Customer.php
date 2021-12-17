<?php

class Customer implements JsonSerializable {
    //db fields:
    //customerID int auto_increment,
    //firstName varchar(80) ,
    //lastName varchar(80) ,
    //dateOfBirth date ,
    //phone varchar(10),
    //email varchar(80),	
    //loyaltyMember boolean,
    //favoriteStore int not null,
    
    //fields - private
    private $customerID;
    private $firstName;
    private $lastName;
    private $dob;
    private $phone;
    private $email;
    private $loyalty;
    private $favStore;
    
    //constructor
    public function __construct($customerID, $firstName, $lastName, $dob, $phone, $email, $loyalty, $favStore) {
        $this->customerID = $customerID;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->dob = $dob;
        $this->phone = $phone;
        $this->email = $email;
        $this->loyalty = $loyalty;
        $this->favStore = $favStore;
    }

 
    //getters
    function getCustomerID() {
        return $this->customerID;
    }

    function getFirstName() {
        return $this->firstName;
    }

    function getLastName() {
        return $this->lastName;
    }

    function getDob() {
        return $this->dob;
    }

    function getPhone() {
        return $this->phone;
    }

    function getEmail() {
        return $this->email;
    }

    function getLoyalty() {
        return $this->loyalty;
    }

    function getFavStore() {
        return $this->favStore;
    }

   //abstract method from interface   
    public function jsonSerialize() {
       return get_object_vars($this); 
    }

}
