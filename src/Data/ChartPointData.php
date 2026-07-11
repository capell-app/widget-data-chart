<?php

declare(strict_types=1);

namespace Capell\WidgetDataChart\Data;

use Spatie\LaravelData\Data;

final class ChartPointData extends Data
{
    public function __construct(public string $label, public float $value) {}

    /** @return array<string, list<string>> */
    public static function rules(): array
    {
        return ['label' => ['required', 'string', 'max:80'], 'value' => ['required', 'numeric', 'between:-1000000,1000000']];
    }
}
