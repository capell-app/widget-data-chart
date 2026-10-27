<?php

declare(strict_types=1);

use Capell\Core\Enums\ContentStructure;
use Capell\Core\Facades\CapellCore;
use Capell\Core\Models\Page;
use Capell\Core\Models\Site;
use Capell\Core\Models\SiteDomain;
use Capell\Frontend\Actions\AssertPublicHtmlContainsNoAuthoringSurfaceAction;
use Capell\WidgetDataChart\Data\DataChartInputData;
use Capell\WidgetDataChart\Data\DataChartRenderData;
use Capell\WidgetDataChart\Filament\DataChartWidget;
use Capell\WidgetDataChart\Health\WidgetDataChartHealthCheck;
use Capell\WidgetDataChart\Providers\WidgetDataChartServiceProvider;
use Dom\HTMLDocument;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

beforeEach(function (): void {
    CapellCore::forcePackageInstalled('capell-app/widget-data-chart');
    $this->app->register(WidgetDataChartServiceProvider::class);
});
it('renders safe svg and healthy surfaces', function (): void {
    $data = DataChartRenderData::from(['title' => '<script>x</script>', 'type' => 'bar', 'series' => [['label' => 'Sales', 'points' => [['label' => '<img src=x>', 'value' => 10]]]]]);
    expect($data->svg())->not->toContain('<script>')->not->toContain('<img')->toContain('&lt;script&gt;')->and(WidgetDataChartHealthCheck::passed())->toBeTrue();
});
it('rejects unbounded values', function (): void {
    expect(fn (): DataChartInputData => DataChartInputData::validateAndCreate(['title' => 'Chart', 'type' => 'bar', 'series' => [['label' => 'S', 'points' => [['label' => 'P', 'value' => INF]]]]]))->toThrow(ValidationException::class);
});

it('renders signed bars around zero and preserves series in connected lines', function (): void {
    $input = ['title' => 'Balance', 'type' => 'bar', 'series' => [
        ['label' => 'Actual', 'points' => [['label' => 'Jan', 'value' => -20], ['label' => 'Feb', 'value' => 20]]],
        ['label' => 'Plan', 'points' => [['label' => 'Jan', 'value' => 10], ['label' => 'Feb', 'value' => 15]]],
    ]];
    $data = DataChartRenderData::from($input);
    $document = HTMLDocument::createFromString($data->svg(), LIBXML_NOERROR);
    $bars = $document->querySelectorAll('rect:has(title)');
    expect((float) $bars[0]->getAttribute('y'))->toBeGreaterThan((float) $bars[1]->getAttribute('y'));
    expect($document->querySelectorAll('g[aria-label]'))->toHaveCount(2);

    $input['type'] = 'line';
    $data = DataChartRenderData::from($input);
    $document = HTMLDocument::createFromString($data->svg(), LIBXML_NOERROR);
    expect($document->querySelectorAll('polyline'))->toHaveCount(2);
    $lines = $document->querySelectorAll('polyline');
    expect(explode(' ', trim($lines[0]->getAttribute('points'))))->toHaveCount(2);
    $html = view('capell-widget-data-chart::widget', ['renderData' => $data])->render();
    $document = HTMLDocument::createFromString($html, LIBXML_NOERROR);
    expect($document->querySelectorAll('tbody tr'))->toHaveCount(4)
        ->and($document->querySelector('tbody tr td:last-child')?->textContent)->toBe('-20');
});

