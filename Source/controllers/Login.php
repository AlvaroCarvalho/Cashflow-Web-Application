<?php

    namespace Source\controllers;
    use Source\models\User;
    use League\Plates\Engine;
    use CoffeeCode\Router\Router;
    
    class Login
    {
        private $router;
        private $view;

        public function __construct($router)
        {
            $this->router = $router;
            $this->view = Engine::create(__DIR__ . "/../views", "php");
        }

        public function login(): void
        {   
            echo $this->view->render("login");
        }

        public function validate(array $data): void
        {            
            $user = new User();
            $user = $user->get($data);

            if (!empty($user) && $user[0]->status) {
                session_start();
                $_SESSION["user"] = $user[0];

                if ($_SESSION["user"]->type) {
                    $this->router->redirect(url("app/usuarios"));
                } else {
                    $this->router->redirect(url("app/lancamentos"));
                }
            } else {
                echo $this->view->render("login", ["success" => false]);
            }
        }
    }