<?php
namespace App\Config;

class Config
{
    // Database settings
    public function db()
    {
            return [
                'driver' => 'mysql',
                'host' => 'localhost:3306',
                'database' => 'nenpos',
                'username' => 'root',
                'password' => '',
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix' => '',
            ];
    }
    // Slim settings
    public function slim()
    {
        return [
            'settings' => [
                'determineRouteBeforeAppMiddleware' => false,
                'displayErrorDetails' => true,
                'db' => self::db(),
            ],
        ];
    }
    // Auth settings
    public function auth()
    {
        return [
            'secret' => 'crankedappsbestkeptsecret',
            'expires' => 60, // in minutes
            'hash' => PASSWORD_DEFAULT,
            'jwt' => 'HS256',
        ];
    }
}