<?php

declare(strict_types=1);

namespace Capell\WidgetDataChart\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

final class ChartSeriesData extends Data
{
    /** @param DataCollection<int, ChartPointData> $points */
    public function __construct(public string $label, #[DataCollectionOf(ChartPointData::class)] public DataCollection $points) {}

    /** @return array<string, list<string>> */
    public static function rules(): array
    {
        return ['label' => ['required', 'string', 'max:80'], 'points' => ['required', 'array', 'min:1', 'max:50']];
    }
}
