<?php
namespace Middleware;

use Core\Response;

class JsonMiddleware
{
    public function handle($request, $next)
    {
        header('Access-Control-Allow-Origin: ' . config('cors.allowed_origin'));
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

        if ($request->getMethod() === 'OPTIONS') {
            Response::json(array('ok' => true), 204);
        }

        $next();
    }
}
