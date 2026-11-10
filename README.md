# Data Chart Widget

<!-- prettier-ignore-start -->

## What It Does

Data Chart adds a Layout Builder widget for bar, line, and donut charts. It renders server-side SVG together with an accessible data table.

- **Price and availability:** Check current availability and price on the [Capell Marketplace listing](https://capell.app/extensions/widget-data-chart).
- **Licence:** Check the licence terms on the [Capell Marketplace listing](https://capell.app/extensions/widget-data-chart).
- **Requires:** `PHP ^8.4`, `capell-app/admin ^1.0`, `capell-app/core ^1.0.66`, `capell-app/frontend ^1.0`, `capell-app/layout-builder ^1.0`, `lorisleiva/laravel-actions ^2.8`, `spatie/laravel-package-tools ^1.0`.
- **Install:** `composer require capell-app/widget-data-chart`.

Editors enter chart labels and values in Layout Builder, and visitors receive both the visual chart and its tabular values in public output.

## Who It Is For

**For developers:** Typed render data feeds a server-rendered Blade view, and focused tests check the generated SVG and output safety.

**For teams:** Teams can explain small datasets visually while keeping the underlying values available to readers who do not use the chart graphic.

## Quick Start

1. Install the package: `composer require capell-app/widget-data-chart`.
2. Open a page, click **Layout builder**, **Add widgets**, then choose **Data Chart**.

## Configure

This extension works without configuration. There is nothing to set after install.

## Limits And Pitfalls

- Install the required Capell packages at the versions in `composer.json`: `capell-app/admin ^1.0`, `capell-app/core ^1.0.66`, `capell-app/frontend ^1.0`, `capell-app/layout-builder ^1.0`.
- Keep public Blade and cached HTML free of authoring markers, model IDs, permissions, signed editor URLs, and lazy database queries.
- Custom write integrations must preserve invalidation for `widget-data-chart` cache tags.

## Screens And Workflow

![Data Chart public output](docs/screenshots/data-chart-public-output.png)

![Data Chart fields in Layout Builder](docs/screenshots/data-chart-layout-builder-editor.png)

- Data Chart Widget extension card (marketplace).
- Data Chart fields in Layout Builder (admin).
- Data Chart public output (frontend).
- Data Chart fields in Layout Builder with admin sidebar menu open (admin).

## Install Impact

- Required packages: `capell-app/admin`, `capell-app/core`, `capell-app/frontend`, `capell-app/layout-builder`.
- Admin navigation: no admin page or resource contribution is declared.
- Admin/editor extensions: `content-widget: WidgetDataChartContribution`.
- Permissions: no package permission declarations or Shield gates detected; host access rules still apply.
- Public routes: none declared.
- Database changes: no package migrations declared.
- Config: no package config files.
- Settings: no package settings declared.
- Queues or schedules: none declared.
- Cache tags: `widget-data-chart`.
- Commands: none declared.

## Troubleshooting

| Symptom | Likely cause | Check | Fix |
| --- | --- | --- | --- |
| Package surface is missing after install | Provider or manifest is not loaded | Confirm `capell.json`, package `composer.json`, and provider registration | Reinstall the package, refresh Composer autoload, and clear host caches |
| Public output leaks unexpected state | Render data, cache variation, or authoring boundary has regressed | Check public Blade, cache tags, and public-output safety tests | Move data loading out of Blade and rerun the package public-output tests |
| The chart is refused | It has no series, more than 8, or a type other than bar, line or donut | Count the series and check the chart type | Keep between 1 and 8 series and pick a listed type |
| A series or value is refused | A series has no points or more than 50, or a value is outside -1,000,000 to 1,000,000 | Check the points in each series | Keep between 1 and 50 points per series and values within range |

## Upgrading

No manual steps between minor versions.

## Next Steps

- [Package docs](docs/README.md)
- [Overview](docs/overview.md)
- [Troubleshooting](#troubleshooting)
- [Developer reference](docs/reference.md)
- [Marketplace assets](docs/assets/marketplace/)
- [Capell content language plan](../../docs/CONTENT_LANGUAGE_PLAN.md)
- [Capell documentation design system](../../docs/DESIGN_SYSTEM.md)
- [Capell and package ERD notes](../../docs/erd/capell-and-package-erds.md)
- Related packages: [Layout Builder](../layout-builder/README.md).

<!-- prettier-ignore-end -->
