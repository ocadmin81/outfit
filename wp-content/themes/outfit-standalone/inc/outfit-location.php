<?php

/**
 * Class OutfitLocation
 * address, latitude, longitude, locality, aal3, aal2, aal1
 */
class OutfitLocation {

    const LOCALITY = 'LOCALITY';
    const AREA3 = 'AREA3';
    const AREA2 = 'AREA2';
    const AREA1 = 'AREA1';

    private $address = '';
    private $latitude = '';
    private $longitude = '';
    private $locality = '';
    private $aal3 = '';
    private $aal2 = '';
    private $aal1 = '';
    private $lastSearchKeyBy = '';

    public function __construct($address, $longitude, $latitude, $tags) {
        $this->address = $address;
        $this->longitude = $longitude;
        $this->latitude = $latitude;

        if (isset($tags['locality'])) {
            $this->locality = $tags['locality'];
        }
        if (isset($tags['aal3'])) {
            $this->aal3 = $tags['aal3'];
        }
        if (isset($tags['aal2'])) {
            $this->aal2 = $tags['aal2'];
        }
        if (isset($tags['aal1'])) {
            $this->aal1 = $tags['aal1'];
        }
    }

    public function suggestSearchKey() {
        if (!empty($this->locality)) {
            $this->lastSearchKeyBy = self::LOCALITY;
            return $this->locality;
        }
        if (!empty($this->aal3)) {
            $this->lastSearchKeyBy = self::AREA3;
            return $this->aal3;
        }
        if (!empty($this->aal2)) {
            $this->lastSearchKeyBy = self::AREA2;
            return $this->aal2;
        }
        if (!empty($this->aal1)) {
            $this->lastSearchKeyBy = self::AREA1;
            return $this->aal1;
        }
        return '';
    }

    public function getLastSearchKeyBy() {
        return $this->lastSearchKeyBy;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param string $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param string $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * @return string
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * @param string $locality
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;
    }

    /**
     * @return string
     */
    public function getAal3()
    {
        return $this->aal3;
    }

    /**
     * @param string $aal3
     */
    public function setAal3($aal3)
    {
        $this->aal3 = $aal3;
    }

    /**
     * @return string
     */
    public function getAal2()
    {
        return $this->aal2;
    }

    /**
     * @param string $aal2
     */
    public function setAal2($aal2)
    {
        $this->aal2 = $aal2;
    }

    /**
     * @return string
     */
    public function getAal1()
    {
        return $this->aal1;
    }

    /**
     * @param string $aal1
     */
    public function setAal1($aal1)
    {
        $this->aal1 = $aal1;
    }

    public function isValid() {

        if (empty($this->address) || empty($this->latitude) || empty($this->longitude)) {
            return false;
        }
        if (empty($this->locality) && empty($this->aal3) && empty($this->aal2) && empty($this->aal1)) {
            return false;
        }
        return true;
    }

    public function toString() {

        //????????? ????
        $arr = (array) get_object_vars($this);
        foreach ($arr as $k => $v) {
            if ($k != 'latitude' && $k != 'longitude') {
                $arr[$k] = outfitEncodeUnicodeString($v);
            }
        }
        //return json_encode($arr);
        return json_encode($arr, JSON_HEX_APOS);
    }

    public static function toAssoc($jsonString) {

        $json = json_decode($jsonString, true);
        if (!$json) return array();

        foreach ($json as $k => $v) {
            if ($k != 'latitude' && $k != 'longitude') {
                $json[$k] = outfitDecodeUnicodeString($v);
            }
        }

        return $json;
    }

    public static function createFromJSON($jsonString) {

        $json = json_decode($jsonString, true);
        if (!$json) return null;

        foreach ($json as $k => $v) {
            if ($k != 'latitude' && $k != 'longitude') {
                $json[$k] = outfitDecodeUnicodeString($v);
            }
        }
        $postAddress = (isset($json['address'])? $json['address'] : '');
        $postLatitude = (isset($json['latitude'])? $json['latitude'] : '');
        $postLongitude = (isset($json['longitude'])? $json['longitude'] : '');

        $postLocality = (isset($json['locality'])? $json['locality'] : '');
        $postArea1 = (isset($json['aal1'])? $json['aal1'] : '');
        $postArea2 = (isset($json['aal2'])? $json['aal2'] : '');
        $postArea3 = (isset($json['aal3'])? $json['aal3'] : '');

        return new OutfitLocation($postAddress, $postLongitude, $postLatitude, [
            'locality' => $postLocality,
            'aal3' => $postArea3,
            'aal2' => $postArea2,
            'aal1' => $postArea1
        ]);
    }
}