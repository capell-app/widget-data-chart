@php /** @var \Capell\WidgetDataChart\Data\DataChartRenderData $renderData */ @endphp
<figure class="capell-data-chart">
    <h2>{{ $renderData->title }}</h2>
    @php $graphic = $renderData->svg(); @endphp
    @if ($graphic !== '')
        <div class="capell-data-chart__graphic">{!! $graphic !!}</div>
    @else
        <p>{{ __('capell-widget-data-chart::widget.invalid_donut') }}</p>
    @endif
    <details>
        <summary>{{ __('capell-widget-data-chart::widget.table') }}</summary>
        <div class="capell-data-chart__table">
            <table>
                <caption>
                    {{ $renderData->title }}
                </caption>
                <thead>
                    <tr>
                        <th scope="col">
                            {{ __('capell-widget-data-chart::widget.series') }}
                        </th>
                        <th scope="col">
                            {{ __('capell-widget-data-chart::widget.point_label') }}
                        </th>
                        <th scope="col">
                            {{ __('capell-widget-data-chart::widget.value') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($renderData->series as $series)
                        @foreach ($series->points as $point)
                            <tr>
                                <th scope="row">{{ $series->label }}</th>
                                <td>{{ $point->label }}</td>
                                <td>{{ $point->value }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </details>
</figure>
