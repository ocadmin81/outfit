<?php

/**
 * Class OutfitLocation
 * address, latitude, longitude, locality, aal3, aal2, aal1
 */
class OutfitLocation {

    private $address = '';
    private $latitude = '';
    private $longitude = '';
    private $locality = '';
    private $aal3 = '';
    private $aal2 = '';
    private $aal1 = '';

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

    public function toJSON() {

        return json_encode(get_object_vars($this));
    }
}