<?php

use Core\Auth;
use Core\Validator;
use Models\UserModel;

$redirect = function ($path, $status = 302) {
    header('Location: ' . $path, true, $status);
    exit;
};

$flash = function ($type, $message) {
    $_SESSION['flash'] = array('type' => $type, 'message' => $message);
};

$requireAuth = function () use ($redirect) {
    if (empty($_SESSION['user_id'])) {
        $_SESSION['lastPage'] = $_SERVER['REQUEST_URI'] ?? '/app';
        $redirect('/login');
    }
};

$router->get('/', function () use ($redirect) {
    if (!empty($_SESSION['user_id'])) {
        $redirect('/app');
    }
    $redirect('/login');
});

$router->get('/login', function () use ($redirect) {
    if (!empty($_SESSION['user_id'])) {
        $redirect('/app');
    }
    $flashMessage = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    $flash = $flashMessage;
    include BASE_PATH . '/login.php';
});

$router->get('/register', function () use ($redirect) {
    if (!empty($_SESSION['user_id'])) {
        $redirect('/app');
    }
    $flashMessage = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    $flash = $flashMessage;
    include BASE_PATH . '/register.php';
});

$router->post('/auth/login', function () use ($request, $redirect, $flash) {
    $data = $request->getBody();
    $errors = array();

    $email = isset($data['email']) ? trim($data['email']) : '';
    $password = isset($data['password']) ? $data['password'] : '';

    if (!Validator::required($email) || !Validator::email($email)) {
        $errors['email'] = 'Введите корректный email.';
    }
    if (!Validator::required($password)) {
        $errors['password'] = 'Введите пароль.';
    }

    if ($errors) {
        $flash('error', 'Проверьте корректность введённых данных.');
        $redirect('/login');
    }

    $user = UserModel::findByEmail($email);
    if (!$user || !password_verify($password, $user['password_hash'])) {
        $flash('error', 'Неверный email или пароль.');
        $redirect('/login');
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['auth_token'] = Auth::issueToken($user['id']);

    $target = $_SESSION['lastPage'] ?? '/app';
    unset($_SESSION['lastPage']);

    if (strpos($target, '/app') !== 0) {
        $target = '/app';
    }

    $redirect($target);
});

$router->post('/auth/register', function () use ($request, $redirect, $flash) {
    $data = $request->getBody();
    $errors = array();

    $name = isset($data['name']) ? trim($data['name']) : '';
    $email = isset($data['email']) ? trim($data['email']) : '';
    $password = isset($data['password']) ? $data['password'] : '';

    if (!Validator::required($name)) {
        $errors['name'] = 'Введите имя.';
    }
    if (!Validator::required($email) || !Validator::email($email)) {
        $errors['email'] = 'Введите корректный email.';
    }
    if (!Validator::required($password) || !Validator::minLength($password, 8)) {
        $errors['password'] = 'Пароль должен быть минимум 8 символов.';
    }

    if ($errors) {
        $flash('error', 'Проверьте корректность введённых данных.');
        $redirect('/register');
    }

    if (UserModel::findByEmail($email)) {
        $flash('error', 'Этот email уже зарегистрирован.');
        $redirect('/register');
    }

    $usernameBase = preg_replace('/[^a-z0-9_]/i', '', strtok($email, '@'));
    $usernameBase = $usernameBase ?: 'user';
    $username = UserModel::generateUniqueUsername($usernameBase);

    $hash = password_hash($password, PASSWORD_BCRYPT);
    UserModel::create(array(
        'name' => $name,
        'first_name' => $name,
        'last_name' => '',
        'username' => $username,
        'email' => $email,
        'password_hash' => $hash,
        'tariff' => 'Free',
        'balance' => 0,
    ));

    $flash('success', 'Регистрация успешна, войдите в аккаунт.');
    $redirect('/login');
});

$router->get('/logout', function () use ($redirect) {
    $_SESSION = array();
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'], $params['secure'], $params['httponly']
        );
    }
    session_destroy();
    $redirect('/login');
});

$router->get('/app', function () use ($requireAuth) {
    $requireAuth();
    include BASE_PATH . '/dashboard.php';
});

$router->get('/app/profile', function () use ($requireAuth) {
    $requireAuth();
    include BASE_PATH . '/profile.php';
});

$router->get('/app/dashboard', function () use ($requireAuth) {
    $requireAuth();
    include BASE_PATH . '/dashboard.php';
});

$router->get('/app/pos', function () use ($requireAuth) {
    $requireAuth();
    include BASE_PATH . '/pos.php';
});

$router->get('/app/company', function () use ($requireAuth) {
    $requireAuth();
    include BASE_PATH . '/company.php';
});

$router->get('/app/warehouses', function () use ($requireAuth) {
    $requireAuth();
    include BASE_PATH . '/warehouses.php';
});

$router->get('/app/products', function () use ($requireAuth) {
    $requireAuth();
    include BASE_PATH . '/products.php';
});

$router->get('/app/income', function () use ($requireAuth) {
    $requireAuth();
    include BASE_PATH . '/income.php';
});

$router->get('/app/orders', function () use ($requireAuth) {
    $requireAuth();
    include BASE_PATH . '/orders.php';
});

$router->get('/app/services', function () use ($requireAuth) {
    $requireAuth();
    include BASE_PATH . '/services.php';
});
