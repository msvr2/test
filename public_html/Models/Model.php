<?php

class Model {

    public static function setPassword($value)
    {
        return password_hash($value, PASSWORD_BCRYPT);
    }

    public static function cookieHash($user)
    {
        return password_hash($user["password"], PASSWORD_BCRYPT);
    }
}
