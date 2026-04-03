<?php

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function url(string $path = ''): string
{
    return APP_URL . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return ASSET_PATH . '/' . ltrim($path, '/');
}

function csrf_field(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return '<input type="hidden" name="csrf_token" value="' . e($_SESSION['csrf_token']) . '">';
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function old(string $key, string $default = ''): string
{
    return e($_SESSION['old_input'][$key] ?? $default);
}

function flash(string $key, mixed $value = null): mixed
{
    if ($value !== null) {
        $_SESSION['flash'][$key] = $value;
        return null;
    }

    $val = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $val;
}

function is_logged_in(): bool
{
    return isset($_SESSION['user']);
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function user_id(): ?int
{
    return $_SESSION['user']['id'] ?? null;
}

function user_role(): ?string
{
    return $_SESSION['user']['role'] ?? null;
}

function is_admin(): bool
{
    return ($_SESSION['user']['role'] ?? '') === 'admin';
}

function is_page_enabled(string $pageKey): bool
{
    static $model = null;
    if ($model === null) {
        $model = new \App\Models\PageSetting();
    }
    return $model->isEnabled($pageKey);
}

function format_money(float $amount): string
{
    return number_format($amount, 0, '.', ' ') . ' ₸';
}

function time_ago(string $datetime): string
{
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    if ($diff->y > 0) return $diff->y . ' г. назад';
    if ($diff->m > 0) return $diff->m . ' мес. назад';
    if ($diff->d > 0) return $diff->d . ' дн. назад';
    if ($diff->h > 0) return $diff->h . ' ч. назад';
    if ($diff->i > 0) return $diff->i . ' мин. назад';
    return 'только что';
}

function get_rank(int $completedOrders): array
{
    if ($completedOrders >= 200) return ['name' => 'Эксперт', 'color' => 'purple', 'level' => 4];
    if ($completedOrders >= 50)  return ['name' => 'Профи', 'color' => 'blue', 'level' => 3];
    if ($completedOrders >= 10)  return ['name' => 'Опытный', 'color' => 'green', 'level' => 2];
    return ['name' => 'Новичок', 'color' => 'gray', 'level' => 1];
}

function render_stars(float $rating): string
{
    $full = (int) floor($rating);
    $half = ($rating - $full) >= 0.5 ? 1 : 0;
    $empty = 5 - $full - $half;

    $html = '';
    for ($i = 0; $i < $full; $i++) {
        $html .= '<svg class="w-4 h-4 text-yellow-400 inline" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
    }
    if ($half) {
        $html .= '<svg class="w-4 h-4 text-yellow-400 inline" fill="currentColor" viewBox="0 0 20 20" opacity="0.5"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
    }
    for ($i = 0; $i < $empty; $i++) {
        $html .= '<svg class="w-4 h-4 text-gray-300 inline" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
    }
    return $html;
}

function paginate_links(int $currentPage, int $totalPages, string $baseUrl): string
{
    if ($totalPages <= 1) return '';

    $html = '<nav class="flex items-center justify-center gap-1 mt-6">';

    if ($currentPage > 1) {
        $html .= '<a href="' . $baseUrl . '?page=' . ($currentPage - 1) . '" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition">&laquo;</a>';
    }

    $start = max(1, $currentPage - 2);
    $end = min($totalPages, $currentPage + 2);

    for ($i = $start; $i <= $end; $i++) {
        $active = $i === $currentPage
            ? 'bg-emerald-500 text-white border-emerald-500'
            : 'border-gray-200 text-gray-600 hover:bg-gray-50';
        $html .= '<a href="' . $baseUrl . '?page=' . $i . '" class="px-3 py-2 rounded-lg border ' . $active . ' transition">' . $i . '</a>';
    }

    if ($currentPage < $totalPages) {
        $html .= '<a href="' . $baseUrl . '?page=' . ($currentPage + 1) . '" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition">&raquo;</a>';
    }

    $html .= '</nav>';
    return $html;
}

/** @return array<string, string> */
function app_categories(): array
{
    $app = require ROOT_PATH . '/config/app.php';

    return $app['categories'] ?? [];
}

/** market | catalog */
function category_mode(string $categorySlug): string
{
    $app = require ROOT_PATH . '/config/app.php';
    $modes = $app['category_modes'] ?? [];

    return ($modes[$categorySlug] ?? '') === 'catalog' ? 'catalog' : 'market';
}

/** @return list<string> */
function catalog_category_slugs(): array
{
    $app = require ROOT_PATH . '/config/app.php';
    $modes = $app['category_modes'] ?? [];

    return array_keys(array_filter($modes, static fn ($m) => $m === 'catalog'));
}

function is_valid_category_slug(string $slug): bool
{
    return array_key_exists($slug, app_categories());
}
