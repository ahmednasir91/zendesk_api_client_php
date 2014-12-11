<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * OAuthClients test class
 */
class OAuthClientsTest extends BasicTest {

    public function testCredentials() {
        $this->assertEquals($_ENV['SUBDOMAIN'] != '', true, 'Expecting _ENV[SUBDOMAIN] parameter; does phpunit.xml exist?');
        $this->assertEquals($_ENV['TOKEN'] != '', true, 'Expecting _ENV[TOKEN] parameter; does phpunit.xml exist?');
        $this->assertEquals($_ENV['USERNAME'] != '', true, 'Expecting _ENV[USERNAME] parameter; does phpunit.xml exist?');
    }

    public function testAuthToken() {
        $this->client->setAuth('token', $this->token);
        $requests = $this->client->tickets()->findAll();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testCreate() {
        $client = $this->client->oauthClients()->create(array(
            'name' => 'Test Client',
            'identifier' => md5(time()),
            'user_id' => 454094082
        ));
        $this->assertEquals(is_object($client), true, 'Should return an object');
        $this->assertEquals(is_object($client->client), true, 'Should return an object called "client"');
        $this->assertGreaterThan(0, $client->client->id, 'Returns a non-numeric id for client');
        $this->assertEquals($client->client->name, 'Test Client', 'Name of test client does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $id = $client->client->id;
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testAll($stack) {
        $clients = $this->client->oauthClients()->findAll();
        $this->assertEquals(is_object($clients), true, 'Should return an object');
        $this->assertEquals(is_array($clients->clients), true, 'Should return an object containing an array called "clients"');
        $this->assertGreaterThan(0, $clients->clients[0]->id, 'Returns a non-numeric id for clients[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testFind($stack) {
        $id = array_pop($stack);
        $client = $this->client->oauthClient($id)->find();
        $this->assertEquals(is_object($client), true, 'Should return an object');
        $this->assertGreaterThan(0, $client->client->id, 'Returns a non-numeric id for client');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(array $stack) {
        $id = array_pop($stack);
        $client = $this->client->oauthClient($id)->update(array(
            'name' => 'New Client Name'
        ));
        $this->assertEquals(is_object($client), true, 'Should return an object');
        $this->assertEquals(is_object($client->client), true, 'Should return an object called "client"');
        $this->assertGreaterThan(0, $client->client->id, 'Returns a non-numeric id for client');
        $this->assertEquals($client->client->name, 'New Client Name', 'Name of test client does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testDelete(array $stack) {
        $id = array_pop($stack);
        $this->assertGreaterThan(0, $id, 'Cannot find a client id to test with. Did testCreate fail?');
        $topic = $this->client->oauthClient($id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}

?>
