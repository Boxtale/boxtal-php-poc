<?php
use Boxtal\BoxtalPhp\ApiClient;

/**
 * @author boxtal <api@boxtal.com>
 */
class ApiClientTest extends \PHPUnit_Framework_TestCase
{

    public function testMethod1()
    {
        $client = new ApiClient;
        $this->assertTrue($client->request() === 'hello world');
    }

}