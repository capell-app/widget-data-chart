<?php

declare(strict_types=1);

namespace Capell\WidgetDataChart\Filament;

use Capell\Admin\Contracts\Widgets\FilamentWidget;
use Capell\WidgetDataChart\Enums\ChartType;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Override;

final class DataChartWidget implements FilamentWidget
{
    #[Override]
    public static function getWidgetName(): string
    {
        return 'capell-app.data-chart';
    }

    #[Override]
    public static function make(): Block
    {
        return Block::make(self::getWidgetName())->label(__('capell-widget-data-chart::widget.label'))->schema([
            TextInput::make('title')->required()->maxLength(120),
            Select::make('type')->options(ChartType::class)->default(ChartType::Bar->value)->live()->helperText(__('capell-widget-data-chart::widget.donut_help'))->required(),
            Repeater::make('series')->minItems(1)->maxItems(8)->schema([
                TextInput::make('label')->required()->maxLength(80),
                Repeater::make('points')->minItems(1)->maxItems(50)->schema([
                    TextInput::make('label')->required()->maxLength(80),
                    TextInput::make('value')->numeric()->minValue(fn (Get $get): int => in_array(
                        $get('../../../../type'),
                        [
                            ChartType::Donut,
                            ChartType::Donut->value,
                        ],
                        true,
                    ) ? 0 : -1000000)->maxValue(1000000)->required(),
                ]),
            ]),
        ]);
    }
}
