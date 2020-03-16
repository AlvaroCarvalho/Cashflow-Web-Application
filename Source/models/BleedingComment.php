<?php

    namespace Source\models;
    use CoffeeCode\DataLayer\DataLayer;

    class BleedingComment extends DataLayer
    {
        public function __construct()
        {
            parent::__construct("bleedings_comments", ["shift"]);
        }
    }