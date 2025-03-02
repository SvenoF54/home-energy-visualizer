<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>


# https://github.com/bluerhinos/phpMQTT/tree/master

class ZendureService
{
    private $client_id;
    private $mqqtClient;
    private $timeoutInSec = 10;
    private $kvsTable;
    private $stateTopic;
    private $config;

    public function __construct() {
        $this->client_id = uniqid('nrgHomeVis_');  
        $this->kvsTable = KeyValueStoreTable::getInstance();
        $this->config = Configuration::getInstance()->zendure();        
    }

    public function connect() {
        $config = Configuration::getInstance()->zendure();        
        $this->mqqtClient = new Bluerhinos\phpMQTT($config->getServer(), $config->getPort(), $this->client_id);

        // Connect
        if ($config->getAppKey() == null || $config->getAppSecret() == null) {
            throw new Exception("Kein AppKey oder kein AppSecret in der local-config.php gesetzt.");
        }
        if (! $this->mqqtClient->connect(true, NULL, $config->getAppKey(), $config->getAppSecret())) {            
            throw new Exception("Fehler: Verbindung zum Zendure MQTT-Broker fehlgeschlagen.");
        }

        $this->stateTopic = $config->getAppKey()."/#";  // Typical AppKey/#
        $this->timeoutInSec = $config->getReadTimeInSec();
    }

    public function readDataFromMqqt() {
        $this->mqqtClient->subscribe([
            $this->stateTopic => ['qos' => 0, 'function' => [$this, 'handleMessage']]
        ]);

        $start = time();
        while (true) {
            if (! $this->mqqtClient->proc()) { break; }            
            if ((time() - $start) >= $this->timeoutInSec) { break; }
            
            usleep(100000); // wait 100 ms, to reduce CPU
        }
            
        if (! $this->mqqtClient->proc()) {
            throw new Exception("Error: MQTT-connect lost or a problem with the message.");
        }

        $this->mqqtClient->close();
    }

    public function handleMessage($topic, $msg) {
        $data = json_decode($msg, true);
        foreach ($this->getZendureKeys() as $key => $notice) {
            if ($data && isset($data[$key])) {
                $this->kvsTable->insertOrUpdate(KeyValueStoreScopeEnum::Zendure, $key, $data[$key], $notice);
            }
        }
    }

    public function getZendureKeys()
    {
        $keys = [
            $this->config->getKeySolarInput()   => "Ladestand über alle Batterien in %", 
            $this->config->getKeyAkkuCapacity() => "Aktuelle Solarleistung über alle Eingänge in W",
            $this->config->getKeyAkkuMax()      => "(Obere) Ladegrenze in % * 10"
        ];

        return $keys;

        // Possible interesting keys, but most of their values are not send very often
        $keys = [
            "electricLevel"    => "Ladestand über alle Batterien in %", 

            "solarInputPower"  => "Aktuelle Solarleistung über alle Eingänge in W", 
            "solarPower1"      => "Aktuelle Solarleistung Eingang PV 1 in W", 
            "solarPower2"      => "Aktuelle Solarleistung Eingang PV 2 in W", 

            "outputHomePower"  => "Aktuelle Leistungsabgabe ans 'Haus' in W nur im Terminmmodus",
            "gridInputPower"   => "Leistungsbezug aus dem Netz in W", 
            "inverseMaxPower"  => "Maximal zulässige Abgabeleistung ans 'Haus' / gestzl. Obergrenze in W", 
            "outputLimit"      => "Maximale Leistungsabgabe ans 'Haus' (Obergrenze) in W",

            "packInputPower"   => "Aktuelle Entladeleistung der Batterien in W", 
            "outputPackPower"  => "Aktuelle Ladeleistung der Batterien in W", 
            "remainOutTime"    => "Verbleibende Zeit bis Batterien entladen in min. ", 
            "remainInputTime"  => "Verbleibende Zeit bis Batterien geladen in min. ", 
            "packState"        => "Status über alle Batterien (0: Standby, 1: Laden, 2: Entladen)",
            "socSet"           => "(Obere) Ladegrenze in % * 10", 

            "sn"               => "Seriennummer"
           ];

        return $keys;
    }

    // Zendure only sends solarInputPower and electricLevel intime. So akku charging will be calculated
    public function prepareZendureDashboardData($isZeroFeedInActive, $substractPmxData)
    {
        $keySolarInput = $this->config->getKeySolarInput();
        $keyAkkuCapacity = $this->config->getKeyAkkuCapacity();
        $resultData = [];
        
        // Read latest Zendure data from DB
        $zendureKvsData = [$keySolarInput => 0, $keyAkkuCapacity => 0];
        foreach ($this->kvsTable->getRowsForScope(KeyValueStoreScopeEnum::Zendure) as $row) {
            $zendureKvsData[$row->getStoreKey()] = $row->getValue();
        }

        // Prepare result set
        $resultData = [];        
        // Current solar energy over all in W
        $resultData["solarInputPower"] = isset($zendureKvsData[$keySolarInput]) ? $zendureKvsData[$keySolarInput] : 0;
        // Current pack capacity over all in %        
        $resultData["akkuPackLevelPercent"] = isset($zendureKvsData[$keyAkkuCapacity]) ? $zendureKvsData[$keyAkkuCapacity] : 0;
        
        // Pack charge calculation, only if zero feed in is active because zendure data are not send very often
        if ($isZeroFeedInActive) {
            $currentAkkuCharge = $resultData["solarInputPower"] - $substractPmxData;                     // If another PM-Port has third party power
            $resultData["chargePackPowerCalc"] = $currentAkkuCharge > 0 ? $currentAkkuCharge : 0;        // Caclulated current pack charging W
            $resultData["isZendureChargeActive"] = $currentAkkuCharge > 0;                               // Pack charging active
            $resultData["dischargePackPowerCalc"] = $currentAkkuCharge < 0 ? -$currentAkkuCharge : 0;    // Caclulated current pack discharging W
            $resultData["isZendureDischargeActive"] = $currentAkkuCharge < 0;                            // Pack discharging active
        } else {
            $resultData["chargePackPowerCalc"] = 0;
            $resultData["isZendureChargeActive"] = false;
            $resultData["dischargePackPowerCalc"] = 0;
            $resultData["isZendureDischargeActive"] = false;
        }

        // Zendure production
        $resultData["productionTotal"] = $resultData["solarInputPower"] + $resultData["dischargePackPowerCalc"];
        $resultData["productionUsedInternal"] = $resultData["chargePackPowerCalc"];

        return $resultData;
    }

}
