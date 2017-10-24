<?php

namespace MarioFlores\Proxy;

use MarioFlores\Proxy\Database;
use MarioFlores\Proxy\Scrape;

class Proxy {

    public function getProxy() {
        $this->checkStock();
        $tested = false;
        $client = new Client();
        while ($tested == false) {
            $proxy = Database::orderByRaw("RAND()")->first();
            $proxy = $proxy; 
            $tested = $this->test($proxy); 
            if($tested == false){
                Database::destroy($proxy->id); 
            }
            else{
                return (array)$proxy; 
            }
        }
    }

    private function checkStock() {
        $stock = Database::all()->count();
        if ($stock < 50) {
            $scrape = new Scrape;
            $scrape->scrape();
            Database::insert($scrpe->proxys);
        }
    }

    function test($proxy) {
        try {
            $response = $client->request('GET', 'http://agency-systems.com/home/proxys/', [
                'proxy' => 'tcp://' . $proxy->ip . ':' . $proxy->port,
                'timeout' => 60,
                'http_errors' => false
            ]);
            if ($response->getStatusCode() == 200) {
                $header = $response->getHeader('Content-Type');
                if ($header[0] != 'application/json') {
                    return false;
                } else {
                    $resposta = $response->getBody()->getContents();
                    if (empty($resposta)) {
                        return false;
                    } else {
                        $json = $resposta;
                        $resposta = json_decode($json);
                        if ($resposta->ip == '62.28.58.182') {
                            return false;
                        } else {
                            return true;
                        }
                    }
                }
            } else {
                return false;
            }
        } catch (RequestException $ex) {
            return false;
        }
    }

}
