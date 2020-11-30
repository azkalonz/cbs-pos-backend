<?php
namespace App\Controllers;

use App\Models\Transaction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Illuminate\Database\Capsule\Manager as DB;

class TransactionController
{
    public function lastOrder(Request $request, Response $response){
        return $response->withJson(Transaction::orderBy('transaction_id','DESC')->get()->first());
    }
    public function get(Request $request, Response $response, $args){
        if(isset($args['transaction_id'])){
            return $response->withJson(Transaction::where("transaction_id",$args['transaction_id'])->where("visible",1)->get()->first());
        } else {
            return $response->withJson(Transaction::where("visible",1)->get()->toArray());
        }
    }
    public function post(Request $request, Response $response){
        $data = $request->getParsedBody();
        $transaction = Transaction::create($data);
        return $response->withJson($transaction->toArray());
    }
    public function delete(Request $request, Response $response,$args){
        if(isset($args['transaction_id'])){
            $transaction = Transaction::where("transaction_id",$args['transaction_id'])->update(['visible'=>false]);
            return $response->withJson([
                "success"=>$transaction?true:false
            ]);
        }
    }
}