<?php

namespace Pixie\Todo4u\Controllers;

use Pixie\Todo4u\Repository\LoginRepository;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class LoginController extends Controller
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function index(): void
    {
        $this->load('/login.html.twig', []);
    }

    /**
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function login(): void
    {
        if (!isset($_POST[self::SEND_FORM])) {
            $this->showMessage("Je hebt niks ingevuld", '/login');
            return;
        }
        $userName = $_POST[self::USERNAME_KEY];
        $passwordLogin = $_POST[self::PASSWORD_KEY];
        $loginRepository = new LoginRepository();
        $userData = $loginRepository->userCheck($this->connection(), $userName);

        if (!$userData) {
            $this->showMessage('Je gebruikersnaam bestaat niet.', '/login');
            return;
        }

        if (!password_verify($passwordLogin, $userData['password'])) {
            $this->showMessage('Je wachtwoord is onjuist.', '/login');
            return;
        }

        $_SESSION[self::USER_KEY] = $userData['id'];
        $_SESSION[self::LOGIN_KEY] = true;
        $adminCheck = $this->adminCheck($userData);
        $this->showMessage('Login succesvol!', '/login');

        if (!$adminCheck) {
            return;
        }
    }

    /**
     * @param array $userData
     * @return bool
     */
    public function adminCheck(array $userData):bool
    {
        if ($userData['role'] === 1) {
            return $_SESSION[self::ADMIN_KEY] = true;
        }
        return false;
    }

    /**
     * @return void
     */
    public function logout(): void
    {
        session_destroy();
        header('Location: /login');
    }
}