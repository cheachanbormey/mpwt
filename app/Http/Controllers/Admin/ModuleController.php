<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmploymentStatus;
use App\Support\ModulePresenter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Generic CRUD for every module described in config/hrms.php.
 *
 * Writing this once keeps thirteen modules consistent: every module gets
 * the same search box, filters, validation messages and confirmation
 * behaviour. The module registry is the single source of truth.
 *
 * IMPORTANT - leave approval
 * --------------------------
 * Nothing here can approve or reject a leave request. The `status` field of
 * leave_requests is marked readonly in config/hrms.php, so it is excluded
 * from validation and never written. Requests are signed on paper.
 */
class ModuleController extends Controller
{
    /**
     * Rows shown per page.
     */
    private const PER_PAGE = 15;

    /*
    |--------------------------------------------------------------------------
    | Read
    |--------------------------------------------------------------------------
    */

    /**
     * List rows for a module, with search and filters.
     */
    public function index(Request $request): View
    {
        $module = $this->module($request);
        $definition = $this->definition($module);
        $model = $this->modelClass($definition);

        $query = $model::query()->with($this->relations($definition));

        $search = trim((string) $request->input('q', ''));

        if ($search !== '' && ($definition['search'] ?? []) !== []) {
            $query->where(function ($q) use ($definition, $search) {
                foreach ($definition['search'] as $column) {
                    // A dotted column reaches through a relationship, so the
                    // match has to be made inside the related table.
                    if (str_contains($column, '.')) {
                        [$relation, $relatedColumn] = explode('.', $column, 2);

                        $q->orWhereHas(
                            $relation,
                            fn ($related) => $related->where($relatedColumn, 'like', '%'.$search.'%')
                        );

                        continue;
                    }

                    $q->orWhere($column, 'like', '%'.$search.'%');
                }
            });
        }

        $filters = $this->applyFilters($query, $definition, $request);

        $rows = $query->paginate(self::PER_PAGE)->withQueryString();

        return view('admin.module.index', [
            'module' => $module,
            'definition' => $definition,
            'rows' => $rows,
            'search' => $search,
            'filters' => $filters,
            'filterOptions' => $this->filterOptions($definition),
        ]);
    }

    /**
     * Show one row.
     *
     * Only available for modules whose registry entry sets `show` to true,
     * because the leave-request workflow deliberately has no detail page.
     */
    public function show(Request $request, string $id): View
    {
        $module = $this->module($request);
        $definition = $this->definition($module);

        if (! ($definition['show'] ?? false)) {
            abort(404, 'មិនទាន់មានម៉ូឌុលនេះទេ។');
        }

        $model = $this->modelClass($definition);

        $record = $model::query()
            ->with($this->detailRelations($definition))
            ->findOrFail($id);

        return view('admin.module.show', [
            'module' => $module,
            'definition' => $definition,
            'record' => $record,
            'options' => $this->fieldOptions($definition),
            'statusValues' => $this->statusValues($definition),
        ]);
    }

    /**
     * Activate or deactivate a row.
     *
     * The status column is flipped in place rather than being edited through
     * the form, so an administrator can switch a row off without opening the
     * edit page. Only modules whose registry entry names a `status_field`
     * accept this action.
     */
    public function toggle(Request $request, string $id): RedirectResponse
    {
        $module = $this->module($request);
        $definition = $this->definition($module);
        $column = $definition['status_field'] ?? null;

        if (! is_string($column) || $column === '') {
            abort(404, 'មិនទាន់មានម៉ូឌុលនេះទេ។');
        }

        $model = $this->modelClass($definition);

        $record = $model::query()->findOrFail($id);
        $label = $this->rowLabel($definition, $record);

        $values = $this->statusValues($definition);

        $next = $record->getAttribute($column) === $values['active']
            ? $values['inactive']
            : $values['active'];

        $record->update([$column => $next]);

        $this->afterWrite($definition, $record);

        $message = $next === $values['active']
            ? $label.' ត្រូវបានធ្វើឱ្យសកម្មដោយជោគជ័យ។'
            : $label.' ត្រូវបានធ្វើឱ្យអសកម្មដោយជោគជ័ញ។';

        return redirect()
            ->route('admin.'.$module.'.index')
            ->with('success', $message);
    }

    /**
     * Show the create form.
     */
    public function create(Request $request): View
    {
        $definition = $this->definition($this->module($request));

        return view('admin.module.form', [
            'module' => $this->module($request),
            'definition' => $definition,
            'record' => null,
            'options' => $this->fieldOptions($definition),
        ]);
    }

