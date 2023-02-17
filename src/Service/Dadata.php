<?php

/**
 * Метод init() следует вызвать один раз в начале,
 * затем можно сколько угодно раз вызывать отдельные методы clean(), suggest() и т.п.
 * и в конце следует один раз вызвать метод close().
 *
 * За счёт этого не создаются новые сетевые соединения на каждый запрос,
 * а переиспользуется существующее.
 */

namespace App\Service;

class Dadata
{
    private $clean_url = "https://cleaner.dadata.ru/api/v1/clean";
    private $suggest_url = "https://suggestions.dadata.ru/suggestions/api/4_1/rs";
    private $token;
    private $secret;
    private $handle;

    public function __construct($token, $secret)
    {
        $this->token = $token;
        $this->secret = $secret;
    }

    public function init()
    {
        $this->handle = curl_init();
        curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->handle, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Accept: application/json",
            "Authorization: Token " . $this->token,
            "X-Secret: " . $this->secret,
        ));
        curl_setopt($this->handle, CURLOPT_POST, 1);
    }

    public function clean($type, $value)
    {
        $url = $this->clean_url . "/$type";
        $fields = array($value);
        return $this->executeRequest($url, $fields);
    }

    public function findById($type, $fields)
    {
        $url = $this->suggest_url . "/findById/$type";
        return $this->executeRequest($url, $fields);
    }

    public function geolocate($lat, $lon, $count = 10, $radius_meters = 100)
    {
        $url = $this->suggest_url . "/geolocate/address";
        $fields = array(
            "lat" => $lat,
            "lon" => $lon,
            "count" => $count,
            "radius_meters" => $radius_meters
        );
        return $this->executeRequest($url, $fields);
    }

    public function iplocate($ip)
    {
        $url = $this->suggest_url . "/iplocate/address";
        $fields = array(
            "ip" => $ip
        );
        return $this->executeRequest($url, $fields);
    }

    public function suggest($type, $fields)
    {
        $url = $this->suggest_url . "/suggest/$type";
        return $this->executeRequest($url, $fields);
    }

    public function close()
    {
        curl_close($this->handle);
    }

    private function executeRequest($url, $fields)
    {
        curl_setopt($this->handle, CURLOPT_URL, $url);
        if ($fields != null) {
            curl_setopt($this->handle, CURLOPT_POST, 1);
            curl_setopt($this->handle, CURLOPT_POSTFIELDS, json_encode($fields));
        } else {
            curl_setopt($this->handle, CURLOPT_POST, 0);
        }
        $result = $this->exec();
        $result = json_decode($result, true);
        return $result;
    }

    private function exec()
    {
        $result = curl_exec($this->handle);
        $info = curl_getinfo($this->handle);
        if ($info['http_code'] == 429) {
            throw new \TooManyRequests();
        } elseif ($info['http_code'] != 200) {
            throw new \Exception('Request failed with http code ' . $info['http_code'] . ': ' . $result);
        }
        return $result;
    }
}
