<?php
/**
 * This file was generated by Atlas. Changes will be overwritten.
 */
declare(strict_types=1);

namespace App\DataSource\GamesPlayer;

use Atlas\Table\Table;

/**
 * @method GamesPlayerRow|null fetchRow($primaryVal)
 * @method GamesPlayerRow[] fetchRows(array $primaryVals)
 * @method GamesPlayerTableSelect select(array $whereEquals = [])
 * @method GamesPlayerRow newRow(array $cols = [])
 * @method GamesPlayerRow newSelectedRow(array $cols)
 */
class GamesPlayerTable extends Table
{
    const DRIVER = 'mysql';

    const NAME = 'games_players';

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
        'player_id' => array (
  'name' => 'player_id',
  'type' => 'int unsigned',
  'size' => 10,
  'scale' => 0,
  'notnull' => true,
  'default' => NULL,
  'autoinc' => false,
  'primary' => false,
  'options' => NULL,
),
        'active' => array (
  'name' => 'active',
  'type' => 'tinyint',
  'size' => 3,
  'scale' => 0,
  'notnull' => true,
  'default' => NULL,
  'autoinc' => false,
  'primary' => false,
  'options' => NULL,
),
    ];

    const COLUMN_NAMES = [
        'id',
        'game_id',
        'player_id',
        'active',
    ];

    const COLUMN_DEFAULTS = [
        'id' => null,
        'game_id' => null,
        'player_id' => null,
        'active' => null,
    ];

    const PRIMARY_KEY = [
        'id',
    ];

    const AUTOINC_COLUMN = 'id';

    const AUTOINC_SEQUENCE = null;
}