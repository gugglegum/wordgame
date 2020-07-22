<?php
declare(strict_types=1);

namespace App\DataSource\Event;

use Atlas\Mapper\Mapper;
use Atlas\Table\Row;

/**
 * @method EventTable getTable()
 * @method EventRelationships getRelationships()
 * @method EventRecord|null fetchRecord($primaryVal, array $with = [])
 * @method EventRecord|null fetchRecordBy(array $whereEquals, array $with = [])
 * @method EventRecord[] fetchRecords(array $primaryVals, array $with = [])
 * @method EventRecord[] fetchRecordsBy(array $whereEquals, array $with = [])
 * @method EventRecordSet fetchRecordSet(array $primaryVals, array $with = [])
 * @method EventRecordSet fetchRecordSetBy(array $whereEquals, array $with = [])
 * @method EventSelect select(array $whereEquals = [])
 * @method EventRecord newRecord(array $fields = [])
 * @method EventRecord[] newRecords(array $fieldSets)
 * @method EventRecordSet newRecordSet(array $records = [])
 * @method EventRecord turnRowIntoRecord(Row $row, array $with = [])
 * @method EventRecord[] turnRowsIntoRecords(array $rows, array $with = [])
 */
class Event extends Mapper
{
}
