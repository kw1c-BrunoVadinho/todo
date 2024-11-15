<?php

namespace Pixie\Todo4u\Controllers;

use Pixie\Todo4u\Repository\SignupRepository;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class SignupController extends Controller
{
    /**
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index(): void
    {
        if (isset($_SESSION['admin']) === true) {
            $this->load('/sign_up.html.twig',[]);
            return;
        }

        $this->showMessage('Jij hebt de rechten niet om hier te zijn', '/login');
    }

    /**
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function sign_up(): void
    {
        $userName = $_POST[self::USERNAME_KEY];
        $password = $_POST[self::PASSWORD_KEY];
        $role = $_POST['Role'];

        if(!isset($_POST[self::SIGN_UP_FORM])) {
            $this->load('/sign_up.html.twig', []);
        }

        $signupRepository = new SignupRepository();
        $registrationExists = $signupRepository->registerCheck($this->connection(), $userName);

        if (!$registrationExists) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $registrationSuccess = $signupRepository->addAccount($this->connection(), $userName, $hashedPassword, $role);

            if ($registrationSuccess) {
                $this->showMessage('Registratie succesvol!','/tasks');
                return;
            }
        }
        $this->showMessage('De gebruikersnaam bestaat al. Kies alstublieft een andere gebruikersnaam.','/tasks/sign-up');
    }
}