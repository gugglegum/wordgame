<?php
declare(strict_types=1);

namespace App\DataSource\Event;

use Atlas\Mapper\Record;

/**
 * @method EventRow getRow()
 */
class EventRecord extends Record
{
    use EventFields;
}
