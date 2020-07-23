<?php

declare(strict_types=1);

namespace App\I18n\En;

use gugglegum\I18n\Translate\EnglishLanguage;

class Profile extends EnglishLanguage
{
    // profile
    protected static $profile_page_title = 'User {username}';
    protected static $games_you_are_playing_title = 'Games you are playing';
    protected static $words_count = '{count} words';
//    protected static function words_count(array $values)
//    {
//        return $values['count'] . ' ' . self::plural($values['count'], 'word', 'words');
//    }
}
