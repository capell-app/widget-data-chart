<?php

declare(strict_types=1);
use Capell\WidgetDataChart\Data\DataChartInputData;
use Capell\WidgetDataChart\Data\DataChartRenderData;
use Capell\WidgetDataChart\Health\WidgetDataChartHealthCheck;
use Capell\WidgetDataChart\Providers\WidgetDataChartServiceProvider;
use Illuminate\Validation\ValidationException;

beforeEach(function (): void {
    $this->app->register(WidgetDataChartServiceProvider::class);
});
it('renders safe svg and healthy surfaces', function (): void {
    $data = DataChartRenderData::from(['title' => '<script>x</script>', 'type' => 'bar', 'series' => [['label' => 'Sales', 'points' => [['label' => '<img src=x>', 'value' => 10]]]]]);
    expect($data->svg())->not->toContain('<script>')->not->toContain('<img')->toContain('&lt;script&gt;')->and(WidgetDataChartHealthCheck::passed())->toBeTrue();
});
it('rejects unbounded values', function (): void {
    expect(fn () => DataChartInputData::validateAndCreate(['title' => 'Chart', 'type' => 'bar', 'series' => [['label' => 'S', 'points' => [['label' => 'P', 'value' => INF]]]]]))->toThrow(ValidationException::class);
});
