# Data Chart Widget

<!-- prettier-ignore-start -->

## What This Plugin Adds

Data Chart Widget is an **Available**, **No schema impact** Capell package in the **Capell Foundation** product group. It ships as `capell-app/widget-data-chart` and extends these surfaces: admin, frontend.

Data Chart adds a Layout Builder widget for bar, line, and donut charts. It renders server-side SVG together with an accessible data table.

Editors enter chart labels and values in Layout Builder, and visitors receive both the visual chart and its tabular values in public output.

Evidence: [`src/Data/DataChartRenderData.php`](src/Data/DataChartRenderData.php), [`resources/views/widget.blade.php`](resources/views/widget.blade.php), [`tests/Feature/WidgetDataChartTest.php`](tests/Feature/WidgetDataChartTest.php), [`capell.json`](capell.json), [`src/Filament/DataChartWidget.php`](src/Filament/DataChartWidget.php).

Status details:

- Status: Available
- Tier: free
- Bundle: foundation
- Composer package: `capell-app/widget-data-chart`
- Namespace: `Capell\WidgetDataChart`
- Theme key: not applicable

## Why It Matters

**For developers:** Typed render data feeds a server-rendered Blade view, and focused tests check the generated SVG and output safety.

**For teams:** Teams can explain small datasets visually while keeping the underlying values available to readers who do not use the chart graphic.

Evidence: [`src/Data/DataChartRenderData.php`](src/Data/DataChartRenderData.php), [`src/Providers/WidgetDataChartServiceProvider.php`](src/Providers/WidgetDataChartServiceProvider.php), [`tests/Feature/WidgetDataChartTest.php`](tests/Feature/WidgetDataChartTest.php), [`resources/views/widget.blade.php`](resources/views/widget.blade.php).

## Screens And Workflow

Screenshot contract: `docs/screenshots.json`.

![Data Chart Widget extension card](docs/screenshots/extension-card.svg)

![Data Chart fields in Layout Builder](docs/screenshots/data-chart-layout-builder-editor.png)

- Data Chart Widget extension card (marketplace, required).
- Data Chart fields in Layout Builder (admin, optional).
- Data Chart public output (frontend, optional).

## Technical Shape

- Service providers: `Capell\WidgetDataChart\Providers\WidgetDataChartServiceProvider`.
- Filament classes: `DataChartWidget`.
- Data objects: `ChartPointData`, `ChartSeriesData`, `DataChartInputData`, `DataChartRenderData`.
- Manifest contributions: `content-widget: Capell\WidgetDataChart\WidgetDataChartContribution`.
- Health checks: `Capell\WidgetDataChart\Health\WidgetDataChartHealthCheck`.
- Blade views: `packages/widget-data-chart/resources/views/widget.blade.php`.
- Cache tags: `widget-data-chart`.

## Data Model

This package has no schema impact. It extends Capell through `content-widget` contributions instead of declaring package-owned tables.

## Install Impact

- Required packages: `capell-app/admin`, `capell-app/core`, `capell-app/frontend`, `capell-app/layout-builder`.
- Admin navigation: no admin page or resource contribution is declared.
- Admin/editor extensions: `content-widget: WidgetDataChartContribution`.
- Permissions: none declared in `capell.json`.
- Public routes: none declared.
- Database changes: no package migrations declared.
- Config: no package config files.
- Settings: no package settings declared.
- Queues or schedules: none declared.
- Cache tags: `widget-data-chart`.
- Commands: none declared.

## Common Pitfalls

- Keep required Capell packages on compatible v4 releases: `capell-app/admin`, `capell-app/core`, `capell-app/frontend`, `capell-app/layout-builder`.
- Keep public Blade and cached HTML free of authoring markers, model IDs, permissions, signed editor URLs, and lazy database queries.
- Custom write integrations must preserve invalidation for `widget-data-chart` cache tags.

## Troubleshooting

| Symptom | Likely cause | Check | Fix |
| --- | --- | --- | --- |
| Package surface is missing after install | Provider or manifest is not loaded | Confirm `capell.json`, package `composer.json`, and provider registration | Reinstall the package, refresh Composer autoload, and clear host caches |
| Public output leaks unexpected state | Render data, cache variation, or authoring boundary has regressed | Check public Blade, cache tags, and public-output safety tests | Move data loading out of Blade and rerun the package public-output tests |

## Quick Start

1. Install the package: `composer require capell-app/widget-data-chart`.
2. No package-specific setup command or migrations are declared.
3. Open the package detail or install-intent surface and confirm the Data Chart Widget extension card is present.

## Next Steps

- [Package docs](docs/README.md)
- [Overview](docs/overview.md)
- [Troubleshooting](#troubleshooting)
- [Screenshot contract](docs/screenshots.json)
- [Marketplace assets](docs/assets/marketplace/)
- [Capell content language plan](../../docs/CONTENT_LANGUAGE_PLAN.md)
- [Capell documentation design system](../../docs/DESIGN_SYSTEM.md)
- [Capell and package ERD notes](../../docs/erd/capell-and-package-erds.md)
- Related packages: [Layout Builder](../layout-builder/README.md).
- Focused tests: `vendor/bin/pest packages/widget-data-chart/tests --configuration=phpunit.xml`.

<!-- prettier-ignore-end -->
