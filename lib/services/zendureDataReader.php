<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>


# https://github.com/bluerhinos/phpMQTT/tree/master

class ZendureDataReader 
{
    private $server = 'mqtt-eu.zen-iot.com';     // MQTT-Serveradresse
    private $port = 1883;                     // MQTT-Port
    private $appKey = ZENDURE_APPKEY;           // Dein App-Key
    private $secret = ZENDURE_SECRET;           // Dein Secret    
    private $client_id;
    private $mqqtClient;
    private $timeoutInSec = 50;
    private $kvsTable;

    public function __construct() {
        $this->client_id = uniqid('nrgHomeVis_');  
        $this->kvsTable = KeyValueStoreTable::getInstance();
    }

    public function connect() {
        // Erstelle eine Instanz des MQTT-Clients
        $this->mqqtClient = new Bluerhinos\phpMQTT($this->server, $this->port, $this->client_id);

        // Versuche, eine Verbindung zum Broker herzustellen
        if (! $this->mqqtClient->connect(true, NULL, $this->appKey, $this->secret)) {
            echo "Fehler: Verbindung zum Zendure MQTT-Broker fehlgeschlagen.\n";
            exit(1);
        }
    }

    public function readDataFromMqqt() {
        // Abonniere das Thema
        $topic = ZENDURE_APPKEY.'/u67KGs48/state';  // TODO mittlerer Wert
        
        $this->mqqtClient->subscribe([
            $topic => ['qos' => 0, 'function' => [$this, 'handleMessage']]
        ]);

        $start = time();
        while (true) {
            if (! $this->mqqtClient->proc()) {
                break;
            }
            
            if ((time() - $start) >= $this->timeoutInSec) {
                break;
            }
            
            usleep(100000); // 100 ms warten, um CPU-Last zu reduzieren
        }
            
        if (! $this->mqqtClient->proc()) {
            echo "Error: MQTT-connect lost or a problem with the message.\n";
            echo "Message: " . $this->mqqtClient->getError() . "\n";
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
            "electricLevel"    =>"Ladestand über alle Batterien in %", 

            "solarInputPower"  => "Aktuelle Solarleistung über alle Eingänge in W", 
            "solarPower1"      => "Aktuelle Solarleistung Eingang PV 1 in W", 
            "solarPower2"      => "Aktuelle Solarleistung Eingang PV 2 in W", 

            "outputHomePower"  => "Aktuelle Leistungsabgabe ans 'Haus' in W nur im Terminmmodus",
            "gridInputPower"   => "Leistungsbezug aus dem Netz in W", 
            "inverseMaxPower"  => "Maximal zulässige Abgabeleistung ans 'Haus' / Gesetzliche Obergrenze in W", 
            "outputLimit"      => "Maximale Leistungsabgabe ans 'Haus' (Obergrenze) in W",

            "packInputPower"   => "Aktuelle Entladeleistung der Batterien in W", 
            "outputPackPower"  => "Aktuelle Ladeleistung der Batterien in W", 
            "remainOutTime"    => "Verbleibende Zeit bis Batterien entladen in min. Ob die Entladegrenze berücksichtigt wird, ist mir nicht bekannt", 
            "remainInputTime"  => "Verbleibende Zeit bis Batterien geladen in min. Ob die Ladegrenze berücksichtigt wird, ist mir nicht bekannt", 
            "packState"        => "Status über alle Batterien (0: Standby, 1: Mind. eine Batterie wird geladen, 2: Mind. eine Batterie wird entladen)",
            "socSet"           => "(Obere) Ladegrenze in % * 10", 

            "sn"               => "Seriennummer"
           ];

        return $keys;
    }

}
