<?php
enum ChartOrTableOnFirstPageViewEnum: string {
    case EnergyChart = 'EnergyChart';
    case AutarkyChart = 'AutarkyChart';
    case EnergyTable = 'EnergyTable';

    public static function isEnergyChart($val) {
        return self::EnergyChart->value ==  $val;
    }

    public static function isEnergyTable($val) {
        return self::EnergyTable->value ==  $val;
    }

    public static function isAutarkyChart($val) {
        return self::AutarkyChart->value ==  $val;
    }
}
