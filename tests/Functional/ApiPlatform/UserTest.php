<?php

namespace App\Tests\Functional\ApiPlatform;

use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;

#[Group('functional')]

class UserTest extends WebTestCase
{
    // dd(json_decode($client->getResponse()->getStatusCode()));
    public const REQUEST_HEADERS = [
        'HTTP_ACCEPT' => 'application/ld+json',
        'CONTENT_TYPE' => 'application/json'
    ];

   public const CONTEXT = [
        'email' => 'v.sobolev.1991@gmail.com', 
        'password' => 'string3'
    ];

    public function testRegistration(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/auth/register', [], [], self::REQUEST_HEADERS, json_encode(self::CONTEXT));
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testAuthWithoutConfirmation(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/auth/login', [], [], self::REQUEST_HEADERS, json_encode(self::CONTEXT));
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testAuthWithConfirmation(): void
    {
        $client = static::createClient();
        $this->userUpdate('emailVerify', 1);
        $client->request('POST', '/api/auth/login', [], [], self::REQUEST_HEADERS, json_encode(self::CONTEXT));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testGetUsersWithoutRole(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getUser());
        $client->request('GET', '/api/users', [], [], self::REQUEST_HEADERS);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testGetUsersWithRole(): void
    {
        $client = static::createClient();
        $this->userUpdate('roles', array('ROLE_ADMIN'));
        $client->loginUser($this->getUser());
        $client->request('GET', '/api/users', [], [], self::REQUEST_HEADERS);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    // public function testChangePassword(): void
    // {
        
    // } 

    // public function testChangeRole(): void
    // {
        
    // }

    private function userUpdate(string $field, $value): void
    {
        $container = static::getContainer();
        $container->get(UserRepository::class)->update($field, $value, 'email', self::CONTEXT['email']);

    }

    private function getUser(): User
    {
        $container = static::getContainer();
        $user = $container->get(UserRepository::class)->findOneBy(['email' => self::CONTEXT['email']]);
        return $user;
    }
}