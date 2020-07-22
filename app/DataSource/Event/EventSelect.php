<?php
declare(strict_types=1);

namespace App\DataSource\Event;

use Atlas\Mapper\MapperSelect;

/**
 * @method EventRecord|null fetchRecord()
 * @method EventRecord[] fetchRecords()
 * @method EventRecordSet fetchRecordSet()
 */
class EventSelect extends MapperSelect
{
}
