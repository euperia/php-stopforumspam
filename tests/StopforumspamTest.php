<?php

namespace Euperia;

use Euperia\Stopforumspam;
use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\Mock;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;

/**
 * @property Stopforumspam $object
 */
class StopforumspamTest extends \PHPUnit_Framework_TestCase
{
    
    private $object;
    
    protected function setUp()
    {
        $this->object = new Stopforumspam();
    }

    protected function tearDown()
    {
    }

    // tests
    public function testAddEmailisTrue()
    {
        $email = 'test@example.com';
        $this->assertTrue($this->object->addEmail($email));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testAddEmailThrowsInvalidArgumentException() {
        $email = '123456';
        $this->object->addEmail($email);
    }
    
    public function testAddEmailMatches() {
        $email = 'test@example.com';
        $this->object->addEmail($email);
        $this->assertSame($email, $this->object->email);
    }
    
    public function testAddIpAddressIsTrue() {
        $ip = '192.168.56.103';
        $this->assertTrue($this->object->addIpAddress($ip));
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testAddIpThrowsInvalidArgumentException() {
        $ip = '192168.56.103';
        $this->object->addIpAddress($ip);
    }
    
    public function testAddIpAddressMatches() {
        $ip = '192.168.56.103';
        $this->object->addIpAddress($ip);
        $this->assertSame($ip, $this->object->ipaddress);
    }
    
    
    public function testAddUsernameIsTrue() {
        $username = 'bob1234';
        $this->assertTrue($this->object->addUsername($username));
    }
    
    public function testAddUsernameMatches() {
        $username = 'bob1234';
        $this->object->addUsername($username);
        $this->assertSame($username, $this->object->username);
    }
    
    // now test the meat of the class.  Need a Mock Object here.
    
    
    /**
     * @expectedException LogicException
     */
    public function testCheckThrowsLogicException() {
        $request = new Client();
        $this->object->check($request);
    }
    
    /**
     * @expectedException Exception
     */
    public function testCheckThrowsExceptionIfNot200() {

        $mockResponse = new Response(404);
        $mockResponse->setHeaders(array(
            'Content-Type' => 'text/html',
            'Connection' =>  'keep-alive',
            'Keep-Alive' => 'timeout=45',
            'Vary' => 'Accept-Encoding'
        ));

        $mockResponse->setBody(Stream::factory("Page not found"));

        $mock = new Mock([$mockResponse]);

        $client = new Client();
        $client->getEmitter()->attach($mock);
        
       // set up the object
       $this->object->addEmail('andrew@iweb.co.uk');
       $this->object->check($client);
    }
    
    
    public function testCheckFalseForEmailNotListed() {

        $mockResponse = new Response(200);
        $mockResponse->setHeaders(array(
            "Server" => " nginx",
            "Date" => " Fri, 04 Oct 2013 08:13:21 GMT",
            "Content-Type" => " text/html; charset=UTF-8",
            "Connection" => " keep-alive",
            "Keep-Alive" => " timeout=45",
            "Cache-Control" => " no-cache, must-revalidate",
            "Expires" => "Sat, 26 Jul 1997 05:00:00 GMT",
            "Content-Length" => "49"
        ));

        $mockResponse->setBody(
            Stream::factory('{"success":1,"email":{"frequency":0,"appears":0}}')
        );

        $mock = new Mock([$mockResponse]);
        
        $client = new Client();
        $client->getEmitter()->attach($mock);


       // set up the object
       $this->object->addEmail('test@example.com');
       $this->assertFalse($this->object->check($client));
       
    }
    
    public function testCheckFalseForEmailListed() {

        $mockResponse = new Response(200);
        $mockResponse->setHeaders(array(
            "Server" => " nginx",
            "Date" => " Fri, 04 Oct 2013 08:13:21 GMT",
            "Content-Type" => " text/html; charset=UTF-8",
            "Connection" => " keep-alive",
            "Keep-Alive" => " timeout=45",
            "Cache-Control" => " no-cache, must-revalidate",
            "Expires" => "Sat, 26 Jul 1997 05:00:00 GMT",
            "Content-Length" => "49"
        ));

        $mockResponse->setBody(
            Stream::factory(
                '{"success":1,"email":{"lastseen":"2013-10-04 08:13:15","frequency":5835,"appears":1,"confidence":99.92}}'
            )
        );

        $mock = new Mock([$mockResponse]);

        $client = new Client();
        $client->getEmitter()->attach($mock);
        
       // set up the object
       $this->object->addEmail('ochobscheigyedynne@outlook.com');
       $this->assertTrue($this->object->check($client));
       
    }
    
}
