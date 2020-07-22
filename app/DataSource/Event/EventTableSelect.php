<?php
declare(strict_types=1);

namespace App\DataSource\Event;

use Atlas\Table\TableSelect;

/**
 * @method EventRow|null fetchRow()
 * @method EventRow[] fetchRows()
 */
class EventTableSelect extends TableSelect
{
}
