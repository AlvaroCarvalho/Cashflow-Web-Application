<?php

    namespace Source\controllers;
    use League\Plates\Engine;
    use Source\models\Payment;
    use Source\models\Report as ReportModel;

    class Report
    {
        private $view;

        public function __construct(){
            $this->view = Engine::create(__DIR__ . "/../views", "php");
        }

        public function main(): void
        {
            session_start();
            $payments = (new Payment())->find()->fetch(true);
            echo $this->view->render("reports/report", ["payments" => $payments]);
        }

        public function create(array $data): void
        {
            $report = (new ReportModel())->create($data);

            if ($report) {
                $response["success"] = true;
                $response["data"] = $report;
            } else {
                $response["success"] = false;
            }

            echo json_encode($response);
        }
    }
