<?php

    /* Global settings */

    define("ROOT", "http://localhost/facilpizza");
    define("SITE_NAME", "FacilPizza");

    define("DATA_LAYER_CONFIG", [
        "driver" => "mysql",
        "host" => "localhost",
        "port" => "3306",
        "dbname" => "facilpizza",
        "username" => "root",
        "passwd" => "",
        "options" => [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ]
    ]);

    ini_set('display_errors',1);
    ini_set('display_startup_erros',1);
    error_reporting(E_ALL);

    /*define("ROOT", "https://development.davimanoel.com.br/facilpizza");
    define("SITE_NAME", "FacilPizza");

    define("DATA_LAYER_CONFIG", [
        "driver" => "mysql",
        "host" => "localhost",
        "port" => "3306",
        "dbname" => "davimano_facilpizza",
        "username" => "davimano_alvarocarvalho",
        "passwd" => "@Egdc456fana",
        "options" => [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ]
    ]);

    /* Helpers */

    date_default_timezone_set("America/Sao_Paulo");

    function url(string $uri): string
    {
        if ($uri) {
            return ROOT . "/{$uri}";
        }

        return ROOT;
    }

    function message(string $message, string $type): string
    {
        return "<p class = '$type' id = 'message'>$message</p>";
    }