<?php

use Clue\Socks\Socks4;
use Clue\Socks\Socks4a;
use Clue\Socks\Socks5;

class HttpTest extends TestCase
{
    public function testSocks4()
    {
        $socks = new Socks4(9050);
        $this->httpHostname($socks);
        $this->httpIpv4($socks);
    }

    public function testSocks4a()
    {
        $socks = new Socks4a(NULL, 9050);
        $this->httpHostname($socks);
        $this->httpIpv4($socks);
    }

    public function testSocks5()
    {
        $socks = new Socks5('localhost', 9050);
        $this->httpHostname($socks);
        $this->httpIpv4($socks);
    }

    protected function httpHostname($socks)
    {
        return $this->http($socks, 'www.google.com');
    }

    protected function httpIpv4($socks)
    {
        return $this->http($socks, gethostbyname('www.google.com'));
    }

    protected function http($socks, $host)
    {
        $fp = $socks->connect($host, 80);

        fwrite($fp, "GET / HTTP/1.0\r\nHost: '.$host.'\r\n\r\n");

        $ret = stream_get_contents($fp);

        // var_dump($ret);

        $this->assertStringStartsWith('HTTP/1.', $ret);

        return $ret;
    }
}
