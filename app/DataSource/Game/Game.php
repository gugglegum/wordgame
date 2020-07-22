<?php
declare(strict_types=1);

namespace App\DataSource\Game;

use Atlas\Mapper\Mapper;
use Atlas\Table\Row;

/**
 * @method GameTable getTable()
 * @method GameRelationships getRelationships()
 * @method GameRecord|null fetchRecord($primaryVal, array $with = [])
 * @method GameRecord|null fetchRecordBy(array $whereEquals, array $with = [])
 * @method GameRecord[] fetchRecords(array $primaryVals, array $with = [])
 * @method GameRecord[] fetchRecordsBy(array $whereEquals, array $with = [])
 * @method GameRecordSet fetchRecordSet(array $primaryVals, array $with = [])
 * @method GameRecordSet fetchRecordSetBy(array $whereEquals, array $with = [])
 * @method GameSelect select(array $whereEquals = [])
 * @method GameRecord newRecord(array $fields = [])
 * @method GameRecord[] newRecords(array $fieldSets)
 * @method GameRecordSet newRecordSet(array $records = [])
 * @method GameRecord turnRowIntoRecord(Row $row, array $with = [])
 * @method GameRecord[] turnRowsIntoRecords(array $rows, array $with = [])
 */
class Game extends Mapper
{
}
