<?php
/**
 * This file was generated by Atlas. Changes will be overwritten.
 */
declare(strict_types=1);

namespace App\DataSource\User;

use Atlas\Table\Row;

/**
 * @property mixed $id int(10,0) unsigned NOT NULL
 * @property mixed $username varchar(32) NOT NULL
 * @property mixed $password_sha512 varchar(128) NOT NULL
 * @property mixed $password_salt varchar(16) NOT NULL
 * @property mixed $email varchar(100) NOT NULL
 */
class UserRow extends Row
{
    protected $cols = [
        'id' => null,
        'username' => null,
        'password_sha512' => null,
        'password_salt' => null,
        'email' => null,
    ];
}
