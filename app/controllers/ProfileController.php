<?php

namespace App\Controllers;

use Core\Controller;
use Core\Validator;
use App\Models\User;
use App\Models\Review;

class ProfileController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function show(string $id): void
    {
        $profile = $this->userModel->getProfile((int) $id);

        if (!$profile) {
            flash('error', 'Пользователь не найден.');
            $this->redirect(url('orders'));
            return;
        }

        $reviewModel = new Review();
        $page = max(1, (int) ($this->input('page', 1)));
        $reviews = $reviewModel->getUserReviews((int) $id, $page, 10);

        $this->view('profile.show', [
            'title'      => $profile['name'] . ' — Профиль',
            'profile'    => $profile,
            'reviews'    => $reviews['items'],
            'pagination' => $reviews,
        ]);
    }

    public function edit(): void
    {
        $this->requireAuth();

        $profile = $this->userModel->getProfile(user_id());

        $this->view('profile.edit', [
            'title'   => 'Редактировать профиль',
            'profile' => $profile,
        ]);
    }

    public function update(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            flash('error', 'Неверный токен безопасности.');
            $this->redirect(url('profile'));
            return;
        }

        $data = $this->allInput();

        $validator = new Validator($data);
        $validator
            ->required('name', 'Имя')
            ->minLength('name', 2, 'Имя')
            ->maxLength('name', 100, 'Имя');

        if ($validator->fails()) {
            $_SESSION['errors'] = $validator->firstErrors();
            $this->redirect(url('profile'));
            return;
        }

        $updateData = [
            'name' => $data['name'],
            'bio'  => $data['bio'] ?? '',
        ];

        $this->userModel->update(user_id(), $updateData);
        $_SESSION['user'] = $this->userModel->getSessionData(user_id());

        flash('success', 'Профиль обновлён!');
        $this->redirect(url('profile'));
    }
}
