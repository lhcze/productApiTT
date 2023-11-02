<?php

declare(strict_types=1);

namespace App\Module\V1;

use Apitte\Core\Annotation\Controller as Apitte;
use Apitte\Core\Exception\Api\ServerErrorException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\Domain\Api\Facade\UsersFacade;
use App\Domain\Api\Request\CreateUserReqDto;
use Doctrine\DBAL\Exception\DriverException;
use Nette\Http\IResponse;

/**
 * The UserCreateController class is responsible for handling the '/users/create' endpoint in the API's V1 module.
 * It leverages the Apitte framework to define its routing and HTTP method handling, ensuring that API requests are
 * processed in accordance with the OpenAPI specification.
 *
 * The class provides the following functionality:
 * - It defines a POST route at '/users/create' dedicated to the creation of new user accounts.
 * - It uses the ProductsFacade service to interact with the underlying database and persist new product data.
 * - It expects the request body to contain data corresponding to the CreateUserReqDto class structure.
 * - It handles successful creation by returning a 201 Created HTTP response with a JSON content type.
 * - It captures and handles database driver exceptions, converting them into server error responses to maintain
 *   a consistent API error handling strategy.
 *
 * @Apitte\Path("/users")
 * @Apitte\Tag("Users")
 */
class UserCreateController extends BaseV1Controller
{
    private UsersFacade $usersFacade;

    public function __construct(UsersFacade $usersFacade)
    {
        $this->usersFacade = $usersFacade;
    }

    /**
     * @Apitte\OpenApi("
     *   summary: Create new user.
     * ")
     * @Apitte\Path("/create")
     * @Apitte\Method("POST")
     * @Apitte\RequestBody(entity="App\Domain\Api\Request\CreateUserReqDto")
     */
    public function create(ApiRequest $request, ApiResponse $response): ApiResponse
    {
        /**
 * @var CreateUserReqDto $dto 
*/
        $dto = $request->getParsedBody();

        try {
            $this->usersFacade->create($dto);

            return $response->withStatus(IResponse::S201_Created)
                ->withHeader('Content-Type', 'application/json');
        } catch (DriverException $e) {
            throw ServerErrorException::create()
                ->withMessage('Cannot create user')
                ->withPrevious($e);
        }
    }
}
