<?php
declare(strict_types=1);

namespace Pixie\Todo4u\Repository;

use mysqli;

class TaskRepository
{
    public function __construct(private readonly mysqli $connection)
    {

    }

    /**
     * @param int $id
     * @return array
     */
    public function getByID(int $id): array
    {
        $query = $this->connection->prepare('SELECT * FROM task WHERE id = ?');
        $query->bind_param('i', $id);
        $query->execute();

        return (array)$query->get_result()->fetch_assoc();
    }

    /**
     * @param int $id
     * @param array $task
     * @return bool
     */
    public function update(int $id, array $task): bool
    {
        $query = $this->connection->prepare('UPDATE task SET title = ?, notes = ?, start_date = ?, end_date = ?, status = ?, author = ? WHERE id = ?');
        $query->bind_param(
            'ssssssi',
            $task['title'],
            $task['notes'],
            $task['startDate'],
            $task['endDate'],
            $task['status'],
            $task['author'],
            $id
        );
        return $query->execute() && $query->affected_rows > 0;
    }

    /**
     * @param int $id
     * @param array $task
     * @return bool
     */
    public function add(int $id, array $task): bool
    {
        $query = $this->connection->prepare("INSERT INTO task (title, notes, start_date, end_date, status, author, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $query->bind_param('ssssssi', $task['title'],
            $task['notes'],
            $task['startDate'],
            $task['endDate'],
            $task['status'],
            $task['author'],
            $id
        );
        return $query->execute() && $query->affected_rows > 0;
    }

    /**
     * @param string $extractId
     * @return bool
     */
    public function multiDelete(string $extractId): bool
    {
        $query = $this->connection->prepare("DELETE FROM task WHERE id IN ($extractId)");
        return $query->execute() && $query->affected_rows > 0;
    }

    /**
     * @param int $taskId
     * @return bool
     */
    public function delete(int $taskId): bool
    {
        $query = $this->connection->prepare("DELETE FROM task WHERE id = ?");
        $query->bind_param('i', $taskId);
        return $query->execute() && $query->affected_rows > 0;
    }

    /**
     * @param int $taskId
     * @return bool
     */
    public function complete(int $taskId): bool
    {
        $query = $this->connection->prepare("UPDATE task SET status = 'Completed' WHERE id = ?");
        $query->bind_param('i', $taskId);
        return $query->execute() && $query->affected_rows > 0;
    }

    /**
     * @param string $currentTime
     * @param int $taskId
     * @return bool
     */
    public function updateLastChanged(string $currentTime, int $taskId): bool
    {
        $query = $this->connection->prepare('UPDATE task SET last_changed = ? WHERE id = ?');
        $query->bind_param('si', $currentTime, $taskId);
        return $query->execute() && $query->affected_rows > 0;
    }

    /**
     * @param int $userId
     * @return array
     */
    public function showArchived(int $userId): array
    {
        $query = $this->connection->prepare('SELECT * FROM task WHERE user_id = ? AND archived = 1');
        $query->bind_param('i', $userId);
        $query->execute();
        $result = $query->get_result();

        if ($query->affected_rows > 0)
        {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return[];
    }

    /**
     * @param int $taskId
     * @return bool
     */
    public function updateToArchive(int $taskId): bool
    {
        $query = $this->connection->prepare('UPDATE task SET archived = 1 WHERE id = ?');
        $query->bind_param('i', $taskId);
        return $query->execute() && $query->affected_rows > 0;
    }

    /**
     * @param $extractId
     * @return bool
     */
    public function multiArchive($extractId): bool
    {
        $query = $this->connection->prepare("UPDATE task SET archived = 1 WHERE id IN ($extractId)");
        return $query->execute() && $query->affected_rows > 0;
    }

    /**
     * @param int $userId
     * @param string $searchTitle
     * @return array
     */
    public function titleSearch(int $userId, string $searchTitle): array
    {
        $query = $this->connection->prepare('SELECT * FROM task WHERE user_id = ? AND archived IS NULL AND title LIKE ?');
        $query->bind_param('ss', $userId, $searchTitle);
        $query->execute();
        return $query->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * @param int $userId
     * @param string $searchTitle
     * @param $changedDate
     * @return array
     */
    public function changedDateSearch(int $userId, string $searchTitle, $changedDate): array
    {
        if ($changedDate === 'o/n')
        {
            $query = $this->connection->prepare('SELECT * FROM task WHERE user_id = ? AND title LIKE ? AND archived IS NULL ORDER BY last_changed ASC');
        }
        elseif ($changedDate === 'n/o')
        {
            $query = $this->connection->prepare('SELECT * FROM task WHERE user_id = ? AND title LIKE ? AND archived IS NULL ORDER BY last_changed DESC');
        }

        $query->bind_param('ss',$userId,$searchTitle);
        $query->execute();
        return $query->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * @param int $userId
     * @param string $searchTitle
     * @param $endDate
     * @return array
     */
    public function endDateSearch(int $userId, string $searchTitle, $endDate): array
    {
        if ($endDate === 'o/n')
        {
            $query = $this->connection->prepare('SELECT * FROM task WHERE user_id = ? AND title LIKE ? AND archived IS NULL ORDER BY end_date ASC');
        }
        elseif ($endDate === 'n/o')
        {
            $query = $this->connection->prepare('SELECT * FROM task WHERE user_id = ? AND title LIKE ? AND archived IS NULL ORDER BY end_date DESC');
        }

        $query->bind_param('ss',$userId,$searchTitle);
        $query->execute();
        return $query->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}