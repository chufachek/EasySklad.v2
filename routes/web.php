<?php

use Core\Auth;
use Core\Validator;
use Models\UserModel;

$flash = function ($type, $message) {
    $_SESSION['flash'] = array('type' => $type, 'message' => $message);
};

$requireAuth = function () {
    if (empty($_SESSION['user_id']) && empty($_SESSION['auth_token'])) {
        $_SESSION['lastPage'] = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : base_url('/app');
        redirect('/login');
    }
};

$router->get('/__health', function () {
    header('Content-Type: text/plain; charset=utf-8');
    echo "OK ROUTER\n";
    echo 'uri=' . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '') . "\n";
    echo 'basePath=' . base_path() . "\n";
    echo 'routingMode=' . routing_mode() . "\n";
    echo 'timestamp=' . date('c') . "\n";
});

$router->get('/', function () {
    if (!empty($_SESSION['user_id'])) {
        redirect('/app/dashboard');
    }
    redirect('/login');
});

$router->get('/login', function () {
    if (!empty($_SESSION['user_id'])) {
        redirect('/app');
    }
    $flashMessage = isset($_SESSION['flash']) ? $_SESSION['flash'] : null;
    unset($_SESSION['flash']);
    render('login', array('flash' => $flashMessage));
});

$router->get('/register', function () {
    if (!empty($_SESSION['user_id'])) {
        redirect('/app');
    }
    $flashMessage = isset($_SESSION['flash']) ? $_SESSION['flash'] : null;
    unset($_SESSION['flash']);
    render('register', array('flash' => $flashMessage));
});

$router->post('/auth/login', function () use ($request, $flash) {
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
        redirect('/login');
    }

    $user = UserModel::findByEmail($email);
    if (!$user || !password_verify($password, $user['password_hash'])) {
        $flash('error', 'Неверный email или пароль.');
        redirect('/login');
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['auth_token'] = Auth::issueToken($user['id']);

    $target = isset($_SESSION['lastPage']) ? $_SESSION['lastPage'] : '/app';
    unset($_SESSION['lastPage']);

    if (strpos($target, '/app') !== 0) {
        $target = '/app';
    }

    redirect($target);
});

$router->post('/auth/register', function () use ($request, $flash) {
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
        redirect('/register');
    }

    if (UserModel::findByEmail($email)) {
        $flash('error', 'Этот email уже зарегистрирован.');
        redirect('/register');
    }

    $usernameBase = preg_replace('/[^a-z0-9_]/i', '', strtok($email, '@'));
    $usernameBase = $usernameBase ? $usernameBase : 'user';
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
    redirect('/login');
});

$router->get('/logout', function () {
    $_SESSION = array();
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'], $params['secure'], $params['httponly']
        );
    }
    session_destroy();
    redirect('/login');
});

$router->before('GET|POST', '/app(\/.*)?', function () use ($requireAuth) {
    $requireAuth();
});

$router->get('/app', function () {
    redirect('/app/dashboard');
});

$router->get('/app/dashboard', function () {
    render('dashboard');
});

$router->get('/app/profile', function () {
    render('profile');
});

$router->get('/app/pos', function () {
    render('pos');
});

$router->get('/app/company', function () {
    render('company');
});

$router->get('/app/warehouses', function () {
    render('warehouses');
});

$router->get('/app/products', function () {
    render('products');
});

$router->get('/app/categories', function () {
    render('categories');
});

$router->get('/app/income', function () {
    render('income');
});

$router->get('/app/orders', function () {
    render('orders');
});

$router->get('/app/services', function () {
    render('services');
});
