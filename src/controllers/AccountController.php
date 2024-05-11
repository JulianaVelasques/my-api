<?php     
    namespace App\controllers;
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;

    // "Dataset"
    $accounts = [];

    class AccountController {

        public function reset(Request $request, Response $response, $args) {
            // Get global variable
            global $accounts;
            // Clean "dataset"
            $accounts = [];
        
            return $response -> withStatus(200);
        }

        public function getBalance(Request $request, Response $response, $args){
            $queryParams = $request -> getQueryParams();
            $accountId = $queryParams["account_id"];
            global $accounts;
            $foundAccountId = null;

            // Early return
            if(empty($accounts)){
                $response->getBody()->write("404 0");
                return $response->withStatus(404);
            } else{
                foreach($accounts as $account){
                    if($account->getId() === $accountId){
                        global $foundAccountId;
                        $foundAccountId = $account;
                        break;
                    }
                }
                if($foundAccountId !== null){
                    $balance = $foundAccountId->getBalance();
                    $balanceAsString = strval($balance); // convert to string to write
                    $response->getBody()->write("200 " . $balanceAsString);
                    return $response->withStatus(200);
                } else {
                    $response->getBody()->write("404 0");
                    return $response->withStatus(404);
                }
            }
        }
    }
?>