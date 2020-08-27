<?php
namespace App\Controllers;

use App\Includes\ValidationRules as ValidationRules;
use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use \App\Models\User as User;

class UserController
{
    private $logger;
    private $db;
    private $validator;

    private $table;

    // Dependency injection via constructor
    public function __construct($depLogger, $depDB, $depValidator)
    {
        $this->logger = $depLogger;
        $this->db = $depDB;
        $this->validator = $depValidator;
        $this->table = $this->db->table('users');
    }

    // POST /users
    // Create user
    public function create(Request $request, Response $response)
    {
        $this->logger->addInfo('POST /users');
        $data = $request->getParsedBody();
        $errors = [];
        // The validate method returns the validator instance
        $validator = $this->validator->validate($request, ValidationRules::usersPost());
        if (!$validator->isValid()) {
            $errors = $validator->getErrors();
        }
        if (!$errors && User::where(['username' => $data['username']])->first()) {
            $errors[] = 'Username already exists';
        }
        if (!$errors) {
            // Input is valid, so let's do something...
            $newUser = User::create([
                'username' => $data['username'],
                'password' => $data['password'],
            ]);
            return $response->withJson([
                'success' => true,
                'id' => $newUser->id,
            ], 200);
        } else {
            // Error occured
            return $response->withJson([
                'success' => false,
                'errors' => $errors,
            ], 400);
        }
    }

    // POST /users/login
    public function login(Request $request, Response $response)
    {
        $this->logger->addInfo('POST /users/login');
        $data = $request->getParsedBody();
        $errors = [];
        $validator = $this->validator->validate($request, ValidationRules::authPost());
        // Validate input
        if (!$validator->isValid()) {
            $errors = $validator->getErrors();
        }
        // validate username
        if (!$errors && !($user = User::where(['username' => $data['username']])->first())) {
            $errors[] = 'Username invalid';
        }
        // validate password
        if (!$errors && !password_verify($data['password'], $user->password)) {
            $errors[] = 'Password invalid';
        }
        if (!$errors) {
            // No errors, generate JWT
            $token = $user->tokenCreate();
            // return token
            return $response->withJson([
                "success" => true,
                "data" => [
                    "token" => $token['token'],
                    "expires" => $token['expires'],
                ],
            ], 200);
        } else {
            // Error occured
            return $response->withJson([
                'success' => false,
                'errors' => $errors,
            ], 400);
        }
    }
    public function auth(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $key = \App\Config\Config::auth()['secret'];
        $errors = [];
        $jwt = $data['token'];
        if (!empty($jwt)) {
            try {
                $decoded = JWT::decode($jwt, $key, array('HS256'));
                // $user = JWT::toUser($jwt);
                $user = User::find($decoded->sub);
                return $response->withJson([
                    "success" => true,
                    "data" => $decoded,
                    "user" => $user,
                ]);
            } catch (\Exception $e) {
                $errors[] = "Invalid Token";
            }
        } else {
            $errors[] = "token is missing";
        }
        return $response->withJson([
            "errors" => $errors,
        ], 401);
    }
}