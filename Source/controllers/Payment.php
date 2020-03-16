<?php

    namespace Source\controllers;
    use Source\models\Payment as PaymentModel;

    class Payment
    {
        public function create(): void
        {
            $data = file_get_contents("php://input");
            $data = json_decode($data);

            $payment = new PaymentModel();

            $payment->name = $data->name;
            $payment->action_id = $data->action_id;
			$payment->position = $data->position;
			$payment->add_to_report = $data->add_to_report;
            $payment->save();

            if ($payment->fail()) {
                $response["success"] = false;
                $text = $payment->fail()->getMessage();
                $response["message"] = message($text, "error");
            } else {
                $response["success"] = true;
                $text = "O pagamento foi cadastrado com sucesso!";
                $response["message"] = message($text, "success");
            }

            echo json_encode($response);
        }

        public function destroy(array $data): void
        {
            $payment = (new PaymentModel())->findById($data["id"]);
            $payment->destroy();

            if ($payment->fail()) {
                $response["success"] = false;
            } else {
                $response["success"] = true;
            }

            echo json_encode($response);
        }

        public function update(): void 
        {
            $data = json_decode(file_get_contents("php://input"));

            foreach ($data as $key => $value) {
                if (($value == "" || $value == "0") && $key != "add_to_report") {
                    $response["success"] = false;
                    $response["message"] = message("Preencha todos os campos!", "error");

                    echo json_encode($response);
                    return;
                }
            }

            $payment = (new PaymentModel())->updatePayment($data);

            if ($payment["success"]) {
                $response["success"] = true;
				$response["message"] = message("O pagamento foi atualizado com sucesso!", "success");
            } else {
				$response["success"] = false;
                $response["message"] = message($payment["message"], "error");
            }

            echo json_encode($response);
        }

        public function updateStatus(array $data): void
        {
            $payment = (new PaymentModel())->findById($data["id"]);
            $payment->status = $data["status"];
            $payment->save();

            if ($payment->fail()) {
                $response["success"] = false;
            } else {
                $response["success"] = true;
            }

            echo json_encode($response);
        }
    }