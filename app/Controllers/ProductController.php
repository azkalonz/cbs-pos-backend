<?php
namespace App\Controllers;

use App\Models\Product;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Illuminate\Database\Capsule\Manager as DB;

class ProductController
{
    public function all(Request $request, Response $response)
    {
        $products = DB::select("SELECT * FROM products 
        INNER JOIN product_cost_history 
        ON products.product_id = product_cost_history.product_id 
        WHERE products.invisible != 1
        AND products.product_status_id = 1
        AND product_cost_history.date_time = (
            SELECT MAX(date_time)
            FROM product_cost_history
            WHERE product_id = products.product_id
          )
        ORDER BY products.product_name DESC");
        // $products = Product::join('product_prices', 'product_prices.product_id', '=', 'products.product_id')
        //     ->where('products.invisible', '!=', '1')
        //     ->where('products.product_status_id', '=', '1')
        //     ->groupBy('products.product_id')
        //     ->orderBy('products.product_name', 'ASC')
        //     ->get();
        if (!empty($_GET['search'])) {
            $products = Product::where('product_name', 'LIKE', '%' . $_GET['search'] . '%')
                        ->where('products.invisible','!=','1')
                        ->where('products.product_status_id','=','1')->orderBy('products.product_name', 'ASC')
                        ->get();
        }
        return $response->withJson($products, 200);
    }
    public function find(Request $request, Response $response, $args)
    {
        $id = $args['product_id'];
        $errors = [];
        if (empty($id)) {
            $errors[] = "missing product_id";
        } else {
            $product = Product::join('product_prices', 'product_prices.product_id', '=', 'products.product_id')
                ->where('products.product_id', '=', $id)
                ->where('products.invisible','!=','1')
                ->where('products.product_status_id','=','1')->orderBy('products.product_name', 'ASC')
                ->get();
            return $response->withJson($product[0], 200);
        }
        return $response->withJson(["errors" => $errors]);
    }
    public function productCost(Request $request, Response $response, $args)
    {
        $id = $args['product_id'];
        $errors = [];
        if (empty($id)) {
            $errors[] = "missing product_id";
        } else {
            // $product = Product::join('product_cost_history', 'product_cost_history.product_id', '=', 'products.product_id')
            //     ->where('products.product_id', '=', $id)
            //     ->get();
            $product = Product::join('product_cost_history', 'product_cost_history.product_id', '=', 'products.product_id')
            ->where('products.product_id', '=', $id)
            ->where('products.invisible','!=','1')
            ->where('products.product_status_id','=','1')->orderBy('products.product_name', 'ASC')
            ->get();
            return $response->withJson($product, 200);
        }
        return $response->withJson(["errors" => $errors]);
    }
}