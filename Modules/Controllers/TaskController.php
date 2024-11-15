<?php

namespace Pixie\Todo4u\Controllers;

use Pixie\Todo4u\Repository\TaskRepository;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class TaskController extends Controller
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function index(): void
    {
        $this->load('task/index.html.twig',[]);
    }
    /**
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list(): void
    {
        if (isset($_SESSION[self::LOGIN_KEY]) !== true) {
            $this->showMessage('Log eerst in!','/login');
            return;
        }
        $tasks = $this->getData();
        if (!isset($_SESSION[self::ADMIN_KEY])) {
            $this->load('/task/logged.html.twig',['tasks' => $tasks]);
            return;
        }
        $this->load('/task/logged.html.twig',['tasks' => $tasks, 'admin' => true]);
    }

    /**
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function update(): void
    {
        if (isset($_SESSION[self::LOGIN_KEY]) !== true) {
            $this->showMessage('Log eerst in!','/login');
            return;
        }

        $taskID = (int)($_GET[self::TASK_KEY] ?? 0);
        $taskRepository = new TaskRepository($this->connection());
        $task = $taskRepository->getByID($taskID);
        $this->load('task/update.html.twig', ['task' => $task]);
    }

    /**
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function updatePost(): void
    {
        if (isset($_SESSION[self::LOGIN_KEY]) !== true) {
            $this->showMessage('Log eerst in!','/login');
            return;
        }

        $taskID = (int)($_GET[self::TASK_KEY] ?? 0);
        $taskRepository = new TaskRepository($this->connection());
        $update = $taskRepository->update($taskID, [
            'title' => $_POST['title'],
            'notes' => $_POST['notes'],
            'startDate' => $_POST['start_date'],
            'endDate' => $_POST['end_date'],
            'status' => $_POST['status'],
            'author' => $_POST['author'],
        ]);

        $message = 'de task is aangepast';
        if (!$update) {
            $message = 'Er is iets fout gegaan met aanpassen,';
        }
        $this->showMessage($message,'/tasks');
    }

    /**
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function add(): void
    {
        if (isset($_SESSION[self::LOGIN_KEY]) !== true) {
            $this->showMessage('Log eerst in!','/login');
            return;
        }
        $userID = $_SESSION[self::USER_KEY];
        $this->load('task/add.html.twig',['userID' => $userID]);
    }

    /**
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function addTask(): void
    {
        if (isset($_SESSION[self::LOGIN_KEY]) !== true) {
            $this->showMessage('Log eerst in!','/login');
            return;
        }

        $userId = $_SESSION[self::USER_KEY];
        $taskRepository = new TaskRepository($this->connection());
        $add = $taskRepository->add($userId,[
            'title' => $_POST['title'],
            'notes' => $_POST['notes'],
            'startDate' => $_POST['start_date'],
            'endDate' => $_POST['end_date'],
            'status' => $_POST['status'],
            'author' => $_POST['author']
        ]);
        $message = 'Succesvol toegevoegd.';

        if (!$add) {
            $message = 'Er is iets mis gegaan met toevoegen.';
        }
        $this->showMessage($message,'/tasks');
    }

    /**
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function delete(): void
    {
        if (isset($_SESSION[self::LOGIN_KEY]) !== true) {
            $this->showMessage('Log eerst in!','/login');
            return;
        }

        $taskID = (int)($_GET[self::TASK_KEY] ?? 0);
        $taskRepository = new TaskRepository($this->connection());

        if (isset($_POST[self::DELETE_FORM], $_POST[self::MULTIPLE_SELECT])) {
            $this->multipleDelete();
            return;
        }

        $delete = $taskRepository->delete($taskID);
        $message = 'De task is verwijderd.';

        if (!$delete) {
            $message = 'De task is niet verwijderd.';
        }
        $this->showMessage($message,'/tasks');
    }

    /**
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function multipleDelete(): void
    {
        $taskRepository = new TaskRepository($this->connection());
        $allId = $_POST[self::MULTIPLE_SELECT];
        $extractId = implode(',', $allId);
        $multiDelete = $taskRepository->multiDelete($extractId);
        $message = 'De task is verwijderd.';

        if (!$multiDelete)
        {
            $message = 'De task is niet verwijderd.';
        }
        $this->showMessage($message,'/tasks');
    }

    /**
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function complete(): void
    {
        if (isset($_SESSION[self::LOGIN_KEY]) !== true) {
            $this->showMessage('Log eerst in!','/login');
            return;
        }

        $taskID = (int)($_GET[self::TASK_KEY] ?? 0);
        $taskRepository = new TaskRepository($this->connection());
        $complete = $taskRepository->complete($taskID);

        if (!$complete) {
            $this->showMessage('Kan hem niet completen.','/tasks');
            return;
        }

        $currentTime = date('Y-m-d');
        $updatedTime = $taskRepository->updateLastChanged($currentTime, $taskID);

        if ($updatedTime) {
            header('location: /tasks');
        }
    }

    /**
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function archive(): void
    {
        if (isset($_SESSION[self::LOGIN_KEY]) !== true) {
            $this->showMessage('Log eerst in!','/login');
            return;
        }

        $userId = $_SESSION[self::USER_KEY];
        $taskRepository = new TaskRepository($this->connection());
        $show = $taskRepository->showArchived($userId);
        $this->load('task/archive.html.twig', ['tasks' => $show]);
    }

    /**
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function addToArchive(): void
    {
        if (isset($_SESSION[self::LOGIN_KEY]) !== true) {
            $this->showMessage('Log eerst in!','/login');
            return;
        }

        $userId = $_SESSION[self::USER_KEY];
        $taskID = (int)($_GET[self::TASK_KEY] ?? 0);
        $taskRepository = new TaskRepository($this->connection());

        if (isset($_POST[self::ARCHIVE_FORM], $_POST[self::MULTIPLE_SELECT])) {
            $this->multipleArchive();
        }

        $update = $taskRepository->updateToArchive($taskID);

        if($update) {
            $showArchived = $taskRepository->showArchived($userId);
            $this->load('task/archive.html.twig', ['tasks' => $showArchived]);
        }
    }

    /**
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function multipleArchive(): void
    {
        $allId = $_POST[self::MULTIPLE_SELECT];
        $extractId = implode(',', $allId);
        $taskRepository = new TaskRepository($this->connection());
        $multiArchive = $taskRepository->multiArchive($extractId);
        $message = 'De taak is gearchiveerd.';

        if (!$multiArchive)
        {
            $message = 'Er is een fout opgetreden tijdens het archiveren van de taak.';
        }
        $this->showMessage($message,'/tasks');
    }

    /**
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function sorting(): void
    {
        if (isset($_SESSION[self::LOGIN_KEY]) !== true) {
            $this->showMessage('Log eerst in!','/login');
            return;
        }

        $taskRepository = new TaskRepository($this->connection());
        $userId = $_SESSION[self::USER_KEY];
        $searchTitle = $_POST[self::SEARCH_KEY];
        $searchTitle = '%' . $searchTitle . '%';

        if (isset($_POST[self::SEARCH_KEY]) && $_POST[self::CHANGED_DATE_KEY] === 'none' && $_POST[self::END_DATE_KEY] === 'zero') {
            $tasks = $taskRepository->titleSearch($userId, $searchTitle);
            $this->load('task/logged.html.twig', ['tasks' => $tasks]);
            return;
        }
        if (isset($_POST[self::CHANGED_DATE_KEY]) && $_POST[self::END_DATE_KEY] === 'zero') {
            $changedDate = $_POST[self::CHANGED_DATE_KEY];
            $tasks = $taskRepository->changedDateSearch($userId, $searchTitle, $changedDate);
            $this->load('task/logged.html.twig', ['tasks' => $tasks]);
            return;
        }
        if (isset($_POST[self::END_DATE_KEY]) && $_POST[self::CHANGED_DATE_KEY] === 'none') {
            $endDate = $_POST[self::END_DATE_KEY];
            $tasks = $taskRepository->endDateSearch($userId, $searchTitle, $endDate);
            $this->load('task/logged.html.twig', ['tasks' => $tasks]);
        }
    }
}