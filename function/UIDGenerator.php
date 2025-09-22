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

        $dateTimePart = date('Ymd-His');
        $randomChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $randomPart = substr(str_shuffle($randomChars), 0, 4);
        $orderNumber = 'ORD-' . $dateTimePart . '-' . $randomPart;
        if (!preg_match('/^ORD-\d{8}-\d{6}-[A-Z0-9]{4}$/', $orderNumber)) {
            return self::generateOrderNumber();
        }
        return $orderNumber;
    }

    public static function generateProductSKU($attempt = 0)
    {
        if ($attempt >= 5) {
            throw new Exception("Unable to generate valid SKU after 5 attempts.");
        }

        $dateTimePart = date('ymdHis');
        $randomChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $randomPart = substr(str_shuffle($randomChars), 0, 4);
        $rawSku = $dateTimePart . $randomPart;
        $shuffled = substr(str_shuffle($rawSku), 0, 12);

        // Correct format: SKU-XXXX-XXXX-XXXX
        $sku = 'SKU-' . substr($shuffled, 0, 4) . '-' . substr($shuffled, 4, 4) . '-' . substr($shuffled, 8, 4);


        if (!preg_match('/^SKU-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}$/', $sku)) {
            return self::generateProductSKU($attempt + 1);
        }

        return $sku;
    }

    public static function generateTrackingNumber()
    {
        $dateTimePart = date('Ymd-His');
        $randomChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $randomPart = substr(str_shuffle($randomChars), 0, 4);
        $trackingNumber = 'TRK-' . $dateTimePart . '-' . $randomPart;

        if (!preg_match('/^TRK-\d{8}-\d{6}-[A-Z0-9]{4}$/', $trackingNumber)) {
            return self::generateTrackingNumber();
        }

        return $trackingNumber;
    }

    public static function generateTicketId()
    {

        // 20 characters long
        $ticketId = 'TKT-' . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 16);
        return $ticketId;
    }

    public static function generateMessageId()
    {
        $messageId = 'MSG-' . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 16);

        return $messageId;
    }
}
