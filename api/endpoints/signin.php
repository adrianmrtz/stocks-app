<?php
    require realpath(__DIR__ . '/../db.php');

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        //Get the POST data
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        //If there is data
        if($data !== null){
            //If all fields were sent
            if(isset($data['username']) && isset($data['password']) && isset($data['name'])) {
                //Use users collection
                $collection = $db -> selectCollection('users');

                //Search if the user already exists
                $search = $collection -> find(['username' => $data['username']]);
                $count = $search -> count();

                //If username does not exists
                if($count <= 0) {
                    //Hash password
                    $newUser = [
                        'username' => $data['username'],
                        'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                        'name' => $data['name']
                    ];

                    //Insert user on collection
                    $collection -> insertOne($newUser);
                    http_response_code(200);
                    echo json_encode(['message' => 'User correctly registered']);
                }
                else {
                    http_response_code(400);
                    echo json_encode(['error' => 'User already exists']);
                }

            }
            else {
                http_response_code(400);
                echo json_encode(['error' => 'Some data is missing']);
            }
        }
        else {
            http_response_code(400);
            echo json_encode(['error' => 'No data received']);
        }
    }
    else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
?>