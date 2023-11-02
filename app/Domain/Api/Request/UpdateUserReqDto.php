<?php

declare(strict_types=1);

namespace App\Domain\Api\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class UpdateUserReqDto
{
    /**
     * @var array<string, mixed>
     */
    private array $setProperties = [];

    /**
     * @Assert\Email
     */
    private ?string $email = null;

    private ?string $name = null;
    private ?string $surname = null;
    private ?string $username = null;
    private ?string $password = null;

    public function __set(string $name, mixed $value)
    {
        $this->setProperties[$name] = true;
        $this->$name = $value;
    }

    public function __isset(string $name)
    {
        return isset($this->setProperties[$name]);
    }

    // Getters and setters for each property
    public function setEmail(?string $email): void
    {
        $this->email = $email;
        $this->__set('email', $email);
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
        $this->__set('name', $name);
    }

    public function setSurname(?string $surname): void
    {
        $this->surname = $surname;
        $this->__set('surname', $surname);
    }

    public function setUsername(?string $username): void
    {
        $this->username = $username;
        $this->__set('username', $username);
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
        $this->__set('password', $password);
    }

    // Getters for each property
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    // Method to check if a property was set
    public function wasSet(string $property): bool
    {
        return isset($this->setProperties[$property]);
    }
}
