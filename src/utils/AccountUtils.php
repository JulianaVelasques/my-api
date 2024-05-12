<?php
    namespace App\Utils;

    use App\models\AccountModel;

    class AccountUtils {
        public static function getAccountById($accounts, string $accountId){
            if(empty($accounts)){
                return null;
            }
            
            foreach($accounts as $account){
                if($account->getId() === $accountId){
                    return $account;
                }
            }
            return null;
        }

        public static function saveAccountToFile($accounts) {
            $serializedData = '';
            foreach ($accounts as $account) {
                $accountArray = ['id' => $account->getId(), 'balance' => $account->getBalance()];
                $serializedData .= json_encode($accountArray) . PHP_EOL; // Add newline character
            }
            file_put_contents('accounts.txt', $serializedData); // Overwrite the entire file with updated account data
        }

        public static function loadAccountsFromFile() {
            $serializedData = file_get_contents('accounts.txt');
            $accountsData = explode(PHP_EOL, $serializedData); // Split by newlines
    
            $accounts = [];
            foreach ($accountsData as $accountData) {
                if (!empty($accountData)) {
                    $accountArray = json_decode($accountData, true);
                    $accounts[] = new AccountModel($accountArray['id'], $accountArray['balance']);
                }
            }
    
            return $accounts;
        }
    }
?>