<?php     
    namespace App\controllers;

    use App\models\AccountModel;
    use App\Utils\AccountUtils;
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;


    class AccountController {
        // Load accounts from file when the controller is instantiated
        private $accounts = [];

        public function __construct() {
            $this->accounts = AccountUtils::loadAccountsFromFile();
        }

        public function reset(Request $request, Response $response, $args) {
            // Clean accounts from memory
            $this->accounts = [];

            // Clear accounts from file
            file_put_contents('accounts.txt', '');

            $response->getBody()->write("OK");
            return $response -> withStatus(200);
        }

        public function getBalance(Request $request, Response $response, $args){
            $queryParams = $request -> getQueryParams();
            $accountId = $queryParams["account_id"];

            // Early return
            if(empty($this->accounts)){
                $response->getBody()->write("0");
                return $response->withStatus(404);
            }

            $foundAccountId = AccountUtils::checkAccountId($this->accounts, $accountId);

            if($foundAccountId !== null){
                $balance = $foundAccountId->getBalance();
                $balanceAsString = strval($balance); // convert to string to write
                $response->getBody()->write($balanceAsString);
                return $response->withStatus(200);
            } else {
                $response->getBody()->write("0");
                return $response->withStatus(404);
            }
        }

        public function resolveEvent(Request $request, Response $response, $args){
            $data = json_decode($request->getBody()->getContents(), true);

            if($data["type"] === "deposit"){
                $accountId = $data["destination"];

                if(empty($this->accounts)){
                    $newAccount = new AccountModel($accountId, $data["amount"]);

                    $this->accounts[] = $newAccount;

                    AccountUtils::saveAccountToFile($this->accounts);

                    // Create response object
                    $responseData = [
                        'destination' => [
                            'id' => $newAccount->getId(),
                            'balance' => $newAccount->getBalance(),
                        ]
                    ];

                    // Transform response in json
                    $jsonResponse = json_encode($responseData);

                    $response->getBody()->write($jsonResponse);

                    return $response->withStatus(201);
                }

                $foundAccount = AccountUtils::checkAccountId($this->accounts, $accountId);

                // Account not empty but the id was not found
                if($foundAccount === null){
                    // Create an account
                    $newAccount = new AccountModel($accountId, $data["amount"]);

                    $this->accounts[] = $newAccount;

                    AccountUtils::saveAccountToFile($this->accounts);

                    // Create response object
                    $responseData = [
                        'destination' => [
                            'id' => $newAccount->getId(),
                            'balance' => $newAccount->getBalance(),
                        ]
                    ];

                    // Transform response in json
                    $jsonResponse = json_encode($responseData);

                    $response->getBody()->write($jsonResponse);

                    return $response->withStatus(201);
                } else {
                    $foundAccount->deposit($data["amount"]);

                    AccountUtils::saveAccountToFile($this->accounts);

                    // Create response object
                    $responseData = [
                        'destination' => [
                            'id' => $foundAccount->getId(),
                            'balance' => $foundAccount->getBalance(),
                        ]
                    ];

                    // Transform response in json
                    $jsonResponse = json_encode($responseData);

                    $response->getBody()->write($jsonResponse);

                    return $response->withStatus(201);
                }        

            }


            if($data["type"] === "withdraw"){
                $accountId = $data["origin"];

                $foundAccount = AccountUtils::checkAccountId($this->accounts, $accountId);

                if($foundAccount === null){
                    $response->getBody()->write("0");
                    return $response->withStatus(404);
                }

                $foundAccount->withdraw($data["amount"]);

                AccountUtils::saveAccountToFile($this->accounts);
                // Create response object
                $responseData = [
                    'origin' => [
                        'id' => $foundAccount->getId(),
                        'balance' => $foundAccount->getBalance(),
                    ]
                ];

                // Transform response in json
                $jsonResponse = json_encode($responseData);
                
                $response->getBody()->write($jsonResponse);
                return $response->withStatus(201);
                
            }

            if($data["type"] === "transfer"){
                $accountOriginId = $data["origin"];
                $accountDestinationId = $data["destination"];
                $destinationAmount = $data["amount"];

                $foundAccountOrigin = AccountUtils::checkAccountId($this->accounts, $accountOriginId);

                if($foundAccountOrigin === null){
                    $response->getBody()->write("0");
                    return $response->withStatus(404); 
                } 
                
                $foundAccountOrigin->transfer($destinationAmount);

                    AccountUtils::saveAccountToFile($this->accounts);
    
                    // Create response object
                    $responseData = [
                        'origin' => [
                            'id' => $foundAccountOrigin->getId(),
                            'balance' => $foundAccountOrigin->getBalance(),
                        ],
    
                        'destination' => [
                            'id' => $accountDestinationId,
                            'balance' => $destinationAmount,
                        ]
                    ];
    
                    // Transform response in json
                    $jsonResponse = json_encode($responseData);
                        
                    $response->getBody()->write($jsonResponse);
                    return $response->withStatus(201);     
            }
        }
    }
?>