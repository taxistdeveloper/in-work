<?php

namespace Core;

class Validator
{
    private array $errors = [];
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function required(string $field, string $label = ''): self
    {
        $label = $label ?: $field;
        if (!isset($this->data[$field]) || trim((string) $this->data[$field]) === '') {
            $this->errors[$field][] = "{$label} — обязательное поле.";
        }
        return $this;
    }

    public function email(string $field, string $label = ''): self
    {
        $label = $label ?: $field;
        if (!empty($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = "{$label} — укажите корректный email.";
        }
        return $this;
    }

    public function minLength(string $field, int $min, string $label = ''): self
    {
        $label = $label ?: $field;
        if (!empty($this->data[$field]) && mb_strlen($this->data[$field]) < $min) {
            $this->errors[$field][] = "{$label} — минимум {$min} символов.";
        }
        return $this;
    }

    public function maxLength(string $field, int $max, string $label = ''): self
    {
        $label = $label ?: $field;
        if (!empty($this->data[$field]) && mb_strlen($this->data[$field]) > $max) {
            $this->errors[$field][] = "{$label} — максимум {$max} символов.";
        }
        return $this;
    }

    public function numeric(string $field, string $label = ''): self
    {
        $label = $label ?: $field;
        if (!empty($this->data[$field]) && !is_numeric($this->data[$field])) {
            $this->errors[$field][] = "{$label} — должно быть числом.";
        }
        return $this;
    }

    public function min(string $field, float $min, string $label = ''): self
    {
        $label = $label ?: $field;
        if (!empty($this->data[$field]) && (float) $this->data[$field] < $min) {
            $this->errors[$field][] = "{$label} — минимальное значение {$min}.";
        }
        return $this;
    }

    public function match(string $field1, string $field2, string $label = ''): self
    {
        $label = $label ?: $field1;
        if (($this->data[$field1] ?? '') !== ($this->data[$field2] ?? '')) {
            $this->errors[$field1][] = "{$label} — поля не совпадают.";
        }
        return $this;
    }

    public function unique(string $field, string $table, string $column, string $label = '', ?int $excludeId = null): self
    {
        $label = $label ?: $field;
        if (!empty($this->data[$field])) {
            $db = Database::getInstance();
            $sql = "SELECT COUNT(*) as cnt FROM {$table} WHERE {$column} = ?";
            $params = [$this->data[$field]];

            if ($excludeId !== null) {
                $sql .= " AND id != ?";
                $params[] = $excludeId;
            }

            $result = $db->fetch($sql, $params);
            if (($result['cnt'] ?? 0) > 0) {
                $this->errors[$field][] = "{$label} уже используется.";
            }
        }
        return $this;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstErrors(): array
    {
        $first = [];
        foreach ($this->errors as $field => $messages) {
            $first[$field] = $messages[0];
        }
        return $first;
    }
}
