<?php
namespace Core;

class Helpers
{
    public static function paginate($page, $limit)
    {
        $page = $page ? max(1, intval($page)) : 1;
        $limit = $limit ? max(1, min(100, intval($limit))) : 20;
        $offset = ($page - 1) * $limit;
        return array($page, $limit, $offset);
    }
}
