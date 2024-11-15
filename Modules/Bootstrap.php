<?php
namespace Pixie\Todo4u;

use Kyrill\PhpRoute\Router;
use Pixie\Todo4u\Controllers\LoginController;
use Pixie\Todo4u\Controllers\SignupController;
use Pixie\Todo4u\Controllers\TaskController;

class Bootstrap
{
    public function index(): void
    {
        session_start();

        $router = new Router();
        $router->addRoute('GET','/',[TaskController::class,'index']);

        $router->addRoute('GET','/login',[LoginController::class,'index']);
        $router->addRoute('POST', '/login',[LoginController::class,'login']);
        $router->addRoute('GET','/logout',[LoginController::class,'logout']);

        $router->addRoute('GET','/tasks/sign-up',[SignupController::class,'index']);
        $router->addRoute('POST','/tasks/sign-up',[SignupController::class,'sign_up']);

        $router->addRoute('GET', '/task/delete',[TaskController::class,'delete']);
        $router->addRoute('POST', '/task/delete',[TaskController::class,'delete']);
        $router->addRoute('POST','/tasks',[TaskController::class,'sorting']);
        $router->addRoute('GET', '/tasks/archive',[TaskController::class,'archive']);
        $router->addRoute('GET', '/tasks/archived',[TaskController::class,'addToArchive']);
        $router->addRoute('POST', '/tasks/multipleArchive',[TaskController::class,'addToArchive']);

        $router->addRoute('GET', '/tasks',[TaskController::class,'list']);
        $router->addRoute('GET', '/tasks/add',[TaskController::class,'add']);
        $router->addRoute('POST', '/tasks/add',[TaskController::class,'addTask']);
        $router->addRoute('GET', '/tasks/update',[TaskController::class,'update']);
        $router->addRoute('POST', '/tasks/update',[TaskController::class,'updatePost']);

        $router->addRoute('GET', '/tasks/completed',[TaskController::class,'complete']);

        $router->resolveRoute();
    }
}