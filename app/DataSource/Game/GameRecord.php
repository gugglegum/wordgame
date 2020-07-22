<?php
declare(strict_types=1);

namespace App\DataSource\Game;

use Atlas\Mapper\Record;

/**
 * @method GameRow getRow()
 */
class GameRecord extends Record
{
    use GameFields;
}
