<?php

declare(strict_types=1);

namespace Capell\WidgetDataChart\Providers;

use Capell\Core\Enums\PresentationLoadingStrategy;
use Capell\LayoutBuilder\Data\WidgetExtensions\WidgetExtensionCapabilitiesData;
use Capell\LayoutBuilder\Data\WidgetExtensions\WidgetExtensionDefinitionData;
use Capell\LayoutBuilder\Enums\WidgetPresentationCapability;
use Capell\LayoutBuilder\Providers\AbstractFoundationWidgetServiceProvider;
use Capell\WidgetDataChart\Data\DataChartInputData;
use Capell\WidgetDataChart\Data\DataChartRenderData;
use Capell\WidgetDataChart\Filament\DataChartWidget;

final class WidgetDataChartServiceProvider extends AbstractFoundationWidgetServiceProvider
{
    public static string $name = 'capell-widget-data-chart';

    public static string $packageName = 'capell-app/widget-data-chart';

    protected static function resourceGroup(): string
    {
        return 'capell-app.widget-data-chart';
    }

    protected function definition(): WidgetExtensionDefinitionData
    {
        return new WidgetExtensionDefinitionData(key: 'capell-app.data-chart', packageName: self::$packageName, stateVersion: 1, filamentWidget: DataChartWidget::class, inputData: DataChartInputData::class, renderData: DataChartRenderData::class, fallbackView: 'capell-widget-data-chart::widget', components: ['blade' => 'capell::widgets.capell-app.data-chart'], resourceGroups: [self::resourceGroup()], defaultResourceLoadingStrategy: PresentationLoadingStrategy::Visible, capabilities: new WidgetExtensionCapabilitiesData(requiresInstanceIdentity: true, presentationCapabilities: [WidgetPresentationCapability::Width, WidgetPresentationCapability::Alignment, WidgetPresentationCapability::LoadingStrategy]));
    }
}
