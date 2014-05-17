<?php

namespace Clue\Socks;

use Exception;
use InvalidArgumentException;
use UnexpectedValueException;

class Client
{
    private $socksHost;

    private $socksPort;

    private $resolveLocal = true;

    private $protocolVersion = null;

    private $auth = null;

    public function __construct($socksHost, $socksPort)
    {
        $this->socksHost = $socksHost;
        $this->socksPort = $socksPort;
    }

    public function setResolveLocal($resolveLocal)
    {
        if ($this->protocolVersion === '4' && !$resolveLocal) {
            throw new UnexpectedValueException('SOCKS4 requires resolving locally. Consider using another protocol version or resolving locally');
        }
        $this->resolveLocal = $resolveLocal;
    }

    public function setProtocolVersion($version)
    {
        if ($version !== null) {
            $version = (string)$version;
            if (!in_array($version, array('4', '4a', '5'), true)) {
                throw new InvalidArgumentException('Invalid protocol version given');
            }
            if ($version !== '5' && $this->auth){
                throw new UnexpectedValueException('Unable to change protocol version to anything but SOCKS5 while authentication is used. Consider removing authentication info or sticking to SOCKS5');
            }
            if ($version === '4' && !$this->resolveLocal) {
                throw new UnexpectedValueException('Unable to change to SOCKS4 while resolving locally is turned off. Consider using another protocol version or resolving locally');
            }
        }
        $this->protocolVersion = $version;
    }

    /**
     * set login data for username/password authentication method (RFC1929)
     *
     * @param string $username
     * @param string $password
     * @link http://tools.ietf.org/html/rfc1929
     */
    public function setAuth($username, $password)
    {
        if (strlen($username) > 255 || strlen($password) > 255) {
            throw new InvalidArgumentException('Both username and password MUST NOT exceed a length of 255 bytes each');
        }
        if ($this->protocolVersion !== null && $this->protocolVersion !== '5') {
            throw new UnexpectedValueException('Authentication requires SOCKS5. Consider using protocol version 5 or waive authentication');
        }
        $this->auth = pack('C2', 0x01, strlen($username)) . $username . pack('C', strlen($password)) . $password;
    }

    public function unsetAuth()
    {
        $this->auth = null;
    }

    public function connect($host, $port)
    {

    }
}
