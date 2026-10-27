<?php

declare(strict_types=1);

namespace Capell\WidgetDataChart\Actions;

use Capell\WidgetDataChart\Data\DataChartRenderData;
use Lorisleiva\Actions\Concerns\AsFake;
use Lorisleiva\Actions\Concerns\AsObject;

final class RenderDataChartSvgAction
{
    use AsFake;
    use AsObject;

    public function handle(DataChartRenderData $data): string
    {
        $minimum = 0.0;
        $maximum = 0.0;
        $categories = [];
        foreach ($data->series as $series) {
            $occurrences = [];
            foreach ($series->points as $point) {
                $minimum = min($minimum, $point->value);
                $maximum = max($maximum, $point->value);
                $occurrence = $occurrences[$point->label] ?? 0;
                $occurrences[$point->label] = $occurrence + 1;
                $category = ['label' => $point->label, 'occurrence' => $occurrence];
                if (! in_array($category, $categories, true)) {
                    $categories[] = $category;
                }
            }
        }

        if ($data->type === 'donut' && $minimum < 0) {
            return '';
        }

        $range = $maximum - $minimum;
        if ($range === 0.0) {
            $range = 1.0;
        }

        $y = static fn (float $value): float => 190 - (($value - $minimum) / $range * 160);
        $zero = $y(0);
        $step = 520 / max(1, count($categories));
        $seriesCount = max(1, $data->series->count());
        $marks = '';
        foreach (array_values($data->series->items()) as $seriesIndex => $series) {
            $name = $this->escape($series->label);
            $marks .= '<g aria-label="' . $name . '"><title>' . $name . '</title>';
            $coordinates = [];
            $total = 0.0;
            foreach ($series->points as $point) {
                $total += $point->value;
            }

            $offset = 0.0;
            $occurrences = [];
            foreach (array_values($series->points->items()) as $pointIndex => $point) {
                $occurrence = $occurrences[$point->label] ?? 0;
                $occurrences[$point->label] = $occurrence + 1;
                $categoryIndex = array_search(['label' => $point->label, 'occurrence' => $occurrence], $categories, true);
                $x = 40 + ((is_int($categoryIndex) ? $categoryIndex : 0) + 0.5) * $step;
                $pointY = $y($point->value);
                $title = '<title>' . $name . ' — ' . $this->escape($point->label) . ': ' . $point->value . '</title>';
                if ($data->type === 'donut') {
                    if ($point->value <= 0 || $total <= 0) {
                        continue;
                    }

                    $share = $point->value / $total * 100;
                    $cx = 150 + ($seriesIndex % 2) * 300;
                    $cy = 110 + intdiv($seriesIndex, 2) * 220;
                    $marks .= sprintf('<circle cx="%d" cy="%d" r="70" pathLength="100" fill="none" stroke="url(#pattern-%d)" stroke-width="28" stroke-dasharray="%.6F %.6F" stroke-dashoffset="%.6F" transform="rotate(-90 %d %d)">%s</circle>', $cx, $cy, $pointIndex % 4, $share, 100 - $share, -$offset, $cx, $cy, $title);
                    $offset += $share;
                } elseif ($data->type === 'line') {
                    $coordinates[] = sprintf('%.2F,%.2F', $x, $pointY);
                    $marks .= sprintf('<circle cx="%.2F" cy="%.2F" r="5" fill="currentColor">%s</circle>', $x, $pointY, $title);
                } else {
                    $width = $step * 0.8 / $seriesCount;
                    $barX = $x - $step * 0.4 + $seriesIndex * $width;
                    $marks .= sprintf('<rect x="%.2F" y="%.2F" width="%.2F" height="%.2F" fill="url(#pattern-%d)">%s</rect>', $barX, min($zero, $pointY), $width, abs($pointY - $zero), $seriesIndex % 4, $title);
                }
            }

            if ($data->type === 'line') {
                $marks .= '<polyline points="' . implode(' ', $coordinates) . '" fill="none" stroke="currentColor" stroke-width="2" stroke-dasharray="' . (2 + $seriesIndex * 2) . ' 3" />';
            }

            if ($data->type !== 'donut' && $seriesCount > 1) {
                $legendY = 240 + $seriesIndex * 24;
                $marks .= $data->type === 'line'
                    ? sprintf('<line x1="40" x2="65" y1="%d" y2="%d" stroke="currentColor" stroke-width="2" stroke-dasharray="%d 3"/>', $legendY - 4, $legendY - 4, 2 + $seriesIndex * 2)
                    : sprintf('<rect x="40" y="%d" width="25" height="16" fill="url(#pattern-%d)"/>', $legendY - 16, $seriesIndex % 4);
                $marks .= sprintf('<text x="75" y="%d" fill="currentColor">%s</text>', $legendY, $name);
            }

            if ($data->type === 'donut') {
                $marks .= sprintf('<text x="%d" y="%d" text-anchor="middle" fill="currentColor">%s</text>', 150 + ($seriesIndex % 2) * 300, 215 + intdiv($seriesIndex, 2) * 220, $name);
            }

            $marks .= '</g>';
        }

        if ($data->type !== 'donut') {
            $marks .= sprintf('<line x1="40" x2="560" y1="%.2F" y2="%.2F" stroke="currentColor"/><text x="2" y="%.2F" fill="currentColor">0</text>', $zero, $zero, $zero);
            foreach ($categories as $index => $category) {
                $marks .= sprintf('<text x="%.2F" y="215" text-anchor="middle" fill="currentColor">%s</text>', 40 + ($index + 0.5) * $step, $this->escape($category['label']));
            }
        }

        $patterns = '<pattern id="pattern-0" width="8" height="8" patternUnits="userSpaceOnUse"><path d="M0 8L8 0" stroke="currentColor"/></pattern><pattern id="pattern-1" width="8" height="8" patternUnits="userSpaceOnUse"><path d="M0 0L8 8" stroke="currentColor"/></pattern><pattern id="pattern-2" width="6" height="6" patternUnits="userSpaceOnUse"><circle cx="3" cy="3" r="2" fill="currentColor"/></pattern><pattern id="pattern-3" width="8" height="8" patternUnits="userSpaceOnUse"><path d="M0 4H8" stroke="currentColor"/></pattern>';

        $height = $data->type === 'donut' ? (int) ceil($seriesCount / 2) * 220 : ($seriesCount > 1 ? 240 + $seriesCount * 24 : 220);

        return '<svg viewBox="0 0 600 ' . $height . '" role="img" aria-label="' . $this->escape($data->title) . '"><defs>' . $patterns . '</defs>' . $marks . '</svg>';
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
