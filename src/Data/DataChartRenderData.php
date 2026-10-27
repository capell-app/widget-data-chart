<?php

declare(strict_types=1);

namespace Capell\WidgetDataChart\Data;

use Capell\WidgetDataChart\Actions\RenderDataChartSvgAction;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

final class DataChartRenderData extends Data
{
    /** @param DataCollection<int, ChartSeriesData> $series */
    public function __construct(public string $title, public string $type, #[DataCollectionOf(ChartSeriesData::class)] public DataCollection $series) {}

    public function svg(): string
    {
        return RenderDataChartSvgAction::run($this);
    }
}
