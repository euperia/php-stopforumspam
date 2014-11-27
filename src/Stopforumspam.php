<?php

namespace Euperia;

use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\Mock;
use GuzzleHttp\Message\Response;

/**
 * Checks the email against www.stopforumspam.com
 * 
 * @see http://www.stopforumspam.com/usage
 * 
 * @example
 * $spam = new Stopforumspam();
 * $spam->addEmail('sibleyjscxk@hotmail.com');
 * if (false === $spam->check()) {
 *  // fail
 * }
 *
 * @see http://guzzle.readthedocs.org/en/latest/index.html
 *
 * @author Andrew Mccombe <andrew@euperia.com>
 */
class Stopforumspam 
{

    public $email;
    public $ipaddress;
    public $username;
    
    private $url;

    public function __construct() {
        $this->email = null;
        $this->ipaddress = null;
        $this->username = null;
        $this->url = 'http://www.stopforumspam.com';
    }

    /**
     * addEmail
     * Add a valid email address
     *
     * @param string $email
     * @throws \InvalidArgumentException
     * @return true
     */
    public function addEmail($email) {
        if (false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Email address invalid');
        }
        $this->email = $email;
        return true;
    }

    /**
     * addIpAddress
     * Add a valid IP address
     * 
     * @param string $ipaddress
     * @throws \InvalidArgumentException
     */
    public function addIpAddress($ipaddress) {

        if (false === filter_var($ipaddress, FILTER_VALIDATE_IP)) {
            throw new \InvalidArgumentException('IP address invalid');
        }
        $this->ipaddress = $ipaddress;
        return true;
    }

    /**
     * addUsername
     * Add a valid Username
     * 
     * @param string $username
     * @throws \InvalidArgumentException
     */
    public function addUsername($username) {

        if (strlen($username) < 3) {
            throw new \InvalidArgumentException('username invalid');
        }
        $this->username = $username;
        return true;
    }

    /**
     * check
     * Performs the call and checks the result.
     * @todo - expand this to check the confidence and number seen result
     * 
     * @param object GuzzleHttp\Client
     * @return boolean 
     * @throws \LogicException
     */
    public function check(Client $client) {
        
        if (null == $this->email && null === $this->ipaddress && null == $this->username) {
            throw new \LogicException('All parameters are null');
        }
        
        $params = array();
        $params['f'] = 'json';
        if (null !== $this->email) {
            $params['email'] = $this->email;
        }
        if (null !== $this->ipaddress) {
            $params['ip'] = $this->ipaddress;
        }
        if (null !== $this->username) {
            $params['username'] = $this->username;
        }
        
        $url = $this->url . '/api?' . http_build_query($params);
        
        try {
            $request = $client->createRequest('GET', $url);
            $response = $client->send($request);

            if (200 == $response->getStatusCode()) {
                $result = json_decode($response->getBody());
                return (bool) $result->email->appears;
            } else {
                throw new \Exception('Service did not return a 200');
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

}
