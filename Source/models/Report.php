<?php

    namespace Source\models;
    use CoffeeCode\DataLayer\Connect;

    class Report
    {
        private $connection;

        public function __construct()
        {
            $this->connection = Connect::getInstance();
        }

        private function getDateQuery(object $filter, string $field): string
        {
            if ($filter->from != "" && $filter->to != "") {
                $where = "WHERE $field >= '$filter->from 00:00:00' AND $field <= '$filter->to 23:59:59' AND";
            } else if ($filter->from != "" && $filter->to == "") {
                $where = "WHERE $field >= '$filter->from 00:00:00' AND";
            } else if ($filter->from == "" && $filter->to != "") {
                $where = "WHERE $field <= '$filter->to 23:59:59' AND";
            } else {
                $where = "WHERE";
            }

            return $where;
        }

        private function getDayBefore(object $filter): object
        {
            $dayBefore = new class{
                public $from = "";
                public $to = ""; 
            };

            if ($filter->from) {
                $dayBefore->from = date('Y-m-d', strtotime('-1 day', strtotime($filter->from)));
            }

            if ($filter->to) 
                $dayBefore->to = date('Y-m-d', strtotime('-1 day', strtotime($filter->to)));

            return $dayBefore;
        }

        public function create(array $data)
        {
            $filter = json_decode($data["filter"]);
            $where = $this->getDateQuery($filter, 'created_at');

            if ($filter->shift != "") {
                $shift = "AND e.shift = {$filter->shift}";
            } else {
                $shift = "";
            }

            if ($filter->amount != "") {
                $amount = "AND e.total = {$filter->amount}";
            } else {
                $amount = "";
            }

            if ($filter->payment != "0") {
                $payment = "AND e.payment LIKE '%{$filter->payment}%'";
            } else {
                $payment = "";
            }
            
            $balanceWhere = $this->getDateQuery($filter, 'b.created_at');

            $dataQuery = "SELECT e.id, e.number, e.shift, e.created_at as date, e.next_day, e.payment, e.amount, e.total FROM entries e $where 1=1 $shift $amount $payment";
            $balanceQuery = "SELECT b.shift, b.opening, b.supply, b.credit, b.closing, b.bleeding, b.user, SUM(e.total) as total 
                            FROM balances b, entries e
                            $balanceWhere b.shift = e.shift $shift $amount $payment
                            GROUP BY e.shift";

            $dayBefore = $this->getDayBefore($filter);
            $dateBeforeWhere = $this->getDateQuery($dayBefore, 'created_at');

            $dateBeforeQuery = "SELECT e.payment, e.amount
                                FROM entries e
                                $dateBeforeWhere next_day = 1 $shift $amount $payment";

            $bleedingQuery = "SELECT * FROM bleedings e $where 1=1 $shift";
            $bleedingCommentQuery = "SELECT * FROM bleedings_comments e $where 1=1 $shift";            
        
            try {
                $dataStmt = $this->connection->prepare($dataQuery);
                $dataStmt->execute();
                $incomes = $dataStmt->fetchAll();

                $balanceStmt = $this->connection->prepare($balanceQuery);
                $balanceStmt->execute();
                $balance = $balanceStmt->fetchAll();

                $dayBeforeStmt = $this->connection->prepare($dateBeforeQuery);
                $dayBeforeStmt->execute();
                $dayBeforeResults = $dayBeforeStmt->fetchAll();   

                $bleedingStmt = $this->connection->prepare($bleedingQuery);
                $bleedingStmt->execute();
                $bleedingResults = $bleedingStmt->fetchAll();

                $bleedingCommentStmt = $this->connection->prepare($bleedingCommentQuery);
                $bleedingCommentStmt->execute();
                $bleedingCommentResults = $bleedingCommentStmt->fetchAll();

                return ["incomes" => $incomes, "balance" => $balance, "before" => $dayBeforeResults, "bleedings" => $bleedingResults, "bleeding_comment" => $bleedingCommentResults];
            } catch (PDOException $e) {
                return [];
            }
        }
    }