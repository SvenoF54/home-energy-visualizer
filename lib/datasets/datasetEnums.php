<?php
enum EnergyTypeEnum: string {
    case EM = 'EM';
    case PM1 = 'PM1';
    case PM2 = 'PM2';
    case PM3 = 'PM3';
}

enum KeyValueStoreScopeEnum: string {
    case Task = 'Task';
    case SendMail = 'SendMail';
}

enum TaskEnum: string {
    case CheckRealtimeEnergyData = 'CheckRealtimeEnergyData';
    case UnifyRealtimeEnergyData = 'UnifyRealtimeEnergyData';
}

enum MailEnum: string {
    case SendRealtimeEnergyDataLoss = 'SendRealtimeEnergyDataLoss';
}

enum StatusEnum: string {
    case Success = 'Success';
    case Failure = 'Failure';
    case Exception = 'Exception';
}
