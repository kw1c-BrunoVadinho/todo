<?php

namespace Pixie\Todo4u\Controllers;

use mysqli;
use Pixie\Todo4u\Repository\HomeRepository;
use Pixie\Todo4u\Database\Connect;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

class Controller
{
    public const USERNAME_KEY = "Username";
    public const PASSWORD_KEY = "Password";
    public const MESSAGE_TEMPLATE = "/task/message.html.twig";
    public const SIGN_UP_FORM = "sign_up";
    public const LOGIN_KEY = "login";
    public const SEARCH_KEY = "searchtitle";
    public const CHANGED_DATE_KEY = "changedDate";
    public const END_DATE_KEY = "endDate";
    public const USER_KEY = "userID";
    public const ADMIN_KEY = "admin";
    public const SEND_FORM = "send";
    public const TASK_KEY = "taskId";
    public const DELETE_FORM = "Delete";
    public const ARCHIVE_FORM = "Archive";
    public const MULTIPLE_SELECT = "multipleSelect";

    protected Environment $environment;
     public function __construct()
     {
         $loader = new FilesystemLoader(getcwd().'/../Resources/View/templates');
         $this->environment = new Environment($loader,[
             'debug' => false
         ]);
        $this->environment->addExtension(new DebugExtension());
     }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function load(string $template, array $name): void
    {
        echo $this->environment->render($template, $name);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        $userId = $_SESSION['userID'];
        return (new HomeRepository())->getTasks($userId);
    }

    /**
     * @return mysqli
     */
    public function connection(): mysqli
    {
        return (new Connect())->connect();
    }

    /**
     * @param string $error
     * @param string $back
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function showMessage(string $error, string $back): void
    {
        $message = $error;
        $this->load(self::MESSAGE_TEMPLATE, ['message' => $message, 'back' => $back]);
    }
}