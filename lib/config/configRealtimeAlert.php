<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

class ConfigRealtimeAlert
{
    private $alertForEnergyType = array();
    private $alertThresholdInMinutes = 5;
    private $sendAlertMail = false;
    private $sendAlertMailEveryXMinutes = 30;

    public function getAlertThresholdInMinutes() { return $this->alertThresholdInMinutes; }
    public function setAlertThresholdInMinutes($alertThresholdInMinutes) { $this->alertThresholdInMinutes = $alertThresholdInMinutes; }

    public function getSendAlertMail() { return $this->sendAlertMail; }
    public function setSendAlertMail($sendAlertMail) { $this->sendAlertMail = $sendAlertMail; }
    
    public function setSendAlertMailEveryXMinutes($sendAlertMailEveryXMinutes) { $this->sendAlertMailEveryXMinutes = $sendAlertMailEveryXMinutes; }
    public function getSendAlertMailEveryXMinutes() { return $this->sendAlertMailEveryXMinutes; }

    public function shouldAlertForEnergyType(EnergyTypeEnum $energyType) { return array_key_exists($energyType->value, $this->alertForEnergyType) ? $this->alertForEnergyType[$energyType->value] : false; }
    public function setShouldAlertForEnergyType(EnergyTypeEnum $energyType, $alertForEm) { $this->alertForEnergyType[$energyType->value] = $alertForEm; }

}
