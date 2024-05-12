<?php     
    namespace App\controllers;

    use App\models\AccountModel;
    use App\Utils\AccountUtils;
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;

    // Start session if not yet
    if (!isset($_SESSION['accounts'])) {
        $_SESSION['accounts'] = [];
    }

    class AccountController {

        public function reset(Request $request, Response $response, $args) {
            $_SESSION['accounts'] = [];

            $response->getBody()->write("OK");
        
            return $response -> withStatus(200);
        }

        public function getBalance(Request $request, Response $response, $args){
            $queryParams = $request -> getQueryParams();
            $accountId = $queryParams["account_id"];

            // Early return
            if(empty($_SESSION['accounts'])){
                $response->getBody()->write("0");
                return $response->withStatus(404);
            } else{
                $foundAccountId = AccountUtils::checkAccountId($_SESSION['accounts'], $accountId);

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
        }

        public function resolveEvent(Request $request, Response $response, $args){
            $data = json_decode($request->getBody()->getContents(), true);

            if($data["type"] === "deposit"){
                $accountId = $data["destination"];

                if(empty($_SESSION['accounts'])){
                    $newAccount = new AccountModel($accountId, $data["amount"]);
                    $_SESSION['accounts'][] = $newAccount;

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
                    $foundAccount = AccountUtils::checkAccountId($_SESSION['accounts'], $accountId);

                    // Account not empty but the id was not found
                    if($foundAccount === null){
                        // Create an account
                        $newAccount = new AccountModel($accountId, $data["amount"]);
                        $_SESSION['accounts'][] = $newAccount;
        
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

                        return $response->withStatus(200);
                    }
                }
            }
        }
    }
?>