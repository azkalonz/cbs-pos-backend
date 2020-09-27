<?php
namespace App\Controllers;

use App\Models\Product;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ProductController
{
    public function all(Request $request, Response $response)
    {
        $products = Product::join('product_prices', 'product_prices.product_id', '=', 'products.product_id')
            ->groupBy('products.product_id')
            ->get();
        if (!empty($_GET['search'])) {
            $products = Product::where('product_name', 'LIKE', '%' . $_GET['search'] . '%')->get();
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
            ->get();
            return $response->withJson($product, 200);
        }
        return $response->withJson(["errors" => $errors]);
    }
}