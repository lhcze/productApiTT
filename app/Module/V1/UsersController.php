<?php

declare(strict_types=1);

namespace App\Module\V1;

use Apitte\Core\Annotation\Controller as Apitte;
use Apitte\Core\Http\ApiRequest;
use App\Domain\Api\Facade\UsersFacade;
use App\Domain\Api\Response\UserResDto;
use App\Model\Utils\Caster;

/**
 * The UserController class is responsible for handling the '/users' endpoint in the API's V1 module. It leverages the
 * Apitte framework to define its routing and HTTP method handling, ensuring that API requests are processed in
 * accordance with the OpenAPI specification.
 *
 * The class provides the following functionality:
 * - It defines a GET route at '/users/' dedicated to listing user accounts
 * - It offers limit and offset functionality for pagination
 * - It uses the ProductsFacade service to interact with the underlying database and persist new product data.
 *  - It returns fetched product as defined in the DTO class UserResDto in JSON format
 *  - It captures and handles database driver exceptions, converting them into server error responses to maintain
 *    a consistent API error handling strategy.
 *
 * @Apitte\Path("/users")
 * @Apitte\Tag("Users")
 */
class UsersController extends BaseV1Controller
{
    private UsersFacade $usersFacade;

    public function __construct(UsersFacade $usersFacade)
    {
        $this->usersFacade = $usersFacade;
    }

    /**
     * @Apitte\OpenApi("
     *   summary: List users.
     * ")
     * @Apitte\Path("/")
     * @Apitte\Method("GET")
     * @Apitte\RequestParameters({
     * @Apitte\RequestParameter(name="limit",  type="int", in="query", required=false, description="Data limit"),
     * @Apitte\RequestParameter(name="offset", type="int", in="query", required=false, description="Data offset")
     * })
     * @return                                 UserResDto[]
     */
    public function index(ApiRequest $request): array
    {
        return $this->usersFacade->findAll(
            Caster::toInt($request->getParameter('limit', 10)),
            Caster::toInt($request->getParameter('offset', 0))
        );
    }
}
