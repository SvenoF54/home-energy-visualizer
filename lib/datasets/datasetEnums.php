<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

enum EnergyTypeEnum: string {
    case EM = 'EM';
    case PM1 = 'PM1';
    case PM2 = 'PM2';
    case PM3 = 'PM3';
}

enum KeyValueStoreScopeEnum: string {
    case Task = 'Task';
    case SendMail = 'SendMail';
    case Zendure = 'Zendure';
    case Shelly = 'Shelly';
}

enum TaskEnum: string {
    case CheckRealtimeEnergyData = 'CheckRealtimeEnergyData';
    case UnifyRealtimeEnergyData = 'UnifyRealtimeEnergyData';
    case ReadZendureData = 'ReadZendureData';
    case ReadShellyData = 'ReadShellyData';
}

enum MailEnum: string {
    case SendRealtimeEnergyDataLoss = 'SendRealtimeEnergyDataLoss';
}

enum StatusEnum: string {
    case Success = 'Success';
    case Failure = 'Failure';
    case Exception = 'Exception';

    public function isErrorOrException(): bool {
        return $this === self::Failure || $this === self::Exception;
    }
    
    public static function isErrorOrExceptionValue(string $status): bool {
        return in_array($status, [self::Failure->value, self::Exception->value], true);
    }
}
