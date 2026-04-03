<?php

namespace App\Controllers;

use Core\Controller;
use App\Services\SpecialistListingService;
use App\Models\User;

class CatalogController extends Controller
{
    /** Веб: выбор каталожной категории. */
    public function index(): void
    {
        $app = require ROOT_PATH . '/config/app.php';
        $modes = $app['category_modes'] ?? [];
        $items = [];
        foreach ($app['categories'] as $slug => $label) {
            if (($modes[$slug] ?? '') === 'catalog') {
                $items[] = ['slug' => $slug, 'label' => $label];
            }
        }

        $this->view('catalog.index', [
            'title'              => 'Каталог специалистов',
            'catalog_categories' => $items,
        ]);
    }

    /** Веб: список специалистов в категории. */
    public function specialists(string $category): void
    {
        if (! is_valid_category_slug($category) || category_mode($category) !== 'catalog') {
            flash('error', 'Категория не найдена или недоступна в каталоге.');
            $this->redirect(url('catalog'));
            return;
        }

        $sort = trim((string) $this->input('sort', 'score'));
        $page = max(1, (int) $this->input('page', 1));
        $service = new SpecialistListingService();
        $result = $service->listByCategory($category, $sort, $page, 12);

        $app = require ROOT_PATH . '/config/app.php';
        $label = $app['categories'][$category] ?? $category;

        $this->view('catalog.specialists', [
            'title'           => $label . ' — каталог',
            'category'        => $category,
            'category_label'  => $label,
            'sort'            => $sort,
            'result'          => $result,
        ]);
    }

    /**
     * Веб: форма найма (только заказчик).
     *
     * @param string $category   slug категории
     * @param string $freelancer id исполнителя
     */
    public function hire(string $category, string $freelancer): void
    {
        if (user_role() !== 'client') {
            flash('error', 'Нанимать из каталога могут только заказчики.');
            $this->redirect(url('catalog/' . $category));
            return;
        }

        if (! is_valid_category_slug($category) || category_mode($category) !== 'catalog') {
            flash('error', 'Категория недоступна.');
            $this->redirect(url('catalog'));
            return;
        }

        $fid = (int) $freelancer;
        $userModel = new User();
        $fl = $userModel->getProfile($fid);
        if (! $fl || ($fl['role'] ?? '') !== 'freelancer') {
            flash('error', 'Исполнитель не найден.');
            $this->redirect(url('catalog/' . $category));
            return;
        }

        if (! (new \App\Models\FreelancerCategory())->userHasCategory($fid, $category)) {
            flash('error', 'Исполнитель не работает в этой категории.');
            $this->redirect(url('catalog/' . $category));
            return;
        }

        unset($fl['password']);

        $app = require ROOT_PATH . '/config/app.php';
        $label = $app['categories'][$category] ?? $category;

        $this->view('catalog.hire', [
            'title'            => 'Нанять: ' . $fl['name'],
            'category'         => $category,
            'category_label'   => $label,
            'freelancer'       => $fl,
            'freelancer_id'    => $fid,
        ]);
    }

    /** Публичный список категорий и режимов (для приложения и лендинга). */
    public function apiCategories(): void
    {
        $app = require ROOT_PATH . '/config/app.php';
        $categories = $app['categories'] ?? [];
        $modes = $app['category_modes'] ?? [];

        $out = [];
        foreach ($categories as $slug => $label) {
            $out[] = [
                'slug'  => $slug,
                'label' => $label,
                'mode'  => ($modes[$slug] ?? '') === 'catalog' ? 'catalog' : 'market',
            ];
        }

        $this->jsonSuccess(['categories' => $out]);
    }

    /** Лента специалистов по каталожной категории. */
    public function apiSpecialists(): void
    {
        $category = trim((string) $this->input('category', ''));
        if ($category === '' || ! is_valid_category_slug($category)) {
            $this->jsonError('Укажите корректную категорию', 422, ['category' => 'Обязательное поле'], 'VALIDATION_ERROR');
            return;
        }
        if (category_mode($category) !== 'catalog') {
            $this->jsonError('Категория не в каталожном режиме', 422, [], 'NOT_CATALOG_CATEGORY');
            return;
        }

        $sort = trim((string) $this->input('sort', 'score'));
        $page = max(1, (int) $this->input('page', 1));
        $perPage = min(50, max(1, (int) $this->input('per_page', 20)));

        $service = new SpecialistListingService();
        $result = $service->listByCategory($category, $sort, $page, $perPage);

        $this->jsonSuccess([
            'specialists' => $result['items'],
            'pagination'  => [
                'page'        => $result['page'],
                'per_page'    => $result['per_page'],
                'total'       => $result['total'],
                'total_pages' => $result['total_pages'],
            ],
        ]);
    }
}
