<?php
class Validator
{
    private array $data;
    private array $errors = [];

    public function __construct(array $data) { $this->data = $data; }

    public static function make(array $data, array $rules): self
    {
        $v = new self($data);
        foreach ($rules as $field => $ruleStr) {
            foreach (explode('|', $ruleStr) as $rule) {
                $params = [];
                if (str_contains($rule, ':')) {
                    [$rule, $arg] = explode(':', $rule, 2);
                    $params = explode(',', $arg);
                }
                $v->apply($field, $rule, $params);
            }
        }
        return $v;
    }

    private function apply(string $field, string $rule, array $params): void
    {
        $value = $this->data[$field] ?? null;
        switch ($rule) {
            case 'required':
                if ($value === null || $value === '') {
                    $this->errors[$field][] = "El campo $field es obligatorio.";
                }
                break;
            case 'email':
                if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field][] = "El campo $field debe ser un email válido.";
                }
                break;
            case 'min':
                if ($value !== null && mb_strlen((string)$value) < (int)$params[0]) {
                    $this->errors[$field][] = "El campo $field debe tener al menos {$params[0]} caracteres.";
                }
                break;
            case 'max':
                if ($value !== null && mb_strlen((string)$value) > (int)$params[0]) {
                    $this->errors[$field][] = "El campo $field no puede exceder {$params[0]} caracteres.";
                }
                break;
            case 'numeric':
                if ($value !== null && $value !== '' && !is_numeric($value)) {
                    $this->errors[$field][] = "El campo $field debe ser numérico.";
                }
                break;
            case 'date':
                if ($value && !strtotime((string)$value)) {
                    $this->errors[$field][] = "El campo $field debe ser una fecha válida.";
                }
                break;
            case 'in':
                if ($value !== null && $value !== '' && !in_array((string)$value, $params, true)) {
                    $this->errors[$field][] = "Valor no permitido para $field.";
                }
                break;
        }
    }

    public function fails(): bool { return !empty($this->errors); }
    public function errors(): array { return $this->errors; }
    public function firstError(): ?string {
        foreach ($this->errors as $msgs) return $msgs[0];
        return null;
    }
}
