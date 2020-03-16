<?php

    namespace Source\models;
    use CoffeeCode\DataLayer\DataLayer;
    
    class Balance extends DataLayer
    {
        public function __construct()
        {
            parent::__construct("balances", ["shift", "opening", "closing", "user"]);
        }
    }