it('uses cumulative proportional donut arcs and keeps invalid legacy data in its table', function (): void {
    $input = ['title' => 'Share', 'type' => 'donut', 'series' => [['label' => 'Total', 'points' => [['label' => 'A', 'value' => 25], ['label' => 'B', 'value' => 75], ['label' => 'C', 'value' => 0]]]]];
    $data = DataChartRenderData::from($input);
    $document = HTMLDocument::createFromString($data->svg(), LIBXML_NOERROR);
    $arcs = $document->querySelectorAll('circle[stroke-dasharray]');
    $first = (float) explode(' ', $arcs[0]->getAttribute('stroke-dasharray'))[0];
    $second = (float) explode(' ', $arcs[1]->getAttribute('stroke-dasharray'))[0];
    expect($second / $first)->toBeGreaterThan(2.99)->toBeLessThan(3.01)
        ->and(abs((float) $arcs[1]->getAttribute('stroke-dashoffset')))->toEqualWithDelta($first, 0.01)
        ->and($arcs)->toHaveCount(2)
        ->and($first)->toEqualWithDelta(25, 0.00001)
        ->and($first + $second)->toEqualWithDelta(100, 0.00001);

    $input['series'][0]['points'][0]['value'] = -25;
    $stored = DataChartInputData::validateAndCreate($input);
    expect($stored->series->first()?->points->first()?->value)->toBe(-25.0);
    $html = view('capell-widget-data-chart::widget', ['renderData' => DataChartRenderData::from($input)])->render();
    $document = HTMLDocument::createFromString($html, LIBXML_NOERROR);
    expect($document->querySelectorAll('circle[stroke-dasharray]'))->toHaveCount(0)
        ->and($document->querySelector('tbody tr td:last-child')?->textContent)->toBe('-25');
});

it('serves the complete public widget through a page without authoring data', function (string $type, float $firstValue, string $keyStyle = 'sequential'): void {
    $chart = ['title' => 'Balance', 'type' => $type, 'series' => [['label' => 'Actual', 'points' => [['label' => 'Jan', 'value' => $firstValue], ['label' => 'Feb', 'value' => 75]]], ['label' => 'Plan', 'points' => [['label' => 'Jan', 'value' => 10], ['label' => 'Feb', 'value' => 15]]]]];
    if ($keyStyle !== 'sequential') {
        $keys = $keyStyle === 'associative' ? ['series-a', 'series-b'] : [20, 80];
        foreach ($chart['series'] as &$series) {
            $series['points'] = array_combine($keys, $series['points']);
        }

        unset($series);
        $chart['series'] = array_combine($keys, $chart['series']);
    }

    $validated = DataChartInputData::validateAndCreate($chart);
    expect(array_keys($validated->series->all()))->toBe(array_keys($chart['series']))
        ->and(array_keys($validated->series->first()?->points->all() ?? []))->toBe(array_keys(array_values($chart['series'])[0]['points']));
    $site = Site::factory()->createOne();
    SiteDomain::query()->updateOrCreate(
        ['site_id' => $site->id, 'domain' => 'localhost', 'path' => null],
        ['scheme' => 'http', 'language_id' => $site->language_id, 'status' => true, 'default' => true],
    );
    $page = Page::factory()->site($site)
        ->state(['content_structure_override' => ContentStructure::Blocks->value])
        ->withTranslations($site->language, ['title' => 'Public widget', 'content' => [['type' => 'capell-app.data-chart', 'data' => ['__capell' => ['instance_id' => '4924158d-b2cf-4345-b410-130b301c9992', 'state_version' => 1], ...$chart]]]], slug: '/public-widget', contentStructure: ContentStructure::Blocks)
        ->createOne();
    $blueprint = $page->blueprint;
    $blueprint->forceFill(['meta' => [...($blueprint->meta ?? []), 'content_structure' => ContentStructure::Blocks->value]])->save();
    $response = resolve(Kernel::class)->handle(Request::create('/public-widget'));
    expect($response->getStatusCode())->toBe(200);
    $html = $response->getContent();
    $this->assertIsString($html);
    $document = HTMLDocument::createFromString($html, LIBXML_NOERROR);
    expect($document->querySelectorAll('.capell-data-chart polyline'))->toHaveCount($type === 'line' ? 2 : 0)
        ->and($document->querySelectorAll('.capell-data-chart tbody tr'))->toHaveCount(4)
        ->and((float) $document->querySelector('.capell-data-chart tbody tr td:last-child')?->textContent)->toBe($firstValue);
    if ($type === 'bar') {
        $bars = $document->querySelectorAll('.capell-data-chart rect:has(title)');
        expect($bars)->toHaveCount(4)
            ->and((float) $bars[0]->getAttribute('y'))->toBeGreaterThan((float) $bars[1]->getAttribute('y'));
        foreach ($bars as $bar) {
            expect((float) $bar->getAttribute('x'))->toBeGreaterThanOrEqual(40)->toBeLessThan(560);
        }
    }

    if ($type === 'line') {
        $lines = $document->querySelectorAll('.capell-data-chart polyline');
        expect($lines[0]->getAttribute('stroke-dasharray'))->toBe('2 3')
            ->and($lines[1]->getAttribute('stroke-dasharray'))->toBe('4 3');
    }

    if ($type === 'donut' && $firstValue > 0) {
        $arcs = $document->querySelectorAll('.capell-data-chart circle[stroke-dasharray]');
        expect($arcs)->toHaveCount(4)
            ->and((float) explode(' ', $arcs[0]->getAttribute('stroke-dasharray'))[0])->toBe(25.0)
            ->and((float) $arcs[1]->getAttribute('stroke-dashoffset'))->toBe(-25.0)
            ->and($arcs[0]->getAttribute('cy'))->toBe('110')
            ->and($arcs[0]->getAttribute('stroke'))->toBe('url(#pattern-0)')
            ->and($arcs[1]->getAttribute('stroke'))->toBe('url(#pattern-1)');
    }

    if ($type === 'donut' && $firstValue < 0) {
        expect($document->querySelectorAll('.capell-data-chart svg'))->toHaveCount(0);
    }

    AssertPublicHtmlContainsNoAuthoringSurfaceAction::run($response);
})->with(['signed line' => ['line', -20.0], 'signed bars' => ['bar', -20.0], 'proportional donut' => ['donut', 25.0], 'legacy negative donut' => ['donut', -25.0], 'associative line' => ['line', -20.0, 'associative'], 'associative bars' => ['bar', -20.0, 'associative'], 'associative donut' => ['donut', 25.0, 'associative'], 'sparse line' => ['line', -20.0, 'sparse'], 'sparse bars' => ['bar', -20.0, 'sparse'], 'sparse donut' => ['donut', 25.0, 'sparse']]);

