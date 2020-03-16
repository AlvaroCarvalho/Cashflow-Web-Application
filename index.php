<?php

require __DIR__ . '/vendor/autoload.php';

use CoffeeCode\Router\Router;

$router = new Router(ROOT);

/*
    Controllers
*/

$router->namespace('Source\controllers');

/*
    Login
*/

$router->group(null);
$router->get("/", "Login:login");
$router->post("/login", "Login:validate");
$router->get("/logout", "App:logout");

/*
    Application
*/

$router->group("app");
$router->get("/", "App:main");

    /* Pages */

    $router->get("/usuarios", "User:main");
    $router->get("/cadastros", "App:register");
    $router->get("/lancamentos", "Entry:main");
    $router->get("/relatorios", "Report:main");
    $router->get("/operacoes", "Operation:main");
    
    /* Users */

    $router->post("/users/create", "User:create");
    $router->delete("/users/delete/{id}", "User:destroy");
    $router->put("/users/update", "User:update");
    $router->put("/users/{id}/status/{status}", "User:updateStatus");

    /* Registers */

    $router->post("/cadastros/create_action", "Action:create");
    $router->post("/cadastros/create_payment", "Payment:create");
    $router->delete("/cadastros/destroy_action/{id}", "Action:destroy");
    $router->delete("/cadastros/destroy_payment/{id}", "Payment:destroy");
    $router->put("/cadastros/update_action", "Action:update");
    $router->put("/cadastros/update_payment", "Payment:update");
    $router->put("/cadastros/{id}/update_action_status/{status}", "Action:updateStatus");
    $router->put("/cadastros/{id}/update_payment_status/{status}", "Payment:updateStatus");

    /* Entries */

    $router->post("/lancamentos/create", "Entry:create");
    $router->get("/lancamentos/get/{number}", "Entry:get");

    /* Operations */

    $router->post("/operacoes/save_balance", "Operation:saveBalance");
    $router->post("/operacoes/save_comment", "Operation:saveComment");
    $router->post("/operacoes/save_bleeding", "Operation:saveBleeding");

    /* Reports */

    $router->get("/relatorios/reports/{filter}", "Report:create");

/*
    Erros
*/


/* 
    Dispatch
*/

$router->dispatch();

if($router->error()){
    echo $router->error();
}