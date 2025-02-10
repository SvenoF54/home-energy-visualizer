<?php

class ConfigRealtimeAlert
{
    private $alertForEnergyType = array();
    private $alertThresholdInMinutes = 5;
    private $sendAlertMail = false;
    private $sendAlertMailEveryXMinutes = 30;

    public function getAlertThresholdInMinutes()
    {
        return $this->alertThresholdInMinutes;
    }

    public function setAlertThresholdInMinutes($alertThresholdInMinutes)
    {
        $this->alertThresholdInMinutes = $alertThresholdInMinutes;
    }

    public function getSendAlertMail()
    {
        return $this->sendAlertMail;
    }

    public function setSendAlertMail($sendAlertMail)
    {
        $this->sendAlertMail = $sendAlertMail;
    }
    
    public function setSendAlertMailEveryXMinutes($sendAlertMailEveryXMinutes)
    {
        $this->sendAlertMailEveryXMinutes = $sendAlertMailEveryXMinutes;
    }

    public function getSendAlertMailEveryXMinutes()
    {
        return $this->sendAlertMailEveryXMinutes;
    }

    public function shouldAlertForEnergyType($energyType)
    {
        return array_key_exists($energyType->value, $this->alertForEnergyType) ? $this->alertForEnergyType[$energyType->value] : false;
    }

    public function setShouldAlertForEnergyType($energyType, $alertForEm)
    {
        $this->alertForEnergyType[$energyType->value] = $alertForEm;
    }

}
