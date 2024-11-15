<?php
declare(strict_types=1);

namespace Pixie\Todo4u\Repository;

use mysqli;
use Pixie\Todo4u\Controllers\Controller;

class SignupRepository extends Controller
{
    /**
     * @param mysqli|null $connecting
     * @param string $userName
     * @return bool
     */
    public function registerCheck(?mysqli $connecting, string $userName): bool
    {
        $query = $connecting->prepare("SELECT * FROM users WHERE user_name = ?");
        $query->bind_param("s", $userName);
        $query->execute();
        $result = $query->get_result();
        return $result->num_rows > 0;
    }

    /**
     * @param mysqli|null $connecting
     * @param string $userName
     * @param string $hashedPassword
     * @param int $role
     * @return bool
     */
    public function addAccount(?mysqli $connecting, string $userName, string $hashedPassword, int $role): bool
    {
        $query = $connecting->prepare("INSERT INTO users (user_name, password,role) VALUES (?, ?, ?)");
        $query->bind_param("ssi", $userName, $hashedPassword, $role);
        return $query->execute();
    }
}