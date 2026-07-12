<?php

declare(strict_types=1);

namespace Capell\WidgetDataChart\Filament;

use Capell\Admin\Contracts\Widgets\FilamentWidget;
use Capell\WidgetDataChart\Enums\ChartType;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

final class DataChartWidget implements FilamentWidget
{
    public static function getWidgetName(): string
    {
        return 'capell-app.data-chart';
    }

    public static function make(): Block
    {
        return Block::make(self::getWidgetName())->label(__('capell-widget-data-chart::widget.label'))->schema([TextInput::make('title')->required()->maxLength(120), Select::make('type')->options(ChartType::class)->default(ChartType::Bar->value)->required(), Repeater::make('series')->minItems(1)->maxItems(8)->schema([TextInput::make('label')->required()->maxLength(80), Repeater::make('points')->minItems(1)->maxItems(50)->schema([TextInput::make('label')->required()->maxLength(80), TextInput::make('value')->numeric()->minValue(-1000000)->maxValue(1000000)->required()])])]);
    }
}
