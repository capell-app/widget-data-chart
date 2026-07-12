<?php

declare(strict_types=1);

namespace Capell\WidgetDataChart\Health;

use Capell\LayoutBuilder\Health\AbstractWidgetExtensionHealthCheck;
use Capell\WidgetDataChart\Filament\DataChartWidget;

final class WidgetDataChartHealthCheck extends AbstractWidgetExtensionHealthCheck
{
    public static function compatibleCapellApiVersion(): string
    {
        return '^4.0';
    }

    protected static function definitionKey(): string
    {
        return 'capell-app.data-chart';
    }

    protected static function filamentWidget(): string
    {
        return DataChartWidget::class;
    }

    protected static function fallbackView(): string
    {
        return 'capell-widget-data-chart::widget';
    }

    protected static function resourceGroups(): array
    {
        return ['capell-app.widget-data-chart'];
    }
}
