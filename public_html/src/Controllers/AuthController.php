<?php
namespace Controllers;

use Core\Response;
use Core\Validator;
use Core\Auth;
use Models\UserModel;

class AuthController
{
    public static function register($request)
    {
        $data = $request->getBody();
        $errors = array();

        if (!Validator::required(isset($data['name']) ? $data['name'] : null)) {
            $errors['name'] = 'Name is required';
        }
        if (!Validator::required(isset($data['email']) ? $data['email'] : null) || !Validator::email($data['email'])) {
            $errors['email'] = 'Valid email is required';
        }
        if (!Validator::required(isset($data['password']) ? $data['password'] : null) || !Validator::minLength($data['password'], 8)) {
            $errors['password'] = 'Password must be at least 8 characters';
        }

        if ($errors) {
            Response::error('VALIDATION_ERROR', 'Validation failed', 400, $errors);
        }

        if (UserModel::findByEmail($data['email'])) {
            Response::error('CONFLICT', 'Email already registered', 409);
        }

        $hash = password_hash($data['password'], PASSWORD_BCRYPT);
        $usernameBase = preg_replace('/[^a-z0-9_]/i', '', strtok($data['email'], '@'));
        $usernameBase = $usernameBase ? $usernameBase : 'user';
        $userId = UserModel::create(array(
            'name' => $data['name'],
            'first_name' => $data['name'],
            'last_name' => '',
            'username' => UserModel::generateUniqueUsername($usernameBase),
            'email' => $data['email'],
            'password_hash' => $hash,
            'tariff' => 'Free',
            'balance' => 0,
        ));
        $token = Auth::issueToken($userId);
        Response::success(array('token' => $token, 'user' => array('id' => $userId, 'name' => $data['name'], 'email' => $data['email'])), 201);
    }

    public static function login($request)
    {
        $data = $request->getBody();
        $errors = array();

        if (!Validator::required(isset($data['email']) ? $data['email'] : null) || !Validator::email($data['email'])) {
            $errors['email'] = 'Valid email is required';
        }
        if (!Validator::required(isset($data['password']) ? $data['password'] : null)) {
            $errors['password'] = 'Password is required';
        }

        if ($errors) {
            Response::error('VALIDATION_ERROR', 'Validation failed', 400, $errors);
        }

        $user = UserModel::findByEmail($data['email']);
        if (!$user || !password_verify($data['password'], $user['password_hash'])) {
            Response::error('UNAUTHORIZED', 'Invalid credentials', 401);
        }

        $token = Auth::issueToken($user['id']);
        Response::success(array('token' => $token, 'user' => array('id' => $user['id'], 'name' => $user['name'], 'email' => $user['email'])));
    }

    public static function logout($request)
    {
        Response::success(array('message' => 'Logged out'));
    }
}
