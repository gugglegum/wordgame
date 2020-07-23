<?php

declare(strict_types=1);

namespace App\I18n\Ru;

use gugglegum\I18n\Translate\RussianLanguage;

class Profile extends RussianLanguage
{
    // profile
    protected static $profile_page_title = 'Пользователь {username}';
    protected static $games_you_are_playing_title = 'Игры, в которых вы участвуете';
    protected static $words_count = '{count} слов';
//    protected static function words_count(array $values)
//    {
//        return $values['count'] . ' ' . self::plural($values['count'], 'слово', 'слова', 'слов');
//    }

}
