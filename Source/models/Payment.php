<?php

    namespace Source\models;
    use CoffeeCode\DataLayer\DataLayer;
    use CoffeeCode\DataLayer\Connect;

    class Payment extends DataLayer
    {
        private $connection;

        public function __construct()
        {
            parent::__construct("payments", ["name", "action_id", "position"]);
        }

        public function getAll(): array
        {
            $this->connection = Connect::getInstance();

            try {
                $query = $this->connection->prepare("SELECT payments.id, payments.position, payments.name, payments.status, actions.name as action_name
                                                    FROM payments, actions
                                                    WHERE
                                                    payments.action_id = actions.id
                                                   ");

                $query->execute();
                $payments = $query->fetchAll();

                return $payments;
            } catch (PDOExpception $e) {
                return ["message" => $e];
            }
        }

        public function getPaymentById(int $id): array
        {
            $this->connection = Connect::getInstance();

            try {
                $query = $this->connection->prepare("SELECT * FROM payments WHERE id = ?");
                $query->execute([$id]);
                $payments = $query->fetchAll();

                return $payments;
            } catch (PDOExpception $e) {
                return ["message" => $e];
            }
        }

        public function updatePayment($data): array
        {
            $this->connection = Connect::getInstance();

            try {
                $query = $this->connection->prepare("UPDATE payments SET name = ?, action_id = ?, position = ?, add_to_report = ?, updated_at=CURRENT_TIMESTAMP WHERE id = ? ");
                $query->execute([$data->name, $data->action_id, $data->position, $data->add_to_report, $data->id]);

                return ["success" => true];
            } catch (PDOExpception $e) {
                return ["success" => false, "message" => $e];
            }
        }
    }