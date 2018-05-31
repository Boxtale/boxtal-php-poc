<?php
namespace Boxtal\Test\ApiClientTest;

use Boxtal\BoxtalPhp\ApiClient;

/**
 * @author boxtal <api@boxtal.com>
 */
class ApiClientTest extends \PHPUnit_Framework_TestCase
{

    public function testMethod1()
    {
        $client = new ApiClient('accessKey', 'secretKey');
        $this->assertTrue(get_class($client->restClient) === 'Boxtal\BoxtalPhp\RestClient');
    }
}
