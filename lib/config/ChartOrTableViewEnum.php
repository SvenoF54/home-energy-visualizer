<?php
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
