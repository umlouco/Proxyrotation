<?php

namespace MarioFlores\Proxy;

use MarioFlores\Proxy\Database;
use MarioFlores\Proxy\Scrape;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;

class Proxy {
    public $output = false; 
    public function getProxy() {
        $this->checkStock();
        $tested = false;
        while ($tested == false) {
            $proxy = Database::orderByRaw("RAND()")->first();
            $proxy = $proxy;
            $tested = $this->test($proxy);
            if ($tested == false) { 
                Database::destroy($proxy->id);
                $this->output('Deleted proxy '); 
            } else {
                return (array) $proxy;
            }
           
        }
    }

    private function checkStock() {
        $stock = Database::all()->count();
        if ($stock < 50) {
            $scrape = new Scrape;
            $scrape->scrape();
            Database::insert($scrape->proxys);
        }
    }

    function test($proxy) {
        $client = new Client([
            'headers' => [
                'User-Agent' => "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:47.0) Gecko/20100101 Firefox/47.0",
                'Accept-Language' => "en-US,en;q=0.5"
            ],
            'timeout' => 60,
            'cookies' => new \GuzzleHttp\Cookie\CookieJar,
            'http_errors' => false,
            'allow_redirects' => true
        ]);
        try {
            $response = $client->request('GET', 'http://agency-systems.com/home/proxys/', [
                'proxy' => 'tcp://' . $proxy->ip . ':' . $proxy->port,
                'timeout' => 60,
                'http_errors' => false
            ]);
            if ($response->getStatusCode() == 200) {
                $header = $response->getHeader('Content-Type');
                if ($header[0] != 'application/json') {
                    $this->output('Response was not json '); 
                    return false;
                } else {
                    $resposta = $response->getBody()->getContents();
                    if (empty($resposta)) {
                        $this->output('Response was empty '); 
                        return false;
                    } else {
                        $json = $resposta;
                        $resposta = json_decode($json);
                        if ($resposta->ip == '62.28.58.182') {
                            $this->output('Proxy is transparent '); 
                            return false;
                        } else {
                            $this->output('the proxy is ok'); 
                            return true;
                        }
                    }
                }
            } else {
                $this->output('Response was different then 2000 '); 
                return false;
            }
        } catch (RequestException $ex) {
            return false;
        }
    }
    
    function output($message){
        if($this->output === true){
            echo $message."<br /> \n"; 
        }
    }

}
