<?php declare(strict_types = 1);

namespace App\Model\Exception\Runtime\Database;

use App\Model\Exception\RuntimeException;

final class EntityNotModifiedException extends RuntimeException
{
	public static function create(string $class, int $id): self
	{
		return new self(sprintf('Entity of type %s, ID %d was found, but no modifications were done.', $class, $id));
	}

}
