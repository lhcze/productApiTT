<?php

declare(strict_types=1);

namespace App\Domain\Api\Facade;

use App\Domain\Api\Request\CreateUserReqDto;
use App\Domain\Api\Request\UpdateUserReqDto;
use App\Domain\Api\Response\UserResDto;
use App\Domain\User\User;
use App\Model\Database\EntityManagerDecorator;
use App\Model\Exception\Runtime\Database\EntityNotFoundException;
use App\Model\Exception\Runtime\InvalidStateException;

/**
 * The UsersFacade class provides a high-level API for performing CRUD operations on Product entities.
 * It utilizes the EntityManagerDecorator to interact with the database, ensuring that all database operations
 * are handled efficiently and in a decoupled manner from the application's controllers.
 *
 * Methods provided by this class include:
 * - findBy: Retrieves a list of users based on a set of criteria, with support for ordering, limiting, and offsetting results.
 * - findAll: Fetches all users, with optional pagination.
 * - findOneBy: Finds a single user based on specific criteria.
 * - findOne: Retrieves a single user by its unique identifier.
 * - create: Creates a new user entity from request data and persists it to the database.
 * - update: Updates an existing user entity with partial update support and persists the changes.
 */
final class UsersFacade
{
    public function __construct(private EntityManagerDecorator $em)
    {
    }

    /**
     * Find users by defined criteria with order by, limit and offset features
     *
     * @param  array<string, mixed> $criteria User definition criteria
     * @param  string[]             $orderBy  Order users by what column and which direction
     * @param  int                  $limit    Return defined number of users, default is 10
     * @param  int                  $offset   Return users from defined offset for pagination purposes
     * @return UserResDto[] Response body
     */
    public function findBy(array $criteria = [], array $orderBy = ['id' => 'ASC'], int $limit = 10, int $offset = 0): array
    {
        $entities = $this->em->getRepository(User::class)->findBy($criteria, $orderBy, $limit, $offset);
        $result = [];

        foreach ($entities as $entity) {
            $result[] = UserResDto::from($entity);
        }

        return $result;
    }

    /**
     * Return all users with pagination feature
     *
     * @param  int $limit  Return defined number of users, default is 10
     * @param  int $offset Return users from defined offset for pagination purposes
     * @return UserResDto[] Response body
     */
    public function findAll(int $limit = 10, int $offset = 0): array
    {
        return $this->findBy([], ['id' => 'ASC'], $limit, $offset);
    }

    /**
     * @param mixed[]  $criteria
     * @param string[] $orderBy
     */
    public function findOneBy(array $criteria, ?array $orderBy = null): UserResDto
    {
        $entity = $this->em->getRepository(User::class)->findOneBy($criteria, $orderBy);

        if ($entity === null) {
            throw new EntityNotFoundException();
        }

        return UserResDto::from($entity);
    }

    /**
     * Find one user by defined criteria
     *
     * @param  int $id User ID
     * @return UserResDto Response body
     */
    public function findOne(int $id): UserResDto
    {
        return $this->findOneBy(['id' => $id]);
    }

    /**
     * Create new user from data provided in the request body
     *
     * @param  CreateUserReqDto $dto Request object
     * @return User User Entity
     */
    public function create(CreateUserReqDto $dto): User
    {
        if ($dto->password === null) {
            throw new InvalidStateException('Password cannot be null');
        }

        $user = new User(
            $dto->name,
            $dto->surname,
            $dto->email,
            $dto->username,
            password_hash($dto->password, PASSWORD_DEFAULT)
        );




        $this->em->persist($user);
        $this->em->flush($user);

        return $user;
    }

    /**
     * Update user from data provided in the request body. Product is identified by product ID, partial user data update
     * is supported
     *
     * @param  int              $id  User ID
     * @param  UpdateUserReqDto $dto Data from the request body
     * @return User User Entity
     */
    public function update(int $id, UpdateUserReqDto $dto): User
    {
        $user = $this->em->getRepository(User::class)->find($id);

        if ($user === null) {
            throw new EntityNotFoundException();
        }

        // Run checks whether properties were sent in request body and are different from persistent data
        if ($dto->wasSet('name') && $dto->getName() !== null && $user->getName() !== $dto->getName()) {
            $user->setName($dto->getName());
        }
        if ($dto->wasSet('surname') && $dto->getSurname() !== null && $user->getSurname() !== $dto->getSurname()) {
            $user->setSurname($dto->getSurname());
        }
        if ($dto->wasSet('username') && $dto->getUsername() !== null && $user->getUsername() !== $dto->getUsername()) {
            $user->setUsername($dto->getUsername());
        }
        if ($dto->wasSet('email') && $dto->getEmail() !== null && $user->getEmail() !== $dto->getEmail()) {
            $user->setEmail($dto->getEmail());
        }
        if ($dto->wasSet('password') && $dto->getPassword() !== null && !password_verify($dto->getPassword(), $user->getPasswordHash())) {
            $user->setPassword(password_hash($dto->getPassword(), PASSWORD_DEFAULT));
        }

        // If any of the data ware changed, persist data
        if ($user->isChanged()) {
            $user->setUpdatedAt();
            $this->em->persist($user);
            $this->em->flush();
        }

        return $user;
    }
}
