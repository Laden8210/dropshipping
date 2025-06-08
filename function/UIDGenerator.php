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
}
