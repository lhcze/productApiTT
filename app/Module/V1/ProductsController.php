<?php

declare(strict_types=1);

namespace App\Module\V1;

use Apitte\Core\Annotation\Controller as Apitte;
use Apitte\Core\Exception\Api\ServerErrorException;
use Apitte\Core\Http\ApiRequest;
use App\Domain\Api\Facade\ProductsFacade;
use App\Domain\Api\Response\ProductResDto;
use App\Domain\Api\Response\UserResDto;
use App\Model\Utils\Caster;
use Doctrine\DBAL\Exception\DriverException;

/**
 * The ProductController class is responsible for handling the '/products' endpoint in the API's V1 module.
 * It leverages the Apitte framework to define its routing and HTTP method handling, ensuring that API requests are
 * processed in accordance with the OpenAPI specification.
 *
 * The class provides the following functionality:
 * - It defines a GET route at '/products' for listing the products
 * - It offers limit and offset functionality for pagination
 * - It uses the ProductsFacade service to interact with the underlying database and persist new product data.
 * - It returns fetched product as defined in the DTO class ProductResDto in JSON format
 * - It captures and handles database driver exceptions, converting them into server error responses to maintain
 *   a consistent API error handling strategy.
 *
 * @Apitte\Path("/products")
 * @Apitte\Tag("Products")
 */
class ProductsController extends BaseV1Controller
{
    private ProductsFacade $productsFacade;

    public function __construct(ProductsFacade $productsFacade)
    {
        $this->productsFacade = $productsFacade;
    }

    /**
     * @Apitte\OpenApi("
     *   summary: List Products
     * ")
     * @Apitte\Path("/")
     * @Apitte\Method("GET")
     * @Apitte\RequestParameters({
     * @Apitte\RequestParameter(name="limit",  type="int", in="query", required=false, description="Data limit"),
     * @Apitte\RequestParameter(name="offset", type="int", in="query", required=false, description="Data offset")
     * })
     * @return                                 array<string, ProductResDto>
     */
    public function index(ApiRequest $request): array
    {

        try {
            return $this->productsFacade->findAll(
                Caster::toInt($request->getParameter('limit', 10)),
                Caster::toInt($request->getParameter('offset', 0))
            );
        } catch (DriverException $e) {
            throw ServerErrorException::create()
                ->withMessage('Cannot list products')
                ->withPrevious($e);
        }
    }
}
