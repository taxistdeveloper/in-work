<?php

namespace App\Controllers;

use Core\Controller;
use Core\Validator;
use App\Models\User;

class AuthController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function showRegister(): void
    {
        $this->view('auth.register', [
            'title' => 'Регистрация',
        ]);
    }

    public function register(): void
    {
        $data = $this->allInput();

        if (!$this->validateCsrf()) {
            flash('error', 'Неверный токен безопасности.');
            $this->redirect(url('register'));
            return;
        }

        $validator = new Validator($data);
        $validator
            ->required('name', 'Имя')
            ->minLength('name', 2, 'Имя')
            ->maxLength('name', 100, 'Имя')
            ->required('email', 'Email')
            ->email('email', 'Email')
            ->unique('email', 'users', 'email', 'Email')
            ->required('password', 'Пароль')
            ->minLength('password', 6, 'Пароль')
            ->required('password_confirm', 'Подтверждение пароля')
            ->match('password', 'password_confirm', 'Пароль')
            ->required('role', 'Роль');

        if ($validator->fails()) {
            $_SESSION['old_input'] = $data;
            $_SESSION['errors'] = $validator->firstErrors();
            $this->redirect(url('register'));
            return;
        }

        if (!in_array($data['role'] ?? '', ['client', 'freelancer'])) {
            $data['role'] = 'freelancer';
        }

        $userId = $this->userModel->createUser($data);

        $_SESSION['user'] = $this->userModel->getSessionData($userId);

        flash('success', 'Добро пожаловать в inWork!');
        $this->redirect(url('dashboard'));
    }

    public function showLogin(): void
    {
        $this->view('auth.login', [
            'title' => 'Вход',
        ]);
    }

    public function login(): void
    {
        $data = $this->allInput();

        if (!$this->validateCsrf()) {
            flash('error', 'Неверный токен безопасности.');
            $this->redirect(url('login'));
            return;
        }

        $validator = new Validator($data);
        $validator
            ->required('email', 'Email')
            ->email('email', 'Email')
            ->required('password', 'Пароль');

        if ($validator->fails()) {
            $_SESSION['old_input'] = $data;
            $_SESSION['errors'] = $validator->firstErrors();
            $this->redirect(url('login'));
            return;
        }

        $user = $this->userModel->findByEmail($data['email']);

        if (!$user || !$this->userModel->verifyPassword($data['password'], $user['password'])) {
            $_SESSION['old_input'] = $data;
            flash('error', 'Неверный email или пароль.');
            $this->redirect(url('login'));
            return;
        }

        $_SESSION['user'] = $this->userModel->getSessionData($user['id']);

        flash('success', 'С возвращением!');
        $redirect = ($_SESSION['user']['role'] === 'admin') ? 'admin' : 'dashboard';
        $this->redirect(url($redirect));
    }

    public function logout(): void
    {
        session_destroy();
        header('Location: ' . APP_URL . '/');
        exit;
    }
}
