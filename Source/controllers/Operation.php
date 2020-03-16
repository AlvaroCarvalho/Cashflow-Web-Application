<?php

    namespace Source\controllers;
    use League\Plates\Engine;
    use Source\models\Balance;
    use Source\models\Comment;
    use Source\models\Bleeding;
    use Source\models\BleedingComment;

    class Operation
    {
        private $view;
        private $router;
        public $id;

        public function __construct($router)
        {
            session_start();
            $this->id = $_SESSION["user"]->id;
            $this->router = $router;
            $this->view = Engine::create(__DIR__ . "/../views", "php");
        }

        public function main(): void
        {
            session_start();
            echo $this->view->render('operations/operation', ["router" => $this->router]);
        }

        public function saveBalance(): void
        {
            $data = json_decode(file_get_contents('php://input'));

            $balance = new Balance();
            $balance->opening = $data->opening;
            $balance->supply = $data->supply;
            $balance->closing = $data->closing;
            $balance->bleeding = $data->bleeding;
            $balance->credit = $data->credit;
            $balance->user = $this->id;
            
            $balance->save();

            if ($balance->fail()) {
                $response["success"] = false;
                $text = $balance->fail()->getMessage();
                $response["message"] = message($text, "error");
            } else {
                $response["success"] = true;
                $text = "O caixa foi fechado com successo!";
                $response["message"] = message($text, "success");
            }

            echo json_encode($response);
        }

        public function saveComment(): void
        {
            $data = json_decode(file_get_contents("php://input"));
            $comment = new Comment();

            $comment->user = $this->id;
            $comment->shift = $data->shift;
            $comment->deliveryman = $data->deliveryman;
            $comment->contact = $data->contact;
            $comment->comment = $data->comment;

            $comment->save();

            if ($comment->fail()) {
                $response["success"] = false;
                $text = $comment->fail()->getMessage();
                $response["message"] = message($text, "error");
            } else {
                $response["success"] = true;
                $text = "O comentário foi criado com successo!";
                $response["message"] = message($text, "success");
            }

            echo json_encode($response);
        }

        public function saveBleeding(): void
        {
            $data = json_decode(file_get_contents('php://input'));
            $bleeding = new Bleeding();
            $bleedingComment = new BleedingComment();

            $bleeding->user = $this->id;
            $bleedingComment->user = $this->id;
            $bleeding->shift = $data->shift;
            $bleedingComment->shift = $data->shift;

            foreach ($data->values as $key => $value) {
                if ($key != "others") {
                    $bleeding->$key = $value;
                    $bleedingComment->$key = $data->comments->$key;
                }
            }

            $bleeding->other = json_encode($data->values->others);
            $bleedingComment->other = json_encode($data->comments->others);

            $bleeding->save();

            if ($bleeding->fail()) {
                $response["success"] = false;
                $text = $bleeding->fail()->getMessage();
                $response["message"] = message($text, "error");

                echo json_encode($response);
                return;
            } else {
                $bleedingComment->save();

                if ($bleedingComment->fail()) {
                    $response["success"] = false;
                    $text = $bleeding->fail()->getMessage();
                    $response["message"] = message($text, "error");

                    echo json_encode($response);
                    return;
                }

                $response["success"] = true;
                $text = "A sangria foi salva com sucesso!";
                $response["message"] = message($text, "success");
            }

            echo json_encode($response);
        }
    }