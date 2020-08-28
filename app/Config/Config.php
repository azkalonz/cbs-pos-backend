<?php
namespace App\Config;

class Config
{
    // Database settings
    public function db()
    {
        $dev = false;
        if ($dev) {
            return [
                'driver' => 'mysql',
                'host' => 'localhost:3309',
                'database' => 'nenpos',
                'username' => 'root',
                'password' => '4244124',
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix' => '',
            ];
        } else {
            return [
                'driver' => 'mysql',
                'host' => '69.10.40.149',
                'database' => 'cebubake_pos',
                'username' => 'cebubake_mark',
                'password' => '?Lf#{+2Pq+4j',
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix' => '',
            ];
        }
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