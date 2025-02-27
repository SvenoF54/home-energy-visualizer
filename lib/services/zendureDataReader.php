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
    private $timeoutInSec = 20;
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
            //$topic => ['qos' => 0, 'function' => 'ZendureDataReader::handleMessage']
            $topic => ['qos' => 0, 'function' => [$this, 'handleMessage']]
        ]);

        $start = time();
        echo "Request data...";
        while (true) {
            if (! $this->mqqtClient->proc()) {
                break;
            }
            
            if ((time() - $start) >= $this->timeoutInSec) {
                echo "Timeout reached.\n";
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
        echo "Nachricht empfangen auf $topic: $msg\n";
        
        $data = json_decode($msg, true);

        $keys = ["electricLevel", "outputHomePower", "packInputPower", "solarInputPower", "solarPower1", "solarPower2"];
        foreach ($keys as $key) {
            if ($data && isset($data[$key])) {
                $this->kvsTable->insertOrUpdate(KeyValueStoreScopeEnum::Zendure, $key, $data[$key]);
            }
        }
    }

}
