<?php
namespace Controllers;

use Core\Response;
use Core\Validator;
use Models\UserModel;

class MeController
{
    public static function show($request)
    {
        $user = $request->getUser();
        Response::success(array(
            'id' => $user['id'],
            'email' => $user['email'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'username' => $user['username'],
            'tariff' => $user['tariff'],
            'balance' => $user['balance'],
        ));
    }

    public static function update($request)
    {
        $user = $request->getUser();
        $data = $request->getBody();
        $errors = array();

        $email = isset($data['email']) ? trim($data['email']) : '';
        $firstName = isset($data['first_name']) ? trim($data['first_name']) : '';
        $lastName = isset($data['last_name']) ? trim($data['last_name']) : '';
        $username = isset($data['username']) ? trim($data['username']) : '';

        if (!Validator::required($email) || !Validator::email($email)) {
            $errors['email'] = 'Введите корректный email.';
        }
        if (!Validator::required($firstName)) {
            $errors['first_name'] = 'Введите имя.';
        }
        if (!Validator::required($username) || !preg_match('/^[a-zA-Z0-9_]{3,32}$/', $username)) {
            $errors['username'] = 'Логин должен быть 3-32 символа (латиница, цифры, underscore).';
        }

        $existingEmail = UserModel::findByEmail($email);
        if ($existingEmail && $existingEmail['id'] != $user['id']) {
            $errors['email'] = 'Этот email уже используется.';
        }

        $existingUsername = UserModel::findByUsername($username);
        if ($existingUsername && $existingUsername['id'] != $user['id']) {
            $errors['username'] = 'Этот логин уже занят.';
        }

        if ($errors) {
            Response::error('VALIDATION_ERROR', 'Validation failed', 400, $errors);
        }

        $updated = UserModel::updateProfile($user['id'], array(
            'name' => trim($firstName . ' ' . $lastName),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'username' => $username,
            'email' => $email,
        ));

        Response::success(array(
            'id' => $updated['id'],
            'email' => $updated['email'],
            'first_name' => $updated['first_name'],
            'last_name' => $updated['last_name'],
            'username' => $updated['username'],
            'tariff' => $updated['tariff'],
            'balance' => $updated['balance'],
        ));
    }
}