it('keeps fractional signed values visible relative to the zero axis', function (string $type): void {
    $data = DataChartRenderData::from(['title' => 'Small balance', 'type' => $type, 'series' => [['label' => 'Balance', 'points' => [['label' => 'Debit', 'value' => -0.00001], ['label' => 'Credit', 'value' => 0.00002]]]]]);
    $html = view('capell-widget-data-chart::widget', ['renderData' => $data])->render();
    $document = HTMLDocument::createFromString($html, LIBXML_NOERROR);
    $zero = (float) $document->querySelector('svg > line')?->getAttribute('y1');
    expect($zero)->toBeGreaterThan(30)->toBeLessThan(190)
        ->and($document->querySelectorAll('tbody tr'))->toHaveCount(2);
    if ($type === 'bar') {
        $bars = $document->querySelectorAll('rect:has(title)');
        expect((float) $bars[0]->getAttribute('height'))->toEqualWithDelta(160 / 3, 0.01)
            ->and((float) $bars[1]->getAttribute('height'))->toEqualWithDelta(320 / 3, 0.01)
            ->and((float) $bars[0]->getAttribute('y'))->toEqualWithDelta($zero, 0.01)
            ->and((float) $bars[1]->getAttribute('y'))->toEqualWithDelta(30, 0.01);
    } else {
        $points = $document->querySelectorAll('circle:has(title)');
        expect((float) $points[0]->getAttribute('cy'))->toEqualWithDelta(190, 0.01)
            ->and((float) $points[1]->getAttribute('cy'))->toEqualWithDelta(30, 0.01);
    }
})->with(['bar', 'line']);

