<?php
declare(strict_types=1);

namespace App\DataSource\Player;

use Atlas\Mapper\MapperSelect;

/**
 * @method PlayerRecord|null fetchRecord()
 * @method PlayerRecord[] fetchRecords()
 * @method PlayerRecordSet fetchRecordSet()
 */
class PlayerSelect extends MapperSelect
{
}
