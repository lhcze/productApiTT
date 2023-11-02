<?php

declare(strict_types=1);

namespace App\Module\V1;

use Apitte\Core\Annotation\Controller as Apitte;
use Apitte\Core\Exception\Api\ClientErrorException;
use Apitte\Core\Exception\Api\ServerErrorException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\Domain\Api\Facade\ProductsFacade;
use App\Model\Exception\Runtime\Database\EntityNotFoundException;
use App\Model\Utils\Caster;
use Doctrine\DBAL\Exception\DriverException;
use Nette\Http\IResponse;

/**
 * The ProductDeleteController class is responsible for handling the '/products/delete' endpoint in the API's V1 module.
 * It leverages the Apitte framework to define its routing and HTTP method handling, ensuring that API requests are
 * processed in accordance with the OpenAPI specification.
 *
 * The class provides the following functionality:
 * - It defines a DELETE route at '/products/delete/{id}' for deleting products
 * - It uses the ProductsFacade service to interact with the underlying database and delete defined product.
 * - It expects ID of the product.
 * - It handles successful deletion by returning a 200 OK HTTP response with a JSON content type.
 * - It captures and handles database driver exceptions, converting them into server error responses to maintain
 * a consistent API error handling strategy.
 *
 * @Apitte\Path("/products")
 * @Apitte\Tag("Products")
 */
class ProductDeleteController extends BaseV1Controller
{
    private ProductsFacade $productsFacade;

    public function __construct(ProductsFacade $productsFacade)
    {
        $this->productsFacade = $productsFacade;
    }

    /**
     * @Apitte\OpenApi("
     *   summary: Delete specified product
     * ")
     * @Apitte\Path("/delete/{id}")
     * @Apitte\Method("DELETE")
     */
    public function delete(ApiRequest $request, ApiResponse $response): ApiResponse
    {
        try {
            $this->productsFacade->delete(Caster::toInt($request->getParameter('id')));

            return $response->withStatus(IResponse::S200_OK)
                ->withHeader('Content-Type', 'application/json');
        } catch (DriverException $e) {
            throw ServerErrorException::create()
                ->withMessage('Cannot delete product')
                ->withPrevious($e);
        } catch (EntityNotFoundException $e) {
            throw ClientErrorException::create()
                ->withMessage('Product not found')
                ->withCode(IResponse::S404_NotFound);
        }
    }
}
