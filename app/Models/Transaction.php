<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'offline_transactions';
    protected $fillable = ['total','transaction_meta','visible','note','payment_id','amount_paid']; // for mass creation
}