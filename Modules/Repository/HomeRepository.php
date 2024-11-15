<?php
declare(strict_types=1);

namespace Pixie\Todo4u\Repository;

use Pixie\Todo4u\Controllers\Controller;

class HomeRepository extends Controller
{
    /**
     * @param int $userId
     * @return array
     */
    public function getTasks(int $userId): array
    {
        $conn = $this->connection();
        $query = $conn->prepare("SELECT * FROM task WHERE user_id = ? AND archived IS NULL ORDER BY end_date ASC");
        $query->bind_param('i',$userId);
        $query->execute();
        $result = $query->get_result();

        if($result->num_rows > 0)
        {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }
}