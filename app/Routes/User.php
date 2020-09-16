<?php
namespace App\Routes;

class User
{
    public function __construct($app)
    {
        $app->post('/users', '\App\Controllers\UserController:create');
        $app->post('/users/login', '\App\Controllers\UserController:login');
        $app->post('/users/auth', '\App\Controllers\UserController:auth');
    }
}