<?php

declare(strict_types=1);

namespace App\I18n\Ru;

use gugglegum\I18n\Translate\RussianLanguage;

class Auth extends RussianLanguage
{
    protected static $login_page_title = 'Авторизация';
    protected static $already_logged_please_logout = 'Вы уже вошли под пользователем {username}. Если вы хотите войти под другим пользователем, сначала выйдите.';
    protected static $username_label = 'Пользователь';
    protected static $password_label = 'Пароль';
    protected static $password_again_label = 'Пароль ещё раз';
    protected static $login_button = 'Войти';
    protected static $logout_button = 'Выйти';
    protected static $register_link = 'Регистрация';
    protected static $login_link = 'Вход';
    protected static $register_page_title = 'Регистрация';
    protected static $register_button = 'Зарегистрироваться';
    protected static $logout_page_title = 'Выход';
    protected static $really_logout = 'Вы действительно хотите выйти?';
    protected static $main_page_link = 'Главная страница';
    protected static $profile_page_link = 'Профиль';

    //    public static function login_page_title()
//    {
//        return '';
//    }
}