it('renders a finite zero axis when every value is zero', function (string $type): void {
    $data = DataChartRenderData::from(['title' => 'No balance', 'type' => $type, 'series' => [['label' => 'Balance', 'points' => [['label' => 'Empty', 'value' => 0]]]]]);
    $document = HTMLDocument::createFromString(view('capell-widget-data-chart::widget', ['renderData' => $data])->render(), LIBXML_NOERROR);
    expect($document->querySelector('svg > line')?->getAttribute('y1'))->toBe('190.00')
        ->and($document->querySelector('tbody tr td:last-child')?->textContent)->toBe('0');
    if ($type === 'bar') {
        expect($document->querySelector('rect:has(title)')?->getAttribute('height'))->toBe('0.00');
    } else {
        expect($document->querySelector('circle:has(title)')?->getAttribute('cy'))->toBe('190.00');
    }
})->with(['bar', 'line']);

it('validates non negative donut values in the nested editor while allowing signed bars', function (): void {
    $host = new class extends Component implements HasSchemas
    {
        use InteractsWithSchemas;

        /** @var array<string, mixed> */
        public array $data = [];
    };
    $block = DataChartWidget::make();
    Schema::make($host)->statePath('data')->components([$block])->fill();
    $host->data['type'] = 'donut';
    $blockSchema = $block->getChildSchema();
    $this->assertInstanceOf(Schema::class, $blockSchema);
    $series = $blockSchema->getComponent('series');
    $this->assertInstanceOf(Repeater::class, $series);
    $series->state(['series-key' => ['label' => 'Share', 'points' => ['point-key' => ['label' => 'Part', 'value' => -1]]]]);
    $seriesItem = $series->getChildSchema('series-key');
    $this->assertInstanceOf(Schema::class, $seriesItem);
    $points = $seriesItem->getComponent('points');
    $this->assertInstanceOf(Repeater::class, $points);
    $pointItem = $points->getChildSchema('point-key');
    $this->assertInstanceOf(Schema::class, $pointItem);
    $value = $pointItem->getComponent('value');
    $this->assertInstanceOf(TextInput::class, $value);
    expect($value->getMinValue())->toBe(0)
        ->and(Validator::make(['value' => -1], ['value' => $value->getValidationRules()])->passes())->toBeFalse();
    $host->data['type'] = 'bar';
    expect($value->getMinValue())->toBe(-1000000)
        ->and(Validator::make(['value' => -1], ['value' => $value->getValidationRules()])->passes())->toBeTrue();
});

it('keeps donut series separate and omits empty totals', function (): void {
    $data = DataChartRenderData::from(['title' => 'Shares', 'type' => 'donut', 'series' => [
        ['label' => 'First', 'points' => [['label' => 'A', 'value' => 10], ['label' => 'B', 'value' => 30]]],
        ['label' => 'Second', 'points' => [['label' => 'A', 'value' => 50], ['label' => 'B', 'value' => 50]]],
        ['label' => 'Empty', 'points' => [['label' => 'A', 'value' => 0]]],
    ]]);
    $document = HTMLDocument::createFromString($data->svg(), LIBXML_NOERROR);
    $first = $document->querySelectorAll('g[aria-label="First"] circle');
    $second = $document->querySelectorAll('g[aria-label="Second"] circle');
    expect($first)->toHaveCount(2)
        ->and($second)->toHaveCount(2)
        ->and((float) explode(' ', $first[0]->getAttribute('stroke-dasharray'))[0])->toBe(25.0)
        ->and((float) explode(' ', $second[0]->getAttribute('stroke-dasharray'))[0])->toBe(50.0)
        ->and($first[0]->getAttribute('cx'))->not->toBe($second[0]->getAttribute('cx'))
        ->and((float) $second[0]->getAttribute('stroke-dashoffset'))->toBe(0.0)
        ->and($document->querySelectorAll('g[aria-label="Empty"] circle'))->toHaveCount(0);
});
