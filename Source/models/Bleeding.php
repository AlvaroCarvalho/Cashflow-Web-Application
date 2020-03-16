<?php

    namespace Source\models;
    use CoffeeCode\DataLayer\DataLayer;

    class Bleeding extends DataLayer
    {
        public function __construct()
        {
            parent::__construct("bleedings", ["shift", "total"]);
        }
    }