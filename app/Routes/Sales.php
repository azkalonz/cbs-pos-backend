<?php
namespace App\Routes;

class Sales
{
    public function __construct($app)
    {
        $app->get('/sales', '\App\Controllers\SalesController:all');
        $app->get('/sales/{sales_id}', '\App\Controllers\SalesController:find');
    }
}