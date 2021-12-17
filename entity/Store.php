<?php

class Store implements JsonSerializable{
    //db fields:
    //storeCode int,
    //storeAddress varchar(80) not null,
    //phone varchar(10) not null,
    
    //fields - private
    private $code;
    private $address;
    private $phone;
    
    //constructor
    public function __construct($code, $address, $phone) {
        $this->code = $code;
        $this->address = $address;
        $this->phone = $phone;
    }
    
    //getters
    function getCode() {
        return $this->code;
    }

    function getAddress() {
        return $this->address;
    }

    function getPhone() {
        return $this->phone;
    }

    //abstract method from interface
    public function jsonSerialize() {
        return get_object_vars($this);
    }

}
