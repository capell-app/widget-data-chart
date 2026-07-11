<?php

declare(strict_types=1);

namespace Capell\WidgetDataChart\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

final class DataChartRenderData extends Data
{
    /** @param DataCollection<int, ChartSeriesData> $series */
    public function __construct(public string $title, public string $type, #[DataCollectionOf(ChartSeriesData::class)] public DataCollection $series) {}

    public function svg(): string
    {
        $points = [];
        $maximum = 1.0;

        foreach ($this->series as $series) {
            foreach ($series->points as $point) {
                $points[] = $point;
                $maximum = max($maximum, abs($point->value));
            }
        }

        $count = max(1, count($points));
        $marks = '';
        foreach ($points as $index => $point) {
            $x = 20 + ($index * (560 / $count));
            $height = abs($point->value) / $maximum * 160;
            $label = htmlspecialchars($point->label, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $pattern = 'url(#pattern-' . ($index % 4) . ')';
            if ($this->type === 'line') {
                $marks .= sprintf('<circle cx="%.2f" cy="%.2f" r="6" class="series-%d"><title>%s: %s</title></circle>', $x, 190 - $height, $index % 8, $label, $point->value);
            } elseif ($this->type === 'donut') {
                $marks .= sprintf('<circle cx="300" cy="110" r="70" fill="none" stroke="%s" stroke-width="28" stroke-dasharray="%.2f 440" stroke-dashoffset="%.2f"><title>%s: %s</title></circle>', $pattern, max(1.0, $height / 160 * 440), -$index * (440 / $count), $label, $point->value);
            } else {
                $marks .= sprintf('<rect x="%.2f" y="%.2f" width="%.2f" height="%.2f" fill="%s"><title>%s: %s</title></rect>', $x, 190 - $height, max(8.0, 500 / $count), $height, $pattern, $label, $point->value);
            }
        }

        return '<svg viewBox="0 0 600 220" role="img" aria-label="' . htmlspecialchars($this->title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '"><defs><pattern id="pattern-0" width="8" height="8" patternUnits="userSpaceOnUse"><path d="M0 8L8 0" stroke="currentColor"/></pattern><pattern id="pattern-1" width="8" height="8" patternUnits="userSpaceOnUse"><path d="M0 0L8 8" stroke="currentColor"/></pattern><pattern id="pattern-2" width="6" height="6" patternUnits="userSpaceOnUse"><circle cx="3" cy="3" r="2" fill="currentColor"/></pattern><pattern id="pattern-3" width="8" height="8" patternUnits="userSpaceOnUse"><path d="M0 4H8" stroke="currentColor"/></pattern></defs>' . $marks . '</svg>';
    }
}
