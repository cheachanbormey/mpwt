<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates the "one month" attendance report.
 */
class AttendanceMonthRequest extends FormRequest
{
    public function authorize(): bool
    {
        // The admin middleware on the route has already answered this.
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'month' => ['nullable', 'date_format:Y-m'],
            'office_id' => ['nullable', 'integer', 'exists:offices,id'],
            'position_id' => ['nullable', 'integer', 'exists:positions_v2,id'],
        ];
    }

    /**
     * Khmer wording for each rule.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'month.date_format' => 'ខែត្រូវតែជាទម្រង់ YYYY-MM ប៉ុណ្ណោះ។',
            'office_id.exists' => 'មិនទាន់មានការងារនេះនៅក្នុងប្រព័ន្ធទេ។',
            'position_id.exists' => 'មិនទាន់មានតួនាទីនេះនៅក្នុងប្រព័ន្ធទេ។',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'month' => 'ខែ',
            'office_id' => 'ការងារ',
            'position_id' => 'តួនាទី',
        ];
    }

    /**
     * The month to report on, defaulting to this month.
     */
    public function month(): string
    {
        return $this->validated('month') ?: now()->format('Y-m');
    }
}
