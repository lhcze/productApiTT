<?php

declare(strict_types=1);

namespace App\Domain\Product;

use App\Model\Database\Repository\AbstractRepository;

/**
 * @method  Product|NULL find($id, ?int $lockMode = NULL, ?int $lockVersion = NULL)
 * @method  Product|NULL findOneBy(array $criteria, array $orderBy = NULL)
 * @method  Product[] findAll()
 * @method  Product[] findBy(array $criteria, array $orderBy = NULL, ?int $limit = NULL, ?int $offset = NULL)
 * @extends AbstractRepository<Product>
 */
class ProductRepository extends AbstractRepository
{
}
