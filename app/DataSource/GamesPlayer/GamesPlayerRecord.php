<?php
declare(strict_types=1);

namespace App\DataSource\GamesPlayer;

use Atlas\Mapper\Record;

/**
 * @method GamesPlayerRow getRow()
 */
class GamesPlayerRecord extends Record
{
    use GamesPlayerFields;
}
