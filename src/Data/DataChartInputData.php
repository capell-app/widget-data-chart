<?php

declare(strict_types=1);

namespace Capell\WidgetDataChart\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

final class DataChartInputData extends Data
{
    /** @param DataCollection<int, ChartSeriesData> $series */
    public function __construct(public string $title, public string $type, #[DataCollectionOf(ChartSeriesData::class)] public DataCollection $series) {}

    /** @return array<string, list<string>> */
    public static function rules(): array
    {
        return ['title' => ['required', 'string', 'max:120'], 'type' => ['required', 'in:bar,line,donut'], 'series' => ['required', 'array', 'min:1', 'max:8']];
    }
}
