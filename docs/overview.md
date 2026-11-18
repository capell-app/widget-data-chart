# Data Chart Widget

<!-- prettier-ignore-start -->

The figures in your annual report or your school's results can go on the page as a bar chart, a line chart or a donut. You type the numbers in, and the chart is drawn for you.

There's no spreadsheet to link and nothing updates on its own. One chart holds up to eight sets of figures, say one for each year, with up to fifty numbers in each. Bars can drop below zero to show a loss. Donuts can't show negative figures, and the form says so before you save.

Colours aren't yours to pick. Every chart is drawn in your site's main brand colour. Bars and donut slices are filled in turn with four patterns of fine lines and dots, and each line on a line chart gets its own dash. A chart printed without colour still reads.

Hovering over a bar or slice shows its exact value. Under the chart, a Chart data heading opens a table of every label and number for anyone who wants to quote a figure.

## What it does

Data Chart turns small editorial datasets into bar, line, or donut charts without a client charting service. Each chart is rendered server-side and paired with a complete accessible data table.

## Do I need to do anything?

Add **Data Chart** in Layout Builder, enter a title, and choose **Bar**, **Line**, or **Donut**. Add one to eight series, each with one to 50 labelled numeric points. Bar and line values can range from `-1,000,000` to `1,000,000`, with positive and negative values on opposite sides of zero. Lines connect the points of each series. Donuts require non-negative values and show a separate ring for each series, with each segment proportional to that series' total; zero values have no segment. Existing negative donut data remains in the accessible table with an explanation instead of a misleading graphic. Preview the public page before publishing.

## Where it shows up

Editors configure the chart type and editorial data in Layout Builder. The public page renders a server-side SVG followed by an expandable **Chart data** table containing every series, label, and value.

## Good to know

- This free extension belongs to Capell Essentials and requires Layout Builder.
- The chart is editorial data, not a connection to an analytics service, spreadsheet, CSV import, or live data source.
- SVG titles and pattern fills, plus the HTML data table, keep meaning available without relying on colour alone.
- Labels and values are escaped before rendering; use concise labels because the visual marks expose the point label and value in their accessible title.

## Screenshot status

Capture the chart configuration and a public chart with pattern-filled marks, accessible point titles, and its expanded data-table disclosure.

---

For developers: see the [README](../README.md).

<!-- prettier-ignore-end -->
