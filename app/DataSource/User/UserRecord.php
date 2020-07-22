<?php
declare(strict_types=1);

namespace App\DataSource\User;

use Atlas\Mapper\Record;

/**
 * @method UserRow getRow()
 */
class UserRecord extends Record
{
    use UserFields;
}
