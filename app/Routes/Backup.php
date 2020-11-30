<?php
namespace App\Routes;

class Backup {
    function __construct($app) {
        $app->post('/backup', '\App\Controllers\BackupController:backup');
        $app->post('/restore', '\App\Controllers\BackupController:restore');
    }
}