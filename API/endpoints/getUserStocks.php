<?php
    require realpath(__DIR__ . '/../db.php');
    require 'jwt/verifyToken.php';

    if ($_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $token = $_SERVER['HTTP_AUTHORIZATION'];

            if (verifyToken($token)) {

                    if (isset($_GET['_id'])) {

                        $collection = $db -> selectCollection('favoriteStocks');

                        $idvalue = $_GET['_id']['$oid'];
                        $search = ['userid' => new MongoDB\BSON\ObjectId($idvalue)];

                        $result = $collection -> findOne($search);

                        $userStocks = $result -> jsonSerialize();

                        if (!empty($userStocks -> stocks)) {
                            http_response_code(200);
                            echo json_encode($userStocks);
                        }
                        else {
                            http_response_code(201);
                            echo json_encode(['error' => 'This user has no stocks.']);
                        }
                    }
                    else {
                        http_response_code(201);
                        echo json_encode(['error' => 'Not all data received']);
                    }
            }
            else {
                http_response_code(202);
                echo json_encode(['error' => 'Unauthorized']);
            }
        }
        else {
            http_response_code(202);
            echo json_encode(['error' => 'Unauthorized, token required']);
        }
    }
    else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
?>