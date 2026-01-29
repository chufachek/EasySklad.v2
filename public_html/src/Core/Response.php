<?php
namespace Core;

class Response
{
    public static function json($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public static function success($data = null, $status = 200, $meta = null)
    {
        $payload = array('ok' => true, 'data' => $data);
        if ($meta !== null) {
            $payload['meta'] = $meta;
        }
        self::json($payload, $status);
    }

    public static function error($code, $message, $status = 400, $fields = null)
    {
        $error = array('code' => $code, 'message' => $message);
        if ($fields !== null) {
            $error['fields'] = $fields;
        }
        self::json(array('ok' => false, 'error' => $error), $status);
    }
}
