<?php
namespace App\Routes;

class Products
{
    public function __construct($app)
    {
        $app->get('/products', '\App\Controllers\ProductController:all');
        $app->get('/product/{product_id}', '\App\Controllers\ProductController:find');
    }
}