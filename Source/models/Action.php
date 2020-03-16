<?php

    namespace Source\models;
    use CoffeeCode\DataLayer\DataLayer;

    class Action extends DataLayer
    {
        public function __construct()
        {
            parent::__construct("actions", ["name"]);
        }
    }