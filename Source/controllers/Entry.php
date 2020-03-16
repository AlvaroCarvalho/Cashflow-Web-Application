<?php

    namespace Source\controllers;
    use League\Plates\Engine;
    use Source\models\Action;
    use Source\models\Payment;
    use Source\models\Entry as EntryModel;
    use Source\models\Income;
    
    class Entry
    {
        private $view;

        public function __construct()
        {
            $this->view = Engine::create(__DIR__ . "/../views", "php");
        }

        public function main(): void
        {
            session_start();
            
            $payments = (new Payment())->find()->fetch(true);
            $actions = (new Action())->find()->fetch(true);

            echo $this->view->render("entries/entry", ["actions" => $actions, "payments" => $payments]);
        }

        public function create(): void
        {
            session_start();
            $data = json_decode(file_get_contents("php://input"));
            $entries = $data[0];
            $total = $data[1];
            $comment = $data[2];
            $user = $_SESSION["user"]->id;

            $entry = new EntryModel();
            $entry->shift = $entries[0]->shift;
            $entry->next_day = $entries[0]->nextDay;
            $entry->number = $entries[0]->number;
            $entry->total = $total;
            $entry->user = $user;
            $entry->comment = $comment;
            $payments = [];
            $actions = [];
            $amounts = [];          

            foreach ($entries as $item) {
                array_push($payments, $item->paymentId);
                array_push($actions, $item->actionId);
                array_push($amounts, $item->amount);
            }

            $entry->payment = json_encode($payments);
            $entry->action = json_encode($actions);
            $entry->amount = json_encode($amounts);
            
            $create = $entry->createNewEntry();

            if ($create["success"]) { 
                foreach ($entries as $item) {
                    $income = new Income();
                    $income->shift = $item->shift;
                    $income->number = $item->number;
                    $income->next_day = $item->nextDay;
                    $income->payment = $item->paymentId;
                    $income->action = $item->actionId;
                    $income->amount = $item->amount;
                    $income->entry = $create["id"];
                    $income->user = $user;
                    $income->save();

                    if ($income->fail()) {
                        $response["success"] = false;
                        $response["message"] = message($income->fail()->getMessage(), "error");

                        echo json_encode($response);
                        return;
                    } else {
                        $response["success"] = true;
                        $text = "O lançamento foi criado com sucesso!";
                        $response["message"] = message($text, "success");
                    }
                }

            } else {
                $response["success"] = false;
                $text = "Não foi possível criar o lançamento. Por favor, tente novamente.";
                $response["message"] = message($text, "error");
            }

            echo json_encode($response);
        }

        public function get(array $data): void
        {
            $entry = (new EntryModel())->findIncomeByNumber($data["number"]);

            if ($entry && !empty($entry)) {
                echo json_encode($entry);
            } else {
                echo json_encode([]);
            }
        }
    }