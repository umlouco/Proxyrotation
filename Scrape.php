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
            $this->getxroxy();
        } catch (Exception $ex) {
            $this->errors[] = $ex->getMessage();
        }
    }

    public function setGuzzle() {
        $this->setHeaders();
        $this->client = new Client([
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

    function getxroxyRows($rows) {

        foreach ($rows as $row) {
            $td = new Crawler($row);
            $this->proxys[] = array(
                'ip' => trim($td->filter('td')->eq(1)->text()),
                'port' => trim($td->filter('td')->eq(2)->text())
            );
        }
    }

    function getxroxy() {
        $pages = true;
        $page = 0;
        while ($pages) {
            try {
                $response = $this->client->request('GET', 'http://www.xroxy.com/proxylist.php?port=&type=All_http&ssl=&country=&latency=&reliability=7500&sort=reliability&desc=true&pnum=' . $page . '#table');
                $html = new Crawler($response->getBody()->getContents());
                $rows = $html->filter('.row1');
                if ($rows->count() > 0) {
                    $pages = $this->getxroxyRows($rows);
                } else {
                    $pages = false;
                }
                $rows = $html->filter('.row0');
                if ($rows->count() > 0) {
                    $pages = $this->getxroxyRows($rows);
                } else {
                    $pages = false;
                }
            } catch (RequestException $ex) {
                $this->errors[] = $ex->getMessage();
                $pages = false; 
            }
        }
    }

    function blackhatUrls() {
        return array('http://proxy.ipcn.org/proxylist2.html',
            'http://www.ip-adress.com/proxy_list/?k=type',
            'https://freevpn.ninja/free-proxy',
            'http://mail.nntime.com/',
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
            'http://amovmh.xyz/proxy/aliveraw.txt',
            'http://fineproxy.org/freshproxy/',
            'https://proxy-list.org/english/index.php?p=1',
            'https://proxy-list.org/english/index.php?p=2',
            'https://proxy-list.org/english/index.php?p=3',
            'https://proxy-list.org/english/index.php?p=4',
            'https://proxy-list.org/english/index.php?p=5',
            'https://proxy-list.org/english/index.php?p=6',
            'https://proxy-list.org/english/index.php?p=7',
            'https://proxy-list.org/english/index.php?p=8',
            'https://proxy-list.org/english/index.php?p=9',
            'http://www.httptunnel.ge/ProxyListForFree.aspx'
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
        for ($i = 0; $i < 3; $i++) {
            try {
                $response = $this->client->request('GET', 'https://hidester.com/proxydata/php/data.php?mykey=data&offset='.$i.'&limit=10&orderBy=latest_check&sortOrder=DESC&country=&port=&type=undefined&anonymity=undefined&ping=undefined&gproxy=2');
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

}
