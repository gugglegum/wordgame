<?php
declare(strict_types=1);

namespace App\DataSource\Game;

use Atlas\Mapper\RecordSet;

/**
 * @method GameRecord offsetGet($offset)
 * @method GameRecord appendNew(array $fields = [])
 * @method GameRecord|null getOneBy(array $whereEquals)
 * @method GameRecordSet getAllBy(array $whereEquals)
 * @method GameRecord|null detachOneBy(array $whereEquals)
 * @method GameRecordSet detachAllBy(array $whereEquals)
 * @method GameRecordSet detachAll()
 * @method GameRecordSet detachDeleted()
 */
class GameRecordSet extends RecordSet
{
}
