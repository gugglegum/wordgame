<?php
declare(strict_types=1);

namespace App\DataSource\Event;

use Atlas\Mapper\RecordSet;

/**
 * @method EventRecord offsetGet($offset)
 * @method EventRecord appendNew(array $fields = [])
 * @method EventRecord|null getOneBy(array $whereEquals)
 * @method EventRecordSet getAllBy(array $whereEquals)
 * @method EventRecord|null detachOneBy(array $whereEquals)
 * @method EventRecordSet detachAllBy(array $whereEquals)
 * @method EventRecordSet detachAll()
 * @method EventRecordSet detachDeleted()
 */
class EventRecordSet extends RecordSet
{
}
