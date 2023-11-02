<?php declare(strict_types = 1);

namespace App\Module\V1;

use Apitte\Core\Annotation\Controller as Apitte;
use Apitte\Core\Exception\Api\ClientErrorException;
use Apitte\Core\Exception\Api\ServerErrorException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\Domain\Api\Facade\UsersFacade;
use App\Domain\Api\Request\UpdateUserReqDto;
use App\Model\Exception\Runtime\Database\EntityNotFoundException;
use App\Model\Utils\Caster;
use Doctrine\DBAL\Exception\DriverException;
use Nette\Http\IResponse;

/**
 * The UserUpdateController class is responsible for handling the '/products/update/{id}' endpoint in the API's V1
 * module. It leverages the Apitte framework to define its routing and HTTP method handling, ensuring that API requests
 * are processed in accordance with the OpenAPI specification.
 *
 * The class provides the following functionality:
 * - It defines a PATCH route at '/users/update/{id}' for updating a user found by user ID
 * - It uses the ProductsFacade service to interact with the underlying database and persist new product data.
 * - It allows partial update of user properties
 * - It expects the request body to contain data corresponding to the UpdateUserReqDto class structure.
 * - It handles successful update by returning a 200 OK HTTP response with a JSON content type.
 * - It captures and handles database driver exceptions, converting them into server error responses to maintain
 * a consistent API error handling strategy.
 *
 * @Apitte\Path("/users")
 * @Apitte\Tag("Users")
 */
class UserUpdateController extends BaseV1Controller
{

	private UsersFacade $usersFacade;

	public function __construct(UsersFacade $usersFacade)
	{
		$this->usersFacade = $usersFacade;
	}

	/**
	 * @Apitte\OpenApi("
	 *   summary: Update user
	 * ")
	 * @Apitte\Path("/update/{id}")
	 * @Apitte\Method("PATCH")
	 * @Apitte\RequestBody(entity="App\Domain\Api\Request\UpdateUserReqDto")
	 */
	public function update(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		/** @var UpdateUserReqDto $dto */
		$dto = $request->getParsedBody();

		try {
			$this->usersFacade->update(Caster::toInt($request->getParameter('id')), $dto);

			return $response->withStatus(IResponse::S200_OK)
				->withHeader('Content-Type', 'application/json');

		} catch (DriverException $e) {
			throw ServerErrorException::create()
				->withMessage('Cannot update user')
				->withPrevious($e);

		} catch (EntityNotFoundException $e) {
			throw ClientErrorException::create()
				->withMessage('User not found')
				->withCode(IResponse::S404_NotFound);

		}

	}

}
