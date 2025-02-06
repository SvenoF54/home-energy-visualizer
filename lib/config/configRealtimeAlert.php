<?php

class ConfigRealtimeAlert
{
    private $alertForEm = false;
    private $alertForPM1 = false;
    private $alertForPM2 = false;
    private $alertForPM3 = false;
    private $alertThresholdInMinutes = 5;
    private $sendAlertMail = false;

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
    

    public function getAlertForEm()
    {
        return $this->alertForEm;
    }

    public function setAlertForEm($alertForEm)
    {
        $this->alertForEm = $alertForEm;
    }

    public function getAlertForPM1()
    {
        return $this->alertForPM1;
    }

    public function setAlertForPM1($alertForPM1)
    {
        $this->alertForPM1 = $alertForPM1;
    }

    public function getAlertForPM2()
    {
        return $this->alertForPM2;
    }

    public function setAlertForPM2($alertForPM2)
    {
        $this->alertForPM2 = $alertForPM2;
    }

    public function getAlertForPM3()
    {
        return $this->alertForPM3;
    }

    public function setAlertForPM3($alertForPM3)
    {
        $this->alertForPM3 = $alertForPM3;
    }
}
