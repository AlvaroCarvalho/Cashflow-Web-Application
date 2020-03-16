<?php

    namespace Source\models;
    use CoffeeCode\DataLayer\DataLayer;

    class Comment extends DataLayer
    {
        public function __construct()
        {
            parent::__construct("comments", ["shift", "deliveryman", "contact", "comment"]);
        }
    }