    /**
     * Show the edit form.
     */
    public function edit(Request $request, string $id): View
    {
        $definition = $this->definition($this->module($request));
        $model = $this->modelClass($definition);

        $record = $model::query()->with($this->relations($definition))->findOrFail($id);

        return view('admin.module.form', [
            'module' => $this->module($request),
            'definition' => $definition,
            'record' => $record,
            'options' => $this->fieldOptions($definition),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Write
    |--------------------------------------------------------------------------
    */

    /**
     * Store a new row.
     */
    public function store(Request $request): RedirectResponse
    {
        $module = $this->module($request);
        $definition = $this->definition($module);
        $model = $this->modelClass($definition);

        $data = $request->validate(
            $this->rules($definition),
            $this->messages($definition),
            $this->attributes($definition)
        );

        $record = $model::create($this->payload($definition, $data));

        $this->afterWrite($definition, $record);

        return redirect()
            ->route('admin.'.$module.'.index')
            ->with('success', $definition['singular'].' ត្រូវបានបង្កើតដោយជោគជ័យ។');
    }

    /**
     * Update an existing row.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $module = $this->module($request);
        $definition = $this->definition($module);
        $model = $this->modelClass($definition);

        $record = $model::query()->findOrFail($id);

        $data = $request->validate(
            $this->rules($definition, $record),
            $this->messages($definition),
            $this->attributes($definition)
        );

        $record->update($this->payload($definition, $data));

        $this->afterWrite($definition, $record);

        return redirect()
            ->route('admin.'.$module.'.index')
            ->with('success', $definition['singular'].' ត្រូវបានរក្សាទុកដោយជោគជ័យ។');
    }

    /**
     * Delete a row.
     */
    public function destroy(Request $request, string $id): RedirectResponse
    {
        $module = $this->module($request);
        $definition = $this->definition($module);
        $model = $this->modelClass($definition);

        $record = $model::query()->findOrFail($id);
        $label = $this->rowLabel($definition, $record);

        try {
            $record->delete();
        } catch (QueryException $e) {
            // A RESTRICT foreign key still points at this row.
            return back()->with(
                'error',
                'មិនអាចលុប '.$label.' បានទេ។ មានព័ត៌មានផ្សេងៗទាក់ទងនឹងកំណត់ត្រានេះ។'
            );
        }

        return redirect()
            ->route('admin.'.$module.'.index')
            ->with('success', $label.' ត្រូវបានលុបដោយជោគជ័យ។');
    }

    /*
    |--------------------------------------------------------------------------
    | Registry helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Which module the current request is about.
     *
     * routes/web.php builds one set of routes per module with the name in the
     * URL as a literal, so it is passed along as a route default rather than
     * a {module} parameter. Anything not in the registry is a 404.
     */
    private function module(Request $request): string
    {
        $module = $request->route()?->defaults['module'] ?? null;

        if (! is_string($module) || config("hrms.{$module}") === null) {
            abort(404, 'មិនទាន់មានម៉ូឌុលនេះទេ។');
        }

        return $module;
    }

    /**
     * Fetch a module definition, or fail with 404.
     *
     * @return array<string, mixed>
     */
    private function definition(string $module): array
    {
        $definition = config("hrms.{$module}");

        if (! is_array($definition)) {
            abort(404, 'មិនទាន់មានម៉ូឌុលនេះទេ។');
        }

        return $definition;
    }

    /**
     * The Eloquent class for a module.
     *
     * @return class-string<Model>
     */
    private function modelClass(array $definition): string
    {
        return $definition['model'];
    }

    /**
     * Every module key, ordered for navigation.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function registry(): array
    {
        $modules = config('hrms', []);

        uasort($modules, fn ($a, $b) => ($a['order'] ?? 999) <=> ($b['order'] ?? 999));

        return $modules;
    }

    /*
    |--------------------------------------------------------------------------
    | Query helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Relations referenced by dotted column names, so the list page avoids
     * N+1 queries.
     *
     * @return list<string>
     */
    private function relations(array $definition): array
    {
        $relations = [];

        foreach ($definition['columns'] ?? [] as $column) {
            if (str_contains($column, '.')) {
                $relations[] = Str::before($column, '.');
            }
        }

        return array_values(array_unique($relations));
    }

    /**
     * Apply the module's filters to a query and return the used values.
     *
     * A filter may point at a different column than its own name - the
     * leave-request list uses `from`/`to` to bound `start_date`/`end_date` -
     * and may carry a comparison operator.
     *
     * @return array<string, string>
     */
    private function applyFilters($query, array $definition, Request $request): array
    {
        $used = [];

        foreach ($definition['filters'] ?? [] as $name => $filter) {
            $value = trim((string) $request->input($name, ''));

            if ($value === '') {
                continue;
            }

            $used[$name] = $value;

            $column = $filter['column'] ?? $name;

            if (($filter['type'] ?? null) === 'date') {
                $query->whereDate($column, $filter['operator'] ?? '=', $value);
            } else {
                $query->where($column, $filter['operator'] ?? '=', $value);
            }
        }

        return $used;
    }

    /**
     * Resolve the option lists needed by the filter bar.
     *
     * @return array<string, array<string, string>>
     */
    private function filterOptions(array $definition): array
    {
        $options = [];

        foreach ($definition['filters'] ?? [] as $name => $filter) {
            if (($filter['type'] ?? null) === 'date') {
                continue;
            }

            if (isset($filter['options'])) {
                $options[$name] = $filter['options'];
            } elseif (isset($filter['options_module'])) {
                $options[$name] = $this->optionsFromModule(
                    $filter['options_module'],
                    $filter['option_label'] ?? 'name'
                );
            }
        }

        return $options;
    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    /**
     * Build validation rules for every writable field.
     *
     * @return array<string, array<int, mixed>>
     */
    private function rules(array $definition, ?Model $record = null): array
    {
        $rules = [];
        $model = $this->modelClass($definition);
        $instance = new $model;
        $table = $instance->getTable();

        foreach ($definition['fields'] as $name => $field) {
            // Readonly fields are never validated and never written.
            if ($field['readonly'] ?? false) {
                continue;
            }

            $required = (bool) ($field['required'] ?? false);
            $chain = [];

            switch ($field['type'] ?? 'text') {
                case 'text':
                    $chain = ['string', 'max:'.($field['max'] ?? 191)];
                    break;

                case 'textarea':
                    $chain = ['string', 'max:'.($field['max'] ?? 2000)];
                    break;

                case 'email':
                    $chain = ['email', 'max:191'];
                    break;

                case 'number':
                    $chain = ['integer'];
                    if (isset($field['min'])) {
                        $chain[] = 'min:'.$field['min'];
                    }
                    if (isset($field['max'])) {
                        $chain[] = 'max:'.$field['max'];
                    }
                    break;

                case 'decimal':
                    $chain = ['numeric'];
                    if (isset($field['min'])) {
                        $chain[] = 'min:'.$field['min'];
                    }
                    if (isset($field['max'])) {
                        $chain[] = 'max:'.$field['max'];
                    }
                    break;

                case 'date':
                    $chain = ['date'];
                    break;

                case 'time':
                    $chain = ['date_format:H:i'];
                    break;

                case 'datetime':
                    $chain = ['date'];
                    break;

                case 'boolean':
                    $rules[$name] = ['nullable', 'boolean'];

                    continue 2;

                case 'select':
                    if (isset($field['options'])) {
                        $chain = ['in:'.implode(',', array_keys($field['options']))];
                    } elseif (isset($field['options_module'])) {
                        $optionModel = $this->modelClass(
                            $this->definition($field['options_module'])
                        );
                        $optionInstance = new $optionModel;
                        $chain = [
                            'exists:'.$optionInstance->getTable().','.$optionInstance->getKeyName(),
                        ];
                    }
                    break;
            }

            // Uniqueness, ignoring the row being edited.
            if ($field['unique'] ?? false) {
                $unique = Rule::unique($table, $name);

                if ($record !== null) {
                    $unique->ignore($record->getKey());
                }

                $chain[] = $unique;
            }

            // Required fields must be present and non-empty.
            $rules[$name] = $required
                ? array_merge(['required'], $chain)
                : array_merge(['nullable'], $chain);

            // Cross-field rules.
            if (($field['type'] ?? null) === 'date' && ($field['after'] ?? null) !== null) {
                $rules[$name][] = 'after_or_equal:'.$field['after'];
            }
        }

        return $rules;
    }

    /**
     * Attribute names for validation messages, in Khmer.
     *
     * @return array<string, string>
     */
    private function attributes(array $definition): array
    {
        $attributes = [];

        foreach ($definition['fields'] as $name => $field) {
            $attributes[$name] = $field['label'] ?? $name;
        }

        return $attributes;
    }

    /**
     * Messages for the failures that are easy to hit.
     *
     * @return array<string, string>
     */
    private function messages(array $definition): array
    {
        $messages = [];

        foreach ($definition['fields'] as $name => $field) {
            $label = $field['label'] ?? $name;

            $messages["{$name}.required"] = "សូមបញ្ចូល{$label}។";
            $messages["{$name}.unique"] = "{$label}នេះត្រូវបានប្រើរួចហើយ។";
            $messages["{$name}.exists"] = "{$label}មិនមាននៅក្នុងប្រព័ន្ធ។";
            $messages["{$name}.in"] = "{$label}មិនត្រឹមត្រូវ។";
            $messages["{$name}.after_or_equal"] = "{$label}ត្រូវមានយ៉ាងតិចតាមវាលមុនមក។";
        }

        return $messages;
    }

    /*
    |--------------------------------------------------------------------------
    | Persistence
    |--------------------------------------------------------------------------
    */

    /**
     * Reduce validated input to the fields the module actually stores.
     *
     * Only keys declared in the registry are kept, so a crafted request
     * cannot write `id`, `created_at` or anything else.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function payload(array $definition, array $data): array
    {
        $payload = [];

        foreach ($definition['fields'] as $name => $field) {
            // Readonly: skip entirely. This is what blocks approval.
            if ($field['readonly'] ?? false) {
                continue;
            }

            if (($field['type'] ?? null) === 'boolean') {
                // An unchecked checkbox is simply absent from the request.
                $payload[$name] = (int) (bool) ($data[$name] ?? false);

                continue;
            }

            if (array_key_exists($name, $data)) {
                $payload[$name] = $data[$name] === '' ? null : $data[$name];
            } elseif (isset($field['default'])) {
                $payload[$name] = $field['default'];
            }
        }

        return $payload;
    }

    /**
     * Side effects after a row is written.
     */
    private function afterWrite(array $definition, Model $record): void
    {
        // The employment status list is cached per request.
        if ($record instanceof EmploymentStatus) {
            EmploymentStatus::flushCache();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Options
    |--------------------------------------------------------------------------
    */

    /**
     * Resolve every dropdown on a form.
     *
     * @return array<string, array<int|string, string>>
     */
    private function fieldOptions(array $definition): array
    {
        $options = [];

        foreach ($definition['fields'] as $name => $field) {
            if (isset($field['options'])) {
                $options[$name] = $field['options'];
            } elseif (isset($field['options_module'])) {
                $options[$name] = $this->optionsFromModule(
                    $field['options_module'],
                    $field['option_label'] ?? 'name'
                );
            }
        }

        return $options;
    }

    /**
     * Build "id => label" pairs from another module.
     *
     * A source may be named either by its registry key or by its model class,
     * so a table can feed a dropdown before it gains a management screen of
     * its own.
     *
     * @return array<int, string>
     */
    private function optionsFromModule(string $module, string $labelColumn): array
    {
        $model = class_exists($module) ? $module : $this->modelClass($this->definition($module));

        return $model::query()
            ->orderBy($labelColumn)
            ->pluck($labelColumn, 'id')
            ->all();
    }

    /*
    |--------------------------------------------------------------------------
    | Display
    |--------------------------------------------------------------------------
    */

    /**
     * Extra relations a detail page needs, declared per module.
     *
     * The list page only eager-loads relations that appear as dotted column
     * names. A Show page may want more - the department page lists its
     * subordinate offices - so modules declare them under `detail_relations`.
     *
     * @return list<string>
     */
    private function detailRelations(array $definition): array
    {
        return array_values(array_unique([
            ...$this->relations($definition),
            ...array_keys($definition['detail_relations'] ?? []),
        ]));
    }

    /**
     * The two values a status column toggles between.
     *
     * @return array{active: string, inactive: string}
     */
    private function statusValues(array $definition): array
    {
        return [
            'active' => $definition['status_values']['active'] ?? 'active',
            'inactive' => $definition['status_values']['inactive'] ?? 'inactive',
        ];
    }

    /**
     * A short label for flash messages.
     */
    private function rowLabel(array $definition, Model $record): string
    {
        return ModulePresenter::rowLabel($record, $definition);
    }
}
