<?php

declare(strict_types=1);

namespace App\I18n\En;

use gugglegum\I18n\Translate\EnglishLanguage;

class Auth extends EnglishLanguage
{
    protected static $login_page_title = 'Login';
    protected static $already_logged_please_logout = 'You\'re already logged as user {username}. If you want to login as another user please logout first.';
    protected static $username_label = 'Username';
    protected static $password_label = 'Password';
    protected static $password_again_label = 'Password again';
    protected static $login_button = 'Login';
    protected static $logout_button = 'Logout';
    protected static $register_link = 'Register...';
    protected static $login_link = 'Login...';
    protected static $register_page_title = 'Register';
    protected static $register_button = 'Register';
    protected static $logout_page_title = 'Logout';
    protected static $really_logout = 'Do you really want to logout?';

//    public static function login_page_title()
//    {
//        return 'Auth M';
//    }
}
