<?php
declare(strict_types=1);

namespace App\DataSource\GamesPlayer;

use Atlas\Mapper\Mapper;
use Atlas\Table\Row;

/**
 * @method GamesPlayerTable getTable()
 * @method GamesPlayerRelationships getRelationships()
 * @method GamesPlayerRecord|null fetchRecord($primaryVal, array $with = [])
 * @method GamesPlayerRecord|null fetchRecordBy(array $whereEquals, array $with = [])
 * @method GamesPlayerRecord[] fetchRecords(array $primaryVals, array $with = [])
 * @method GamesPlayerRecord[] fetchRecordsBy(array $whereEquals, array $with = [])
 * @method GamesPlayerRecordSet fetchRecordSet(array $primaryVals, array $with = [])
 * @method GamesPlayerRecordSet fetchRecordSetBy(array $whereEquals, array $with = [])
 * @method GamesPlayerSelect select(array $whereEquals = [])
 * @method GamesPlayerRecord newRecord(array $fields = [])
 * @method GamesPlayerRecord[] newRecords(array $fieldSets)
 * @method GamesPlayerRecordSet newRecordSet(array $records = [])
 * @method GamesPlayerRecord turnRowIntoRecord(Row $row, array $with = [])
 * @method GamesPlayerRecord[] turnRowsIntoRecords(array $rows, array $with = [])
 */
class GamesPlayer extends Mapper
{
}
