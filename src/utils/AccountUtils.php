<?php
    namespace App\Utils;

    class AccountUtils {
        public static function checkAccountId($accounts, string $accountId){
            foreach($accounts as $account){
                if($account->getId() === $accountId){
                    return $account;
                }
            }
            return null;
        }
    }
?>