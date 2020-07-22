<?php
declare(strict_types=1);

namespace App\DataSource\Player;

use Atlas\Mapper\Record;

/**
 * @method PlayerRow getRow()
 */
class PlayerRecord extends Record
{
    use PlayerFields;
}
