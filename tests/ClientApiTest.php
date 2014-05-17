<?php

use Clue\Socks\Client;

class ClientApiTest extends TestCase
{
    private $client;

    public function setUp()
    {
        $this->client = new Client('127.0.0.1', 9050);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidAuthInformation()
    {
        $this->client->setAuth(str_repeat('a', 256), 'test');
    }

    /**
     * @expectedException UnexpectedValueException
     * @dataProvider providerInvalidAuthVersion
     */
    public function testInvalidAuthVersion($version)
    {
        $this->client->setAuth('username', 'password');
        $this->client->setProtocolVersion($version);
    }

    public function providerInvalidAuthVersion()
    {
        return array(array('4'), array('4a'));
    }

    public function testValidAuthVersion()
    {
        $this->client->setAuth('username', 'password');
        $this->assertNull($this->client->setProtocolVersion(5));
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testInvalidCanNotSetAuthenticationForSocks4()
    {
        $this->client->setProtocolVersion(4);
        $this->client->setAuth('username', 'password');
    }

    public function testUnsetAuth()
    {
        // unset auth even if it's not set is valid
        $this->client->unsetAuth();

        $this->client->setAuth('username', 'password');
        $this->client->unsetAuth();
    }

    /**
     * @dataProvider providerValidProtocolVersion
     */
    public function testValidProtocolVersion($version)
    {
        $this->assertNull($this->client->setProtocolVersion($version));
    }

    public function providerValidProtocolVersion()
    {
        return array(array('4'), array('4a'), array('5'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidProtocolVersion()
    {
        $this->client->setProtocolVersion(3);
    }

    public function testValidResolveLocal()
    {
        $this->assertNull($this->client->setResolveLocal(false));
        $this->assertNull($this->client->setResolveLocal(true));
        $this->assertNull($this->client->setProtocolVersion('4'));
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testInvalidResolveRemote()
    {
        $this->client->setProtocolVersion('4');
        $this->client->setResolveLocal(false);
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testInvalidResolveRemoteVersion()
    {
        $this->client->setResolveLocal(false);
        $this->client->setProtocolVersion('4');
    }
}
