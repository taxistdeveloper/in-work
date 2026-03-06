<?php

namespace App\Models;

use Core\Database;

class PageSetting
{
    private Database $db;
    private static array $cache = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all(): array
    {
        return $this->db->fetchAll("SELECT * FROM page_settings ORDER BY id ASC");
    }

    public function isEnabled(string $pageKey): bool
    {
        if (empty(self::$cache)) {
            $this->loadCache();
        }
        return (bool) (self::$cache[$pageKey] ?? true);
    }

    public function toggle(string $pageKey, bool $enabled): void
    {
        $this->db->query(
            "UPDATE page_settings SET is_enabled = ? WHERE page_key = ?",
            [$enabled ? 1 : 0, $pageKey]
        );
        self::$cache = [];
    }

    public function getPageName(string $pageKey): string
    {
        $row = $this->db->fetch("SELECT page_name FROM page_settings WHERE page_key = ?", [$pageKey]);
        return $row['page_name'] ?? $pageKey;
    }

    private function loadCache(): void
    {
        $rows = $this->db->fetchAll("SELECT page_key, is_enabled FROM page_settings");
        foreach ($rows as $row) {
            self::$cache[$row['page_key']] = (int) $row['is_enabled'];
        }
    }
}
