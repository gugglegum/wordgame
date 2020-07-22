<?php

declare(strict_types=1);

namespace App\Services;

use App\DataSource\User\User;
use App\DataSource\User\UserRecord;
use App\Exceptions\Exception;

/**
 * UserService
 *
 * Creates new user
 *
 * @package App\Services
 */
class UserService
{
    /**
     * @var \Atlas\Orm\Atlas
     */
    private $atlas;

    public function __construct(\Atlas\Orm\Atlas $atlas)
    {
        $this->atlas = $atlas;
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $email
     * @return mixed
     * @throws \Exception
     */
    public function createUser(string $username, string $password, string $email)
    {
        $salt = self::generateSalt();
        /** @var UserRecord $user */
        $user = $this->atlas->newRecord(User::class, [
            'username' => $username,
            'password_sha512' => self::getPasswordSha512($password, $salt),
            'password_salt' => $salt,
            'email' => $email,
        ]);
        $this->atlas->insert($user);
        return $user->id;
    }

    /**
     * @param string $username
     * @param string $password
     * @return UserRecord|null
     */
    public function authorizeUser(string $username, string $password): ?UserRecord
    {
        // If username is valid e-mail address - use e-mail column as username
        if (filter_var($username, FILTER_VALIDATE_EMAIL) !== false) {
            $user = $this->atlas
                ->select(User::class)
                ->where('email = ', $username)
                ->fetchRecord();
        } else {
            $user = $this->atlas
                ->select(User::class)
                ->where('username = ', $username)
                ->fetchRecord();
        }
        if ($user instanceof UserRecord) {
            if ($user->password_sha512 === self::getPasswordSha512($password, $user->password_salt)) {
                return $user;
            }
        }
        return null;
    }

    /**
     * @param string $password
     * @param string $salt
     * @return string
     */
    private static function getPasswordSha512(string $password, string $salt): string
    {
        return hash('sha512', $password . $salt);
    }

    /**
     * @return string
     * @throws Exception
     */
    private static function generateSalt(): string
    {
        static $alphabet;
        if (!$alphabet) {
            $alphabet = array_merge(
                range('a', 'z'),
                range('A', 'Z'),
                range('0', '9'),
                ['`', '~', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '_', '-', '=', '+',
                    '.', ',',':', ';', '"', '\'', '[', ']', '{', '}', '/', '\\', '|', '?', '<', '>']
            );
        }
        $salt = '';
        try {
            for ($i = 0; $i < 16; $i++) {
                $salt .= $alphabet[random_int(0, count($alphabet) - 1)];
            }
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
        return $salt;
    }
}
