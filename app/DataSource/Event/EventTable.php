<?php
/**
 * This file was generated by Atlas. Changes will be overwritten.
 */
declare(strict_types=1);

namespace App\DataSource\Event;

use Atlas\Table\Table;

/**
 * @method EventRow|null fetchRow($primaryVal)
 * @method EventRow[] fetchRows(array $primaryVals)
 * @method EventTableSelect select(array $whereEquals = [])
 * @method EventRow newRow(array $cols = [])
 * @method EventRow newSelectedRow(array $cols)
 */
class EventTable extends Table
{
    const DRIVER = 'mysql';

    const NAME = 'events';

    const COLUMNS = [
        'id' => array (
  'name' => 'id',
  'type' => 'int unsigned',
  'size' => 10,
  'scale' => 0,
  'notnull' => true,
  'default' => NULL,
  'autoinc' => true,
  'primary' => true,
  'options' => NULL,
),
        'game_id' => array (
  'name' => 'game_id',
  'type' => 'int unsigned',
  'size' => 10,
  'scale' => 0,
  'notnull' => true,
  'default' => NULL,
  'autoinc' => false,
  'primary' => false,
  'options' => NULL,
),
        'type' => array (
  'name' => 'type',
  'type' => 'enum',
  'size' => 5,
  'scale' => NULL,
  'notnull' => true,
  'default' => NULL,
  'autoinc' => false,
  'primary' => false,
  'options' => 
  array (
    0 => '\'move\'',
    1 => '\'join\'',
    2 => '\'leave\'',
  ),
),
        'user_id' => array (
  'name' => 'user_id',
  'type' => 'int unsigned',
  'size' => 10,
  'scale' => 0,
  'notnull' => false,
  'default' => 0,
  'autoinc' => false,
  'primary' => false,
  'options' => NULL,
),
        'word' => array (
  'name' => 'word',
  'type' => 'varchar',
  'size' => 50,
  'scale' => NULL,
  'notnull' => false,
  'default' => 'NULL',
  'autoinc' => false,
  'primary' => false,
  'options' => NULL,
),
    ];

    const COLUMN_NAMES = [
        'id',
        'game_id',
        'type',
        'user_id',
        'word',
    ];

    const COLUMN_DEFAULTS = [
        'id' => null,
        'game_id' => null,
        'type' => null,
        'user_id' => 0,
        'word' => 'NULL',
    ];

    const PRIMARY_KEY = [
        'id',
    ];

    const AUTOINC_COLUMN = 'id';

    const AUTOINC_SEQUENCE = null;
}
