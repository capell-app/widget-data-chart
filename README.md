# Data Chart Widget

<!-- prettier-ignore-start -->

## What This Plugin Adds

Data Chart Widget is an **Available**, **No schema impact** Capell package in the **Capell Foundation** product group. It ships as `capell-app/widget-data-chart` and extends these surfaces: admin, frontend.

Validated bar, line, and donut charts rendered as server-side SVG with an accessible HTML table.

After install, admins get package-owned management surfaces and public users may see package-owned frontend output or routes.

Status details:

- Status: Available
- Tier: free
- Bundle: foundation
- Composer package: `capell-app/widget-data-chart`
- Namespace: `Capell\WidgetDataChart`
- Theme key: not applicable

## Why It Matters

**For developers:** The package gives developers package-owned service providers, Data objects, Filament classes, and Blade views instead of pushing this behaviour into core or application code.

**For teams:** Server-rendered charts with patterns, labels, and accessible tables.

## Screens And Workflow

Screenshot contract: `docs/screenshots.json`.

![Data Chart Widget extension card](docs/screenshots/extension-card.svg)

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

This package has no schema impact. It does not declare package-owned migrations or required tables.

Docs gap: document extension points here if the package delegates persistence to a host package.

## Install Impact

- Admin navigation: adds package-owned Filament classes when registered.
- Permissions: none declared in `capell.json`.
- Public routes: none detected in package route files.
- Database changes: no package migrations declared.
- Settings: no package settings declared.
- Queues or schedules: none detected in standard package paths.
- Cache tags: `widget-data-chart`.
- Commands: none declared.

## Common Pitfalls

- Keep public Blade and cached HTML free of authoring markers, model IDs, permissions, signed editor URLs, and lazy database queries.
- Keep `composer.json`, `composer.local.json`, `capell.json`, docs, screenshots, and tests aligned when the package surface changes.

## Troubleshooting

| Symptom | Likely cause | Check | Fix |
| --- | --- | --- | --- |
| Package surface is missing after install | Provider or manifest is not loaded | Confirm `capell.json`, package `composer.json`, and provider registration | Reinstall the package, refresh Composer autoload, and clear host caches |
| Public output leaks unexpected state | Render data, cache variation, or authoring boundary has regressed | Check public Blade, cache tags, and public-output safety tests | Move data loading out of Blade and rerun the package public-output tests |

## Quick Start

1. Install the package: `composer require capell-app/widget-data-chart`.
2. Run the required setup: no package migrations are declared; clear cached config and routes if the host app uses caches.
3. Open the related Capell admin surface and verify Data Chart Widget appears.

## Next Steps

- [Package docs](docs/README.md)
- [Overview](docs/overview.md)
- [Screenshot contract](docs/screenshots.json)
- [Marketplace assets](docs/assets/marketplace/)
- [Capell content language plan](../../docs/CONTENT_LANGUAGE_PLAN.md)
- [Capell documentation design system](../../docs/DESIGN_SYSTEM.md)
- [Capell and package ERD notes](../../docs/erd/capell-and-package-erds.md)
- Related packages: [Layout Builder](../layout-builder/README.md).
- Focused tests: `vendor/bin/pest packages/widget-data-chart/tests --configuration=phpunit.xml`.

<!-- prettier-ignore-end -->
