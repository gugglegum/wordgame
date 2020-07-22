<?php
declare(strict_types=1);

namespace App\DataSource\Game;

use Atlas\Mapper\MapperSelect;

/**
 * @method GameRecord|null fetchRecord()
 * @method GameRecord[] fetchRecords()
 * @method GameRecordSet fetchRecordSet()
 */
class GameSelect extends MapperSelect
{
}
