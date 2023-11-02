<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Model\Database\Entity\AbstractEntity;
use App\Model\Database\Entity\TCreatedAt;
use App\Model\Database\Entity\TId;
use App\Model\Database\Entity\TUpdatedAt;
use App\Model\Exception\Logic\InvalidArgumentException;
use App\Model\Utils\DateTime;
use Doctrine\ORM\Mapping as ORM;
use Nette\Utils\Random;

/**
 * User entity
 *
 * @ORM\Entity(repositoryClass="UserRepository")
 * @ORM\Table(name="`user`")
 * @ORM\HasLifecycleCallbacks
 */
class User extends AbstractEntity
{
    use TId;
    use TCreatedAt;
    use TUpdatedAt;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_USER = 'user';

    public const STATE_FRESH = 1;
    public const STATE_ACTIVATED = 2;
    public const STATE_BLOCKED = 3;

    public const STATES = [self::STATE_FRESH, self::STATE_BLOCKED, self::STATE_ACTIVATED];

    /**
     * @ORM\Column(type="string", length=255, nullable=FALSE, unique=false) 
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=FALSE, unique=false) 
     */
    private string $surname;

    /**
     * @ORM\Column(type="string", length=255, nullable=FALSE, unique=TRUE) 
     */
    private string $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=FALSE, unique=TRUE) 
     */
    private string $username;

    /**
     * @ORM\Column(type="integer", length=10, nullable=FALSE) 
     */
    private int $state;

    /**
     * @ORM\Column(type="string", length=255, nullable=FALSE) 
     */
    private string $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=FALSE) 
     */
    private string $role;

    /**
     * @ORM\Column(type="string", length=255, nullable=FALSE) 
     */
    private string $apikey;

    /**
     * @var                         DateTime|NULL
     * @ORM\Column(type="datetime", nullable=TRUE)
     */
    private ?DateTime $lastLoggedAt = null;

    /**
     * Helper property that is changed to true once we touch data through any of the setters
     *
     * @var bool
     */
    private bool $isChanged = false;

    public function __construct(string $name, string $surname, string $email, string $username, string $passwordHash)
    {
        $this->name = $name;
        $this->surname = $surname;
        $this->email = $email;
        $this->username = $username;
        $this->password = $passwordHash;

        $this->role = self::ROLE_USER;
        $this->state = self::STATE_FRESH;
        $this->apikey = Random::generate(100);
    }

    public function changeLoggedAt(): void
    {
        $this->lastLoggedAt = new DateTime();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getLastLoggedAt(): ?DateTime
    {
        return $this->lastLoggedAt;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
        $this->isChanged = true;
    }

    public function getPasswordHash(): string
    {
        return $this->password;
    }

    public function block(): void
    {
        $this->state = self::STATE_BLOCKED;
        $this->isChanged = true;
    }

    public function activate(): void
    {
        $this->state = self::STATE_ACTIVATED;
        $this->isChanged = true;
    }

    public function isActivated(): bool
    {
        return $this->state === self::STATE_ACTIVATED;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function getFullname(): string
    {
        return $this->name . ' ' . $this->surname;
    }

    public function getState(): int
    {
        return $this->state;
    }

    public function setState(int $state): void
    {
        if (!in_array($state, self::STATES, true)) {
            throw new InvalidArgumentException(sprintf('Unsupported state %s', $state));
        }

        $this->state = $state;
        $this->isChanged = true;
    }

    public function getGravatar(): string
    {
        return 'https://www.gravatar.com/avatar/' . md5($this->email);
    }

    public function getApikey(): string
    {
        return $this->apikey;
    }

    public function setApikey(string $apikey): void
    {
        $this->apikey = $apikey;
        $this->isChanged = true;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
        $this->isChanged = true;
    }

    public function setSurname(string $surname): void
    {
        $this->surname = $surname;
        $this->isChanged = true;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
        $this->isChanged = true;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
        $this->isChanged = true;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
        $this->isChanged = true;
    }

    public function isChanged(): bool
    {
        return $this->isChanged;
    }
}
