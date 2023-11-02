<?php declare(strict_types = 1);

namespace App\Domain\Api\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class UpdateProductReqDto
{
	/**
	 * @var array<string, mixed>
	 */
	private array $setProperties = [];

	/** @Assert\NotBlank */
	private ?string $name;

	/** @Assert\NotBlank */
	private ?float $price;

	public function __set(string $name, mixed $value) {
		$this->setProperties[$name] = true;
		$this->$name = $value;
	}

	public function __isset(string $name) {
		return isset($this->setProperties[$name]);
	}

	public function setName(?string $name): void {
		$this->name = $name;
		$this->__set('name', $name);
	}

	public function setPrice(?float $price): void {
		$this->price = $price;
		$this->__set('price', $price);
	}

	public function getName(): ?string {
		return $this->name;
	}

	public function getPrice(): ?float {
		return $this->price;
	}

	// Method to check if a property came from the request
	public function wasSet(string $property): bool {
		return isset($this->setProperties[$property]);
	}
}
