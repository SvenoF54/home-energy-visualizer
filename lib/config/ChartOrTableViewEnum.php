<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

enum ChartOrTableViewEnum: string {
    case EnergyChart = 'EnergyChart';
    case AutarkyChart = 'AutarkyChart';
    case EnergyTable = 'EnergyTable';

    public static function isEnergyChart($val): bool {
        if ($val instanceof self) {
            return $val === self::EnergyChart;
        }
        return $val === self::EnergyChart->value;
    }

    public static function isEnergyTable($val): bool {
        if ($val instanceof self) {
            return $val === self::EnergyTable;
        }
        return $val === self::EnergyTable->value;
    }

    public static function isAutarkyChart($val): bool {
        if ($val instanceof self) {
            return $val === self::AutarkyChart;
        }
        return $val === self::AutarkyChart->value;
    }
}
