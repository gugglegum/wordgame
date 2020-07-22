<?php
declare(strict_types=1);

namespace App\DataSource\GamesPlayer;

use Atlas\Mapper\RecordSet;

/**
 * @method GamesPlayerRecord offsetGet($offset)
 * @method GamesPlayerRecord appendNew(array $fields = [])
 * @method GamesPlayerRecord|null getOneBy(array $whereEquals)
 * @method GamesPlayerRecordSet getAllBy(array $whereEquals)
 * @method GamesPlayerRecord|null detachOneBy(array $whereEquals)
 * @method GamesPlayerRecordSet detachAllBy(array $whereEquals)
 * @method GamesPlayerRecordSet detachAll()
 * @method GamesPlayerRecordSet detachDeleted()
 */
class GamesPlayerRecordSet extends RecordSet
{
}
