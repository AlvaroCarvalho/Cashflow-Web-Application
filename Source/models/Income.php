<?php

    namespace Source\models;
    use CoffeeCode\DataLayer\DataLayer;
    use CoffeeCode\DataLayer\Connect;

    class Income extends DataLayer
    {
        private $connection;

        public function __construct()
        {
            $this->connection = Connect::getInstance();
            parent::__construct("incomes", ["number", "shift", "payment", "action", "amount", "entry", "user"]);
        }
    }