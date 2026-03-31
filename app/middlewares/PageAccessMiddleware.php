<?php

namespace App\Middlewares;

use App\Models\PageSetting;

class PageAccessMiddleware
{
    private string $pageKey;

    public function __construct(string $pageKey)
    {
        $this->pageKey = $pageKey;
    }

    public function handle(): bool
    {
        if (is_admin()) {
            return true;
        }

        $model = new PageSetting();
        if ($model->isEnabled($this->pageKey)) {
            return true;
        }

        $pageName = $model->getPageName($this->pageKey);
        $this->showMaintenancePage($pageName);
        return false;
    }

    private function showMaintenancePage(string $pageName): void
    {
        http_response_code(503);
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if (strpos($uri, '/api/') === 0) {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error',
                'message' => "Раздел \"{$pageName}\" временно недоступен",
                'code' => 'PAGE_DISABLED',
            ], JSON_UNESCAPED_UNICODE);
            return;
        }
        $title = 'В разработке';
        extract(['pageName' => $pageName, 'title' => $title]);

        ob_start();
        require __DIR__ . '/../../views/pages/maintenance.php';
        $content = ob_get_clean();

        require __DIR__ . '/../../views/layouts/app.php';
    }
}
