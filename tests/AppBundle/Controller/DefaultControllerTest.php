<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Welcome to Symfony', $crawler->filter('#container h1')->text());
    }
  public function testGetAllPosts() {
    $client = static::createClient();
    $crawler = $client->request('GET','api/posts' );
    $this->assertEquals(200, $client->getResponse()->getStatusCode());
  }
  public function testGetFromPosts() {
    $client = static::createClient();
    $crawler = $client->request('GET','api/posts?from=2017-05-12&to=2017-12-31' );
    $this->assertEquals(200, $client->getResponse()->getStatusCode());
  }
  public function testGetAuthorPosts() {
    $client = static::createClient();
    $crawler = $client->request('GET','api/posts?author=Juju' );
    $this->assertEquals(200, $client->getResponse()->getStatusCode());
  }

}
