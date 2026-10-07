<?php

namespace App\Support;

use App\Models\EmploymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Turns a row and a column name into something readable.
 *
 * The generic admin list renders every module through this class, so
 * booleans become badges, dates get a Khmer format and select values are
 * replaced with their labels instead of raw codes.
 */
class ModulePresenter
{
    /**
     * Fixed labels for columns that are backed by a lookup table.
     *
     * @var array<string, array<string, string>>
     */
    private static array $labelSets = [];

    /**
     * Column definitions ready for the table header.
     *
     * @param  array<string, mixed>  $definition
     * @return list<array{key: string, label: string, type: string}>
     */
    public static function columns(array $definition): array
    {
        $fields = $definition['fields'] ?? [];
        $columns = [];

        foreach ($definition['columns'] ?? [] as $key) {
            $fieldName = str_contains($key, '.') ? Str::afterLast($key, '.') : $key;

            $columns[] = [
                'key' => $key,
                'label' => $fields[$fieldName]['label'] ?? self::humanize($fieldName),
                'type' => $fields[$fieldName]['type'] ?? 'text',
            ];
        }

        return $columns;
    }

    /**
     * Render one cell.
     *
     * @param  array<string, mixed>  $definition
     * @return array{text: string, kind: string}
     */
    public static function cell(Model $row, string $column, array $definition): array
    {
        $value = data_get($row, $column);

        if ($value === null || $value === '') {
            return ['text' => '—', 'kind' => 'empty'];
        }

        $fieldName = str_contains($column, '.') ? Str::afterLast($column, '.') : $column;
        $field = $definition['fields'][$fieldName] ?? null;
        $type = $field['type'] ?? 'text';

        // Checkbox columns.
        if ($type === 'boolean') {
            return $value
                ? ['text' => 'បាទ', 'kind' => 'yes']
                : ['text' => 'អត់', 'kind' => 'no'];
        }

        // The module's declared status column becomes a coloured badge.
        if (($definition['status_field'] ?? null) === $column) {
            $labels = $field['options'] ?? [];

            $text = $labels[(string) $value] ?? (string) $value;

            return [
                'text' => $text,
                'kind' => (string) $value === ($definition['status_values']['inactive'] ?? 'inactive')
                    ? 'inactive'
                    : 'active',
            ];
        }

        // Fixed option lists become their Khmer label.
        $labels = self::labels($column, $definition);

        if ($labels !== [] && array_key_exists((string) $value, $labels)) {
            return ['text' => $labels[(string) $value], 'kind' => 'label'];
        }

        return match ($type) {
            'date' => ['text' => self::date($value), 'kind' => 'date'],
            'datetime' => ['text' => self::dateTime($value), 'kind' => 'date'],
            'time' => ['text' => substr((string) $value, 0, 5), 'kind' => 'time'],
            'decimal' => ['text' => number_format((float) $value, 2), 'kind' => 'number'],
            'number' => ['text' => number_format((float) $value, 0, '.', ''), 'kind' => 'number'],
            default => ['text' => (string) $value, 'kind' => 'text'],
        };
    }

    /**
     * Bootstrap contextual class for a cell.
     *
     * @param  array<string, mixed>  $definition
     */
    public static function cellClass(string $column, array $definition, string $kind): string
    {
        $fieldName = str_contains($column, '.') ? Str::afterLast($column, '.') : $column;
        $type = $definition['fields'][$fieldName]['type'] ?? 'text';

        if ($type === 'boolean') {
            return $kind === 'yes' ? 'text-success fw-semibold' : 'text-body-secondary';
        }

        return match ($kind) {
            'empty' => 'text-body-tertiary',
            'number' => 'text-end font-monospace',
            'date', 'time' => 'font-monospace',
            default => '',
        };
    }

    /**
     * The Bootstrap badge class for a status cell, or null when it is plain
     * text.
     *
     * Boolean columns are deliberately absent: those keep the softer
     * .badge-status markup that the staff pages already use.
     *
     * @param  array<string, mixed>  $definition
     */
    public static function badgeClass(string $column, array $definition, string $kind): ?string
    {
        return match ($kind) {
            'active' => 'text-bg-success',
            'inactive' => 'text-bg-secondary',
            default => null,
        };
    }

    /**
     * Labels available for a column, if any.
     *
     * @param  array<string, mixed>  $definition
     * @return array<string, string>
     */
    private static function labels(string $column, array $definition): array
    {
        $fieldName = str_contains($column, '.') ? Str::afterLast($column, '.') : $column;
        $field = $definition['fields'][$fieldName] ?? null;

        if ($field === null) {
            // Employment states come from a lookup table, not the field list.
            return $fieldName === 'employment_status'
                ? (self::$labelSets['employment_status'] ??= EmploymentStatus::labels())
                : [];
        }

        return $field['options'] ?? [];
    }

    /**
     * A short, human label for a row, used in flash messages and page titles.
     *
     * Falls back through the naming conventions used across the modules, so
     * `ផ្ទាំងការងារបន្ទាន់ប្រឡង់` wins over a bare code.
     *
     * @param  array<string, mixed>  $definition
     */
    public static function rowLabel(Model $row, array $definition): string
    {
        $candidates = [
            'name_km', 'name', 'title', 'code', 'staff_code',
            'dep_kh_name', 'dep_code', 'office_name', 'office_code',
        ];

        foreach ($candidates as $candidate) {
            $value = $row->getAttribute($candidate);

            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        return ($definition['singular'] ?? 'កំណត់ត្រា').' #'.$row->getKey();
    }

    /**
     * Format a date.
     */
    private static function date(mixed $value): string
    {
        return Carbon::parse($value)->translatedFormat('d/m/Y');
    }

    /**
     * Format a date and time.
     */
    private static function dateTime(mixed $value): string
    {
        return Carbon::parse($value)->translatedFormat('d/m/Y H:i');
    }

    /**
     * Turn a column name into a readable label.
     */
    private static function humanize(string $name): string
    {
        return Str::headline($name);
    }
}
