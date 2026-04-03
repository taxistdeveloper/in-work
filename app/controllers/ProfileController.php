<?php

namespace App\Controllers;

use Core\Controller;
use Core\Validator;
use App\Models\User;
use App\Models\Review;
use App\Models\FreelancerCategory;

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

        $app = require ROOT_PATH . '/config/app.php';
        $catalogSpecs = [];
        foreach ($app['categories'] as $slug => $label) {
            if (($app['category_modes'][$slug] ?? '') === 'catalog') {
                $catalogSpecs[$slug] = $label;
            }
        }

        $this->view('profile.edit', [
            'title'            => 'Редактировать профиль',
            'profile'          => $profile,
            'catalog_specs'    => $catalogSpecs,
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

        if (($this->currentUser()['role'] ?? '') === 'freelancer') {
            $specs = $_POST['specializations'] ?? [];
            if (! is_array($specs)) {
                $specs = [];
            }
            (new FreelancerCategory())->syncForUser(user_id(), array_map('strval', $specs));
        }

        $_SESSION['user'] = $this->userModel->getSessionData(user_id());

        flash('success', 'Профиль обновлён!');
        $this->redirect(url('profile'));
    }

    public function apiShow(string $id): void
    {
        $profile = $this->userModel->getProfile((int) $id);
        if (!$profile) {
            $this->jsonError('Пользователь не найден', 404, [], 'NOT_FOUND');
            return;
        }
        unset($profile['password']);
        $this->jsonSuccess(['profile' => $profile]);
    }

    public function apiMe(): void
    {
        $this->requireAuth();
        $profile = $this->userModel->getProfile(user_id());
        if (!$profile) {
            $this->jsonError('Пользователь не найден', 404, [], 'NOT_FOUND');
            return;
        }
        unset($profile['password']);
        $this->jsonSuccess(['profile' => $profile]);
    }

    public function apiUpdate(): void
    {
        $this->requireAuth();
        $data = $this->allInput();
        $name = trim((string) ($data['name'] ?? ''));
        if (mb_strlen($name) < 2) {
            $this->jsonError('Ошибка валидации', 422, ['name' => 'Имя минимум 2 символа'], 'VALIDATION_ERROR');
            return;
        }
        $updateData = [
            'name' => $name,
            'bio' => (string) ($data['bio'] ?? ''),
        ];
        $this->userModel->update(user_id(), $updateData);

        $specRaw = $data['specializations'] ?? null;
        if (($this->currentUser()['role'] ?? '') === 'freelancer' && $specRaw !== null) {
            $list = is_array($specRaw) ? $specRaw : json_decode((string) $specRaw, true);
            if (! is_array($list)) {
                $list = [];
            }
            $list = array_values(array_filter(array_map('strval', $list)));
            (new FreelancerCategory())->syncForUser(user_id(), $list);
        }

        $_SESSION['user'] = $this->userModel->getSessionData(user_id());
        $profile = $this->userModel->getProfile(user_id());
        unset($profile['password']);
        $this->jsonSuccess(['profile' => $profile], 'Профиль обновлён');
    }
}
