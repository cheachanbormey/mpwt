<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates the "one day" attendance report.
 */
class AttendanceDayRequest extends FormRequest
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
            'date' => ['nullable', 'date_format:Y-m-d'],
            'status' => ['nullable', 'string', 'in:present,absent,late,leave,holiday,weekend'],
            'q' => ['nullable', 'string', 'max:191'],
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
            'date.date_format' => 'កាលបរិច្ឆេទត្រូវតែជាទម្រង់ YYYY-MM-DD ប៉ុណ្ណោះ។',
            'status.in' => 'ស្ថារាងវត្តមានមិនត្រឹមត្រូវទេ។',
            'q.max' => 'ពាក្យស្វែងរកក្រុងមិនត្រូវលើសពី 191 តួអក្សរទេ។',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'date' => 'កាលបរិច្ឆេទ',
            'status' => 'ស្ថារាង',
            'q' => 'ពាក្យស្វែងរក',
        ];
    }

    /**
     * The day to report on, defaulting to today.
     */
    public function day(): string
    {
        return $this->validated('date') ?: now()->toDateString();
    }
}
