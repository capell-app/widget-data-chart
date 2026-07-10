<?php

declare(strict_types=1);

namespace Capell\WidgetDataChart;

use Capell\Core\Contracts\Extensions\RegistersExtensionContentWidget;

final class WidgetDataChartContribution implements RegistersExtensionContentWidget
{
    public static function compatibleCapellApiVersion(): string
    {
        return '^4.1';
    }
}
