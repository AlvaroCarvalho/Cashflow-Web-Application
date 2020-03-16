<?php

    namespace Source\models;
    use CoffeeCode\DataLayer\DataLayer;
    use CoffeeCode\DataLayer\Connect;

    class Entry extends DataLayer
    {
        private $connection;
        public $shift;
        public $next_day;
        public $number;
        public $payment;
        public $action;
        public $amount;
        public $total;
        public $user;

        public function __construct()
        {
            $this->connection = Connect::getInstance();
            parent::__construct("entries", ["shift", "number", "payment", "action", "amount", "total", "user"]);
        }

        public function createNewEntry(): array
        {            
            try {
                $query = $this->connection->prepare("INSERT INTO entries (shift, next_day, number, payment, action, amount, total, user, comment) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $query->execute([$this->shift, $this->next_day, $this->number, $this->payment, $this->action, $this->amount, $this->total, $this->user, $this->comment]);

                return ["success" => true, "id" => $this->connection->lastInsertId()];
            } catch (PDOException $e) {
                return ["success" => false];
            }
        }

        public function findIncomeByNumber(string $number): array
        {            
            try {
                $query = $this->connection->prepare("SELECT i.number, i.shift, i.next_day, i.amount, p.name as payment, a.name as action, i.entry
                                                    FROM incomes i, payments p, actions a
                                                    WHERE i.number = ? AND i.payment = p.id AND i.action = a.id");
                $query->execute([$number]);

                $incomes = $query->fetchAll();

                return ["success" => true, "incomes" => $incomes];
            } catch (PDOException $e) {
                return ["success" => false];
            }
        }
    }