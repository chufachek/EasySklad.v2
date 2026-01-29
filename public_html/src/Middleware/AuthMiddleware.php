<?php
namespace Middleware;

use Core\Auth;
use Core\Response;
use Models\UserModel;

class AuthMiddleware
{
    public function handle($request, $next)
    {
        $header = $request->getHeader('Authorization');
        if ($header && strpos($header, 'Bearer ') === 0) {
            $token = trim(substr($header, 7));
            $payload = Auth::verify($token);
            if (!$payload || !isset($payload['sub'])) {
                Response::error('UNAUTHORIZED', 'Invalid token', 401);
            }
            $user = UserModel::findById($payload['sub']);
        } elseif (isset($_SESSION['user_id'])) {
            $user = UserModel::findById($_SESSION['user_id']);
        } else {
            Response::error('UNAUTHORIZED', 'Authorization token required', 401);
        }
        if (!$user) {
            Response::error('UNAUTHORIZED', 'User not found', 401);
        }
        $request->setUser($user);
        $next();
    }
}
