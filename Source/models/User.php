<?php

    namespace Source\models;
    use CoffeeCode\DataLayer\DataLayer;
    use CoffeeCode\DataLayer\Connect;

    class User extends DataLayer
    {
        private $connection;

        public function __construct()
        {
            $this->connection = Connect::getInstance();
            parent::__construct("users", ["username", "password", "name"]);
        }

        public function get(array $data): array
        {
            $array = array_values($data);

            try{
                $query = $this->connection->prepare("SELECT * FROM `users` WHERE username = ? AND password = ?");
                $query->execute($array);

                $user = $query->fetchAll();
            
                return $user;
            } catch (PDOException $e) {
                return null;
            }
        }
    }