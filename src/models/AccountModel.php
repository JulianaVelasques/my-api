<?php 
    namespace App\models;

    class AccountModel {
        private $id;
        private $balance;

        public function __construct($id, $balance){
            $this->id = $id;
            $this->balance = $balance;
        }

        // Methods access the account attributes
        public function getId() {
            return $this->id;
        }

        public function getBalance(){
            return $this->balance;
        }

        // Methods to manipulate the account amounts
        public function deposit($amount){
            $this->balance += $amount;
        }
    }
?>
