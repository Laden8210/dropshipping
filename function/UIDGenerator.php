<?php
class UIDGenerator
{
    public static function generateUid()
    {
        $dateTimePart = date('ymdHis');
        $randomChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $randomPart = substr(str_shuffle($randomChars), 0, 4); // 4 random alphanumeric

        $rawUid = $dateTimePart . $randomPart;
        $shuffled = substr(str_shuffle($rawUid), 0, 12);


        $uid = substr($shuffled, 0, 4) . '-' . substr($shuffled, 4, 4) . '-' . substr($shuffled, 8, 4);


        if (!preg_match('/^[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}$/', $uid)) {
            return self::generateUid();
        }

        return $uid;
    }

    public static function generateOrderNumber()
    {
        // format ORD-YYYYMMDD-HHMMSS-XXXX
        $dateTimePart = date('Ymd-His');
        $randomChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $randomPart = substr(str_shuffle($randomChars), 0, 4); 
        $orderNumber = 'ORD-' . $dateTimePart . '-' . $randomPart;
        if (!preg_match('/^ORD-\d{8}-\d{6}-[A-Z0-9]{4}$/', $orderNumber)) {
            return self::generateOrderNumber();
        }
        return $orderNumber;

    }
}
