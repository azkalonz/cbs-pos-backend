<?php
namespace App\Controllers;

use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Product;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SalesController
{
    public function all(Request $request, Response $response)
    {
        $sales = Sale::join('payment_type', 'payment_type.id', '=', 'customer_sales_payment.payment_type_id')
            ->groupBy('customer_sales_payment.sales_id')
            ->get();
        return $response->withJson($sales, 200);
    }
    public function find(Request $request, Response $response, $args)
    {
        $id = $args['sales_id'];
        $errors = [];
        if (empty($id)) {
            $errors[] = "missing sales_id";
        } else {
            $sales = SaleDetail::join('products', 'products.product_id', '=', 'sales_detail.product_id')
            ->where('sales_detail.sales_id', '=', $id)
            ->get();
            $customer_sales  = Sale::join('payment_type', 'payment_type.id', '=', 'customer_sales_payment.payment_type_id')
            ->where('customer_sales_payment.sales_id', '=', $sales[0]['sales_id'])
            ->get()
            ->first();
            $customer_sales['sales'] = $sales;
            return $response->withJson($customer_sales, 200);
        }
        return $response->withJson(["errors" => $errors]);
    }
}