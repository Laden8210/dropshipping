<?php

class Request
{
    public function getBody()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return $_GET;
        }

        $data = file_get_contents('php://input');
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (strpos($contentType, 'application/json') !== false) {
            return json_decode($data, true);
        } else if (strpos($contentType, 'application/x-www-form-urlencoded') !== false) {
            parse_str($data, $result);
            return $result;
        } else if (strpos($contentType, 'multipart/form-data') !== false) {
            return $_POST;
        }

        return [];
    }
}
