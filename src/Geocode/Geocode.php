<?php
/**
 * Created by PhpStorm.
 * User: rafael
 * Date: 09/11/18
 * Time: 14:36
 */

namespace rafaeldsb\Geocode;

class Geocode
{
    public static $key = "AIzaSyDGosQgtKtpJwcgypaa4PjUyZMsH5Rzjs0";
    public $baseUrl = "https://maps.google.com/maps/api/geocode/json?";

    protected $url;
    protected $components;
    protected $address;

    protected $resource;

    protected $lat;
    protected $lng;

    public $street;
    public $number;
    public $district;
    public $city;
    public $state;

    protected $addressValid = ['street', 'number', 'district', 'city', 'state'];

    public function setComponents($options){
        $this->components = "&components=";
        foreach ($options as $key => $option){
            $this->components .= $key . ':' . $option . '|';
        }
        return $this;
    }

    public function setAddress($address){
        $this->address = "&address=";
        foreach ($address as $key => $option){
            if(in_array($key, $this->addressValid)){
                $this->$key = $option;
            } else {
                throw new \Exception("Parâmetro inválido, `{$key}` não existe como parâmetro.");
            }
        }
        $this->address .= urlencode("{$this->street},{$this->number}, {$this->district}, {$this->city}, {$this->state}");

        return $this;
    }

    public function getUrl(){
        if($this->url)
            return $this->url;
        return new \Exception("Url não gerada, use o método `makeUrl()` para gerá-lo");
    }

    protected function getResource(){
        $json = json_decode(file_get_contents($this->getUrl()));
        if(isset($json->results[0])){
            $this->resource = $json->results[0];
        }
        return $this;
    }

    public function make(){
        $this->url = $this->baseUrl . 'key=' . self::$key . $this->components . $this->address;
        $this->getResource();

        return $this;
    }


    public function getLatitudeLongitude(){
        $this->lat = $this->resource->geometry->location->lat;
        $this->lng = $this->resource->geometry->location->lng;

        return [
            'lat' => $this->lat,
            'lng' => $this->lng,
        ];
    }

}
