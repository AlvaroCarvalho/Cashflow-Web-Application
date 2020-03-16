<?php

    namespace Source\controllers;
    use Source\models\Action as ActionModel;
    
    class Action
    {
        public function create(): void
        {   
            $data = file_get_contents("php://input");
            $data = json_decode($data);

            $action = new ActionModel();

            $action->name = $data->name;

            $action->save();

            if ($action->fail()) {
                $response["success"] = false;
                $text = $action->fail()->getMessage();
                $response["message"] = message($text, "error");
            } else {
                $response["success"] = true;
                $text = "A operação foi cadastrada com sucesso!";
                $response["message"] = message($text, "success");
            }

            echo json_encode($response);
        }

        public function destroy(array $data): void
        {
            $action = (new ActionModel())->findById($data["id"]);
            $action->destroy();

            if ($action->fail()) {
                $response["success"] = false;
            } else {
                $response["success"] = true;
            }

            echo json_encode($response);
        }

        public function update(): void
        {
            $data = json_decode(file_get_contents("php://input"));

            $action = (new ActionModel())->findById($data->id);
            $action->name = $data->name;
            $action->save();

            if ($action->fail()) {
                $response["success"] = false;
                $text = $action->fail()->getMessage();
                $response["message"] = message($text, "error");
            } else {
                $response["success"] = true;
                $response["message"] = message("A operação foi atualizada com sucesso!", "success");
            }

            echo json_encode($response);
        }

        public function updateStatus(array $data): void
        {
            $action = (new ActionModel())->findById($data["id"]);
            $action->status = $data["status"];
            $action->save();

            if ($action->fail()) {
                $response["success"] = false;
            } else {
                $response["success"] = true;
            }

            echo json_encode($response);
        }
    }