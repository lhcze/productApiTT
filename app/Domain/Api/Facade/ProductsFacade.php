<?php

declare(strict_types=1);

namespace App\Domain\Api\Facade;

use App\Domain\Product\Product;
use App\Domain\Api\Response\ProductResDto;
use App\Domain\Api\Request\UpdateProductReqDto;
use App\Domain\Api\Request\CreateProductReqDto;
use App\Model\Database\EntityManagerDecorator;
use App\Model\Exception\Runtime\Database\EntityNotFoundException;
use App\Model\Utils\Caster;

/**
 * The ProductsFacade class provides a high-level API for performing CRUD operations on Product entities.
 * It utilizes the EntityManagerDecorator to interact with the database, ensuring that all database operations
 * are handled efficiently and in a decoupled manner from the application's controllers.
 *
 * Methods provided by this class include:
 * - findBy: Retrieves a list of products based on a set of criteria, with support for ordering, limiting, and offsetting results.
 * - findAll: Fetches all products, with optional pagination.
 * - findOneBy: Finds a single product based on specific criteria.
 * - findOne: Retrieves a single product by its unique identifier.
 * - create: Creates a new product entity from request data and persists it to the database.
 * - update: Updates an existing product entity with new data from a request and persists the changes.
 * - delete: Removes a product entity from the database by its identifier.
 */
final class ProductsFacade
{
    public function __construct(private readonly EntityManagerDecorator $em)
    {
    }

    /**
     * Find products by defined criteria with order by, limit and offset features
     *
     * @param  array<string, mixed> $criteria Products definition criteria
     * @param  string[]             $orderBy  Order products by what column and which direction
     * @param  int                  $limit    Return defined number of product, default is 10
     * @param  int                  $offset   Return products from defined offset for pagination purposes
     * @return ProductResDto[] Response body
     */
    public function findBy(array $criteria = [], array $orderBy = ['id' => 'ASC'], int $limit = 10, int $offset = 0): array
    {
        $entities = $this->em->getRepository(Product::class)->findBy($criteria, $orderBy, $limit, $offset);
        $result = [];

        foreach ($entities as $entity) {
            $result[] = ProductResDto::from($entity);
        }

        return $result;
    }

    /**
     * Return all products with pagination feature
     *
     * @param  int $limit  Return defined number of product, default is 10
     * @param  int $offset Return products from defined offset for pagination purposes
     * @return ProductResDto[] Response body
     */
    public function findAll(int $limit = 10, int $offset = 0): array
    {
        return $this->findBy([], ['id' => 'ASC'], $limit, $offset);
    }

    /**
     * Find one product by defined criteria
     *
     * @param  array<string, mixed> $criteria Product definition criteria
     * @param  string[]             $orderBy  Criteria to order by
     * @return ProductResDto Response body
     */
    public function findOneBy(array $criteria, ?array $orderBy = null): ProductResDto
    {
        $entity = $this->em->getRepository(Product::class)->findOneBy($criteria, $orderBy);

        if ($entity === null) {
            throw new EntityNotFoundException();
        }

        return ProductResDto::from($entity);
    }

    /**
     * Find product by its ID
     *
     * @param  int $id Product ID
     * @return ProductResDto Response body
     */
    public function findOne(int $id): ProductResDto
    {
        return $this->findOneBy(['id' => $id]);
    }

    /**
     * Create new product from data provided in the request body
     *
     * @param  CreateProductReqDto $dto Data from the request body
     * @return Product Product entity
     */
    public function create(CreateProductReqDto $dto): Product
    {
        $product = new Product(
            $dto->name,
            $dto->price
        );

        $product->setCreatedAt();

        $this->em->persist($product);
        $this->em->flush($product);

        return $product;
    }

    /**
     * Update product from provided data in the request body. Product is identified by product ID
     *
     * @param  int                 $id  Product ID
     * @param  UpdateProductReqDto $dto Data from the request body
     * @return Product Product entity
     */
    public function update(int $id, UpdateProductReqDto $dto): Product
    {
        $product = $this->em->getRepository(Product::class)->find($id);

        if ($product === null) {
            throw new EntityNotFoundException();
        }

        if ($dto->getName() !== null) {
            $product->setName($dto->getName());
        }
        $product->setPrice(Caster::toFloat($dto->getPrice()));
        $product->setUpdatedAt();

        $this->em->persist($product);
        $this->em->flush();

        return $product;
    }


    /**
     * Delete entity from the database
     *
     * @param  int $id Product ID to be deleted
     * @return bool Return true if removal was successful
     */
    public function delete(int $id): bool
    {
        $product = $this->em->getRepository(Product::class)->find($id);

        if ($product === null) {
            throw new EntityNotFoundException();
        }

        $this->em->remove($product);
        $this->em->flush();

        return true;
    }
}
