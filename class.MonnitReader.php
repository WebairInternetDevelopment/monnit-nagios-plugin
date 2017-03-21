<?php

class MonnitReader
{
    private $authKey;
    private $baseUrl;

    public $data;

    /**
     * Instantiate a MonnitReader object with an auth key (optional)
     *
     *  @param String $authKey API authorization key (optional).
     */
    public function __construct($authKey = null)
    {
        $this->baseUrl = "https://www.imonnit.com/json";

        if (!empty($authKey)) {
            $this->authKey = $authKey;
        }
    }

    /**
     * Set an API authorization key.
     * @param String $authKey Authorization key to be set.
     */
    public function setAuthKey($authKey)
    {
        $this->authKey = $authKey;
    }

    /**
     * Pulls information on all sensors
     * and stores it in the data property
     *
     * @param int $sensorType MonnitApplicationID of sensor type (temperature, temp/humidity, door, etc)
     * @return null
     */
    public function readAllSensors($sensorType = null)
    {
        $url = "{$this->baseUrl}/SensorList/{$this->authKey}";

        if (!empty($sensorType)) {
            $url .= "?applicationID={$sensorType}";
        }

        try {
            $result = self::sendRequest($url);
        } catch (Exception $e) {
            throw $e;
        }


        $this->data = $result["Result"];
    }

    /**
     * Pulls information from a single sensor.
     * @param  String $sensorID ID of sensor to return information on
     * @return null
     */
    public function readSingleSensor($sensorID)
    {
        if (empty($sensorID)) {
            throw new Exception("No sensor ID string was passed.");
        }

        $url = "{$this->baseUrl}/SensorGet/{$this->authKey}?sensorID={$sensorID}";

        try {
            $result = self::sendRequest($url);
        } catch (Exception $e) {
            throw $e;
        }

        $this->data = $result["Result"];
    }

    /**
     * Uses cURL to send a request to the Monnit API.
     * @param  String $url Fully-constructed URL to send request to
     * @return Array      JSON-decoded response from API
     */
    private static function sendRequest($url)
    {
        if (!$url) {
            throw new Exception("Null or invalid request URL provided.");
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $contents = curl_exec($ch);

        if (false === $contents) {
            throw new Exception(curl_error($ch), curl_errno($ch));
        }

        if (curl_errno($ch)) {
            $err = curl_error($ch);
            throw new Exception("Failed to send request to Monnit API. cURL Error: {$err}");
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode != 200) {
            throw new Exception("Request to Monnit API was unsuccessful. HTTP Code: {$httpCode}");
        }

        curl_close($ch);


        try {
            $array = json_decode($contents, true);
        } catch (Exception $e) {
            throw new Exception("Couldn't decode JSON response from API. Error: {$e->getMessage()}");
        }

        // Since Monnit's API doesn't return 401 Unauthorized when
        // supplied with a valid key, we need to manually check the
        // response.

        $result = $array["Result"];
        if ($result == "Invalid Authorization Token") {
            throw new Exception("Monnit API responded with \"{$result}\". Please ensure your API key is valid and set correctly.\n");
        }

        if ($result == "Invalid SensorID") {
            throw new Exception("Monnit API responded with \"{$result}\". Please ensure the sensor ID you specified is valid.\n");
        }

        return $array;
    }


    /**
     * Average sensor temperatures obtained from previous request.
     * @param  Array $idArray Array of sensor ID's to average (optional).
     * @return Float          Average of temperatures.
     */
    public function averageTemps($idArray = false)
    {

        $arr = $this->data;
        if (empty($arr)) {
            throw new Exception("Data property is empty.");
        }

        $count = 0;
        $sum = 0;

        if (!empty($idArray)) {
            for ($i = 0; $i < count($idArray); $i++) {
                for ($j = 0; $j < count($arr); $j++) {
                    $curSensor = $arr[$j];
                    if($curSensor['SensorID'] == $idArray[$i]) {
                        $count++;
                        $sum += floatval($curSensor['CurrentReading']);
                    }
                }
            }
        } else {
            for($i = 0; $i < count($arr); $i++) {
                $cur = $arr[$i];
                $sum += floatval($cur['CurrentReading']);
            }

            $count = count($arr);
        }

        return floatval($sum / $count);
    }
}
