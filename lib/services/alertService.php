<?php

class AlertService {
    private $realTimeEnergyDataTbl;

    public function __construct($pdoConnection)
    {
        $this->realTimeEnergyDataTbl = new RealTimeEnergyDataTable($pdoConnection);
    }

    public function checkEnergyData()
    {
        $actualConfig = Configuration::getInstance()->configRealtimeAlert();
        $latestLogData = $this->realTimeEnergyDataTbl->getLatestLogData();        

        $hasAlerts = false;
        $message = "Für folgende Geräte wurden in den letzten {$actualConfig->getAlertThresholdInMinutes()} Minuten keine Daten empfangen: \n\n";
        $missingEnergyTypes = array();
        foreach (EnergyTypeEnum::cases() as $energyType) {
            if (! $actualConfig->shouldAlertForEnergyType($energyType)) {
                continue;
            }
            
            $hasData = $latestLogData->hasEnergyData($energyType, $actualConfig->getAlertThresholdInMinutes());
            if (! $hasData) {
                $hasAlerts = true;
                $message .= "- ".$energyType->value . " hat keine aktuellen Daten.\n";
                $missingEnergyTypes[] = $energyType->value;
            }
        }

        if ($hasAlerts && $actualConfig->getSendAlertMail()) {
            $subject = "Datenverlust für: ".implode(", ", $missingEnergyTypes);
            MailService::sendMail(SYSTEM_EMAIL, $subject, $message);
        }
    }
    
}
