<?php

    namespace Source\controllers;
    use League\Plates\Engine;
    use CoffeeCode\Router\Router;
    use Source\models\Action;
    use Source\models\Payment;

    class App
    {
        private $view;
        private $router;

        public function __construct($router)
        {     
            session_start();
            $this->router = $router;

            if (isset($_SESSION) && !empty($_SESSION)) {
                $this->view = Engine::create(__DIR__ . "/../views", "php");
            } else {
                $this->router->redirect("");
            }
        }

        public function logout(): void
        {
            unset($_SESSION);
            session_destroy();

            $this->router->redirect("");
        }

        public function main(): void
        {
            echo $this->view->render("main");
        }

        public function register(): void
        {
            if ($_SESSION["user"]->type) {
                $action = new Action();
                $payment = new Payment();

                $actions = $action->find()->fetch(true);
                $payments = $payment->getAll();

                echo $this->view->render("registers/register", ["actions" => $actions, "payments" => $payments]);
            } else {                
                $this->router->redirect(url("app"));
            }
        }
    }