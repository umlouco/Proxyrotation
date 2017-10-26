<?php

namespace MarioFlores\Proxy;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DomCrawler\Crawler;

class Scrape {

    private $client;
    public $proxys;
    public $errors;

    public function scrape() {
        try {
            $this->setGuzzle();
            $this->getBlackhat();
            $this->getHidster();
            $this->httptunnel(); 
            
        } catch (Exception $ex) {
            $this->errors[] = $ex->getMessage();
        }
    }

    public function setGuzzle() {
        $this->setHeaders();
        return new Client([
            'headers' => $this->setHeaders(),
            'timeout' => 60,
            'cookies' => new \GuzzleHttp\Cookie\CookieJar,
            'http_errors' => false,
            'allow_redirects' => true
        ]);
    }

    private function setHeaders() {
        return [
            'User-Agent' => "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:47.0) Gecko/20100101 Firefox/47.0",
            'Accept-Language' => "en-US,en;q=0.5"
        ];
    }

    function blackhatUrls() {
        return array(
            'http://www.ip-adress.com/proxy_list/?k=type',
            'https://freevpn.ninja/free-proxy',
            'http://mail.nntime.com/',
            'http://mail.nntime.com/proxy-list-01.htm',
            'http://mail.nntime.com/proxy-list-02.htm',
            'http://mail.nntime.com/proxy-list-03.htm',
            'http://mail.nntime.com/proxy-list-04.htm',
            'http://mail.nntime.com/proxy-list-05.htm',
            'http://mail.nntime.com/proxy-list-06.htm',
            'http://mail.nntime.com/proxy-list-07.htm',
            'http://mail.nntime.com/proxy-list-08.htm',
            'http://mail.nntime.com/proxy-list-09.htm',
            'http://mail.nntime.com/proxy-list-10.htm',
            'http://mail.nntime.com/proxy-list-11.htm',
            'http://mail.nntime.com/proxy-list-12.htm',
            'http://fineproxy.org/freshproxy/',
            'https://proxy-list.org/english/index.php?p=1',
            'https://proxy-list.org/english/index.php?p=2',
            'https://proxy-list.org/english/index.php?p=3',
            'https://proxy-list.org/english/index.php?p=4',
            'https://proxy-list.org/english/index.php?p=5',
            'https://proxy-list.org/english/index.php?p=6',
            'https://proxy-list.org/english/index.php?p=7',
            'https://proxy-list.org/english/index.php?p=8',
            'https://proxy-list.org/english/index.php?p=9'
        );
    }

    function getBlackhat() {

        try {
            $urls = $this->blackhatUrls();
            foreach ($urls as $url) {
                $html = file_get_contents($url);
                preg_match_all('~\b[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}:[0-9]{1,5}\b~', $html, $out);
                if (!empty($out)) {
                    foreach ($out[0] as $ip) {
                        $data = explode(':', $ip);
                        if ($data[1] == '80' or $data[1] == '8080') {
                            $this->proxys[] = array(
                                'ip' => $data[0],
                                'port' => $data[1]
                            );
                        }
                    }
                }
            }
        } catch (Exception $ex) {
            $this->errors[] = $ex->getMessage();
        }
    }

    function getHidster() {
        $client = $this->setGuzzle();
        for ($i = 0; $i < 3; $i++) {
            try {
                $response = $client->request('GET', 'https://hidester.com/proxydata/php/data.php?mykey=data&offset=' . $i . '&limit=10&orderBy=latest_check&sortOrder=DESC&country=&port=&type=undefined&anonymity=undefined&ping=undefined&gproxy=2');
                $lista = json_decode($response->getBody()->getContents());
                if (!empty($lista)) {
                    foreach ($lista as $list) {
                        if ($list->PORT = 80 or $list->PORT) {
                            $this->proxys[] = array(
                                'ip' => $list->IP,
                                'port' => $list->PORT
                            );
                        }
                    }
                }
            } catch (RequestException $ex) {
                $this->errors[] = $ex->getMessage();
            }
        }
    }

    function httptunnel() {
        $html = file_get_contents('http://www.httptunnel.ge/ProxyListForFree.aspx');
        preg_match_all('~\b[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}:[0-9]{1,5}\b~', $html, $out);
        if (!empty($out)) {
            foreach ($out[0] as $ip) {
                $data = explode(':', $ip);
                if ($data[1] == '80' or $data[1] == '8080') {
                    $this->proxys[] = array(
                        'ip' => $data[0],
                        'port' => $data[1]
                    );
                }
            }
        }
    }

}
