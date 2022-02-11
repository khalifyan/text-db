<?php
namespace app\db\helpers;

class UserHelper
{
    public static function errorCount($user): bool
    {
        if (!empty($user['error_count']) && !empty($user['error_time'])) {
            if ($user['error_count'] >= 3)
            {
                return true;
            }
        }
        return false;
    }

    public static function errorTime($time1, $time2)
    {

        if ( abs($time1 - $time2)/60 >= 5) {
            return true;
        }

        return 300 - abs($time1 - $time2);
    }
}