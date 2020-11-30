<?php
namespace App\Routes;

class Transaction {
    function __construct($app) {
        $app->get('/transaction', '\App\Controllers\TransactionController:get');
        $app->get('/transaction/{transaction_id}', '\App\Controllers\TransactionController:get');
        $app->post('/transaction', '\App\Controllers\TransactionController:post');
        $app->delete('/transaction/{transaction_id}', '\App\Controllers\TransactionController:delete');
        $app->get('/last-order', '\App\Controllers\TransactionController:lastOrder');
    }
}