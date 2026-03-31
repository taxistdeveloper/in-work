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
        if ($this->isApiRequest()) {
            session_destroy();
            $this->jsonSuccess([], 'Вы вышли из аккаунта');
            return;
        }
        session_destroy();
        header('Location: ' . APP_URL . '/');
        exit;
    }

    public function apiLogin(): void
    {
        $data = $this->allInput();
        $email = trim((string) ($data['email'] ?? ''));
        $password = (string) ($data['password'] ?? '');

        if ($email === '' || $password === '') {
            $this->jsonError('Email и пароль обязательны', 422, [
                'email' => 'Укажите email',
                'password' => 'Укажите пароль',
            ], 'VALIDATION_ERROR');
            return;
        }

        $user = $this->userModel->findByEmail($email);
        if (!$user || !$this->userModel->verifyPassword($password, $user['password'])) {
            $this->jsonError('Неверный email или пароль', 401, [], 'INVALID_CREDENTIALS');
            return;
        }

        $_SESSION['user'] = $this->userModel->getSessionData((int) $user['id']);
        $this->jsonSuccess(['user' => $_SESSION['user']], 'Авторизация успешна');
    }

    public function apiRegister(): void
    {
        $data = $this->allInput();
        $name = trim((string) ($data['name'] ?? ''));
        $email = trim((string) ($data['email'] ?? ''));
        $password = (string) ($data['password'] ?? '');
        $passwordConfirm = (string) ($data['password_confirm'] ?? '');
        $role = (string) ($data['role'] ?? 'freelancer');

        $errors = [];
        if (mb_strlen($name) < 2) $errors['name'] = 'Имя минимум 2 символа';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Некорректный email';
        if (mb_strlen($password) < 6) $errors['password'] = 'Пароль минимум 6 символов';
        if ($password !== $passwordConfirm) $errors['password_confirm'] = 'Пароли не совпадают';
        if (!in_array($role, ['client', 'freelancer'], true)) $errors['role'] = 'Некорректная роль';
        if ($this->userModel->findByEmail($email)) $errors['email'] = 'Email уже занят';

        if ($errors !== []) {
            $this->jsonError('Проверьте поля формы', 422, $errors, 'VALIDATION_ERROR');
            return;
        }

        $userId = $this->userModel->createUser([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => $role,
        ]);
        $_SESSION['user'] = $this->userModel->getSessionData($userId);
        $this->jsonSuccess(['user' => $_SESSION['user']], 'Регистрация успешна', 201);
    }

    public function apiMe(): void
    {
        if (!$this->isAuthenticated()) {
            $this->jsonError('Необходима авторизация', 401, [], 'UNAUTHORIZED');
            return;
        }
        $this->jsonSuccess(['user' => $this->currentUser()]);
    }
}
