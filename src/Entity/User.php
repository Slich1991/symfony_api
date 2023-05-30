<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use App\State\UserPasswordHasher;
use App\State\UserRestorePassword;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => 'user:read']),
        new Put(
            processor: UserPasswordHasher::class,
            denormalizationContext: ['groups' => 'user:pass'],
            normalizationContext: ['groups' => 'user:read']
        ),
        new Patch(
            denormalizationContext: ['groups' => 'user:role'],
            normalizationContext: ['groups' => 'user:read']
        ),
        new GetCollection(normalizationContext: ['groups' => 'user:list']),
        new Post(
            processor: UserPasswordHasher::class,
            normalizationContext: ['groups' => 'user:read'],
            denormalizationContext: ['groups' => 'user:write'],
            openapiContext: ['tags' => ['Auth']],
            uriTemplate: '/auth/register'
        ),
        new Patch(
            processor: UserRestorePassword::class,
            denormalizationContext: ['groups' => 'user:forgot'],
            openapiContext: ['tags' => ['Auth']],
            uriTemplate: '/auth/forgot'
        ),
        new Post(
            processor: UserRestorePassword::class,
            denormalizationContext: ['groups' => 'user:restore'],
            openapiContext: ['tags' => ['Auth']],
            uriTemplate: '/auth/restore'
        ),
        new Delete(denormalizationContext: ['groups' => 'user:delete']),
    ],
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read', 'user:list'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['user:read', 'user:list', 'user:write', 'user:forgot'])]
    private ?string $email = null;

    #[ORM\Column(type: 'boolean', options: ["default" => false])]
    #[Groups(['user:read', 'user:list'])]
    private $emailVerify = false;

    #[ORM\Column]
    #[Groups(['user:role'])]
    private array $roles = [];

    #[Groups(['user:restore'])]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $restoreToken;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(['user:pass', 'user:write', 'user:restore'])]
    private ?string $password = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
    
    public function getEmailVerify(): ?bool
    {
        return $this->emailVerify;
    }

    public function setEmailVerify(bool $emailVerify): self
    {
        $this->emailVerify = $emailVerify;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    #[Groups(['user:read', 'user:list'])]
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getRestoreToken(): ?string
    {
        return $this->restoreToken;
    }

    public function setRestoreToken(string $restoreToken): self
    {
        $this->restoreToken = $restoreToken;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
