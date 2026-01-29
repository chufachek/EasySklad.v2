<?php
namespace Controllers;

use Core\Response;

class MeController
{
    public static function show($request)
    {
        Response::success($request->getUser());
    }
}
