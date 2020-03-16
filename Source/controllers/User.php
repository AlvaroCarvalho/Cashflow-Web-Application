<?php

    namespace Source\controllers;
    use Source\models\User as UserModel;
    use League\Plates\Engine;

    class User
    {
        private $view;
        private $router;

        public function __construct($router)
        {
            $this->view = Engine::create(__DIR__ . "/../views", "php");
            $this->router = $router;
        }

        public function main(): void
        {
            session_start();
            
            if ($_SESSION["user"]->type) {
                $users = (new UserModel())->find()->fetch(true); 
                echo $this->view->render("users/user-form", ["users" => $users]);
            } else {
                $this->router->redirect(url("app"));
            }
        }

        public function create(): void
        {
            $data = json_decode(file_get_contents("php://input"));
            $user = new UserModel();

            $user->username = $data->username;
            $user->password = $data->password;
            $user->name = $data->name;
            $user->type = $data->type;
            $user->save();

            if ($user->fail()) {
                $response["success"] = false;
                $text = $user->fail()->getMessage();
                $response["message"] = message($text, "error");
            } else {
                $response["success"] = true;
                $text = "O usuário foi adicionado com sucesso!";
                $response["message"] = message($text, "success");
            }

            echo json_encode($response);
        }

        public function destroy(array $data): void
        {
            $user = (new UserModel())->findById($data["id"]);
            $user->destroy();

            if ($user->fail()) {
                $response["success"] = false;
            } else {
                $response["success"] = true;
            }

            echo json_encode($response);
        }

        public function update(): void
        {
            $data = json_decode(file_get_contents("php://input"));
            $user = (new UserModel())->findById($data->id);

            $user->username = $data->username;
            $user->password = $data->password;
            $user->name = $data->name;
            $user->type = $data->type;
            $user->save();

            if ($user->fail()) {
                $response["success"] = false;
                $text = $user->fail()->getMessage();
                $response["message"] = message($text, "error");
            } else {
                $response["success"] = true;
                $text = "O usuário foi autlizado com sucesso!";
                $response["message"] = message($text, "success");
            }

            echo json_encode($response);
        }

        public function updateStatus(array $data): void
        {
            $user = (new UserModel())->findById($data["id"]);
            $user->status = $data["status"];
            $user->save();

            if ($user->fail()) {
                $response["success"] = false;
            } else {
                $response["success"] = true;
            }

            echo json_encode($response);
        }
    }