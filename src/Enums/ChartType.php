<?php

declare(strict_types=1);

namespace Capell\WidgetDataChart\Enums;

use Filament\Support\Contracts\HasLabel;

enum ChartType: string implements HasLabel
{
    case Bar = 'bar';
    case Line = 'line';
    case Donut = 'donut';

    public function getLabel(): string
    {
        return match ($this) {
            self::Bar => __('capell-widget-data-chart::widget.bar'),self::Line => __('capell-widget-data-chart::widget.line'),self::Donut => __('capell-widget-data-chart::widget.donut')
        };
    }
}
