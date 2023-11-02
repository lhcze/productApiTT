<?php

declare(strict_types=1);

namespace App\Module\V1;

use Apitte\Core\Annotation\Controller as Apitte;
use Apitte\Core\Exception\Api\ClientErrorException;
use Apitte\Core\Http\ApiRequest;
use App\Domain\Api\Facade\UsersFacade;
use App\Domain\Api\Response\UserResDto;
use App\Model\Exception\Runtime\Database\EntityNotFoundException;
use App\Model\Utils\Caster;
use Nette\Http\IResponse;

/**
 * The UserOneController class is responsible for handling the '/users' endpoint in the API's V1 module. It leverages
 * the Apitte framework to define its routing and HTTP method handling, ensuring that API requests are processed in
 * accordance with the OpenAPI specification.
 *
 * The class provides the following functionality:
 * - It defines a GET route at '/users/email' to fetch one user by email specified in the query
 * - It defines a GET route at '/users/{id}' to fetch one user by user ID defined in path
 * - It uses the ProductsFacade service to interact with the underlying database
 * - It returns fetched product as defined in the DTO class UserResDto in JSON format
 * - It captures and handles database driver exceptions, converting them into server error responses to maintain
 * a consistent API error handling strategy.
 *
 * @Apitte\Path("/users")
 * @Apitte\Tag("Users")
 */
class UsersOneController extends BaseV1Controller
{
    private UsersFacade $usersFacade;

    public function __construct(UsersFacade $usersFacade)
    {
        $this->usersFacade = $usersFacade;
    }

    /**
     * @Apitte\OpenApi("
     *   summary: Get user by email
     * ")
     * @Apitte\Path("/email")
     * @Apitte\Method("GET")
     * @Apitte\RequestParameters({
     * @Apitte\RequestParameter(name="email", in="query", type="string", description="User e-mail address")
     * })
     */
    public function byEmail(ApiRequest $request): UserResDto
    {
        try {
            return $this->usersFacade->findOneBy(['email' => $request->getParameter('email')]);
        } catch (EntityNotFoundException $e) {
            throw ClientErrorException::create()
                ->withMessage('User not found')
                ->withCode(IResponse::S404_NotFound);
        }
    }

    /**
     * @Apitte\OpenApi("
     *   summary: Get user by id
     * ")
     * @Apitte\Path("/{id}")
     * @Apitte\Method("GET")
     * @Apitte\RequestParameters({
     * @Apitte\RequestParameter(name="id", in="path", type="int", description="User ID")
     * })
     */
    public function byId(ApiRequest $request): UserResDto
    {
        try {
            return $this->usersFacade->findOne(Caster::toInt($request->getParameter('id')));
        } catch (EntityNotFoundException $e) {
            throw ClientErrorException::create()
                ->withMessage('User not found')
                ->withCode(IResponse::S404_NotFound);
        }
    }
}
