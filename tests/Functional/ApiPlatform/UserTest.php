<?php

namespace App\Tests\Functional\ApiPlatform;

use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

#[Group('functional')]

class UserTest extends WebTestCase
{

    public const REQUEST_HEADERS = [
        'HTTP_ACCEPT' => 'application/ld+json',
        'CONTENT_TYPE' => 'application/json'
    ];

    // public function testSomething(): void
    // {
    //     $client = static::createClient();
    //     $client->request('GET', '/api/users', [], [], self::REQUEST_HEADERS);
    //     // $crawler = $client->request('GET', '/api/');
    //     $this->assertResponseStatusCodeSame(200);

    //     // $this->assertResponseIsSuccessful();
    //     // $this->assertSelectorTextContains('h1', 'Hello World');
    // }

    public function testRegistration(): void
    {
        $context = [
            'email' => 'v.sobolev.1991@gmail.com', 
            'password' => 'string'
        ];
        $client = static::createClient();
        $client->request('POST', '/api/auth/register', [], [], self::REQUEST_HEADERS, json_encode($context));
        // dd(json_decode($client->getResponse()->getContent()));
        $this->assertResponseIsSuccessful();

    }

    // public function testAuthWithoutConfirmation(): void
    // {
        
    // }

    // public function testAuthWithConfirmation(): void
    // {
        
    // }

    public function testGetUsers(): void
    {
            $client = static::createClient();
            $client->request('GET', '/api/users', [], [], self::REQUEST_HEADERS);
            // dd(json_decode($client->getResponse()->getContent()));
            // $crawler = $client->request('GET', '/api/');
            $this->assertResponseStatusCodeSame(200);
    
    }

    // public function testChangePassword(): void
    // {
        
    // } 

    // public function testChangeRole(): void
    // {
        
    // } 
}
