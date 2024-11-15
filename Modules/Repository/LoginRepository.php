<?php
declare(strict_types=1);

namespace Pixie\Todo4u\Repository;

use mysqli;
use Pixie\Todo4u\Controllers\Controller;

class LoginRepository extends Controller
{
    /**
     * @param mysqli $connecting
     * @param string $userName
     * @return array
     */
    public function userCheck(mysqli $connecting, string $userName): array
    {
        $query = $connecting->prepare("SELECT * FROM users WHERE user_name = ?");
        $query->bind_param("s", $userName);
        $query->execute();
        $result = $query->get_result();
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return [];
    }
}




