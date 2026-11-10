# Data Chart Widget reference

<!-- prettier-ignore-start -->

Developer reference for `capell-app/widget-data-chart`. Start with the [README](../README.md) for what the extension does and how to install it.

## Package Details

Data Chart Widget is an **Available**, **No schema impact** Capell package in the **Capell Essentials** product group. It ships as `capell-app/widget-data-chart` and extends these surfaces: admin, frontend.

- Status: Available
- Tier: free
- Bundle: foundation
- Composer package: `capell-app/widget-data-chart`
- Namespace: `Capell\WidgetDataChart`
- Theme key: not applicable

## Technical Shape

### Service providers

- `Capell\WidgetDataChart\Providers\WidgetDataChartServiceProvider`

### Filament classes

- `DataChartWidget`

### Actions

- `RenderDataChartSvgAction`

### Data objects

- `ChartPointData`
- `ChartSeriesData`
- `DataChartInputData`
- `DataChartRenderData`

### Manifest contributions

- `asset: Capell\WidgetDataChart\WidgetDataChartContribution`
- `content-widget: Capell\WidgetDataChart\WidgetDataChartContribution`

### Health checks

- `Capell\WidgetDataChart\Health\WidgetDataChartHealthCheck`

### Blade views

- `packages/widget-data-chart/resources/views/widget.blade.php`

### Cache tags

- `widget-data-chart`


## Data Model

This package has no schema impact. It extends Capell through `asset` contributions and `content-widget` contributions instead of declaring package-owned tables.

<!-- prettier-ignore-end -->
