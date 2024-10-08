<?php declare (strict_types = 1);

namespace Wavevision\PropsControl;

use Nette\Utils\ArrayHash;
use Nette\Utils\Json;
use stdClass;
use Wavevision\PropsControl\Exceptions\NotAllowed;
use function array_key_exists;
use function array_keys;
use function implode;

final class ValidProps extends stdClass
{

	private Props $props;

	/**
	 * @var mixed[]
	 */
	private array $values;

	/**
	 * @param mixed[] $values
	 */
	public function __construct(Props $props, array $values)
	{
		$this->props = $props;
		$this->values = $values;
	}

	/**
	 * @return mixed
	 */
	public function get(string $prop)
	{
        return $this->values[$prop] ?? null;
	}

	/**
	 * @param mixed $default
	 * @return mixed
	 */
	public function getNullable(string $prop, $default = null)
	{
		if ($this->isSet($prop)) {
			return $this->get($prop) ?? $default;
		}
		return $default;
	}

	public function isSet(string $prop): bool
	{
		return array_key_exists($prop, $this->values);
	}

	public function getProps(): Props
	{
		return $this->props;
	}

	/**
	 * @return array<mixed>
	 */
	public function toArray(): array
	{
		return $this->values;
	}

	/**
	 * @return ArrayHash<mixed>
	 */
	public function toArrayHash(): ArrayHash
	{
		return ArrayHash::from($this->toArray());
	}

	public function toJson(): string
	{
		return Json::encode($this->toArray());
	}

	/**
	 * @param mixed[] $arguments
	 */
	public function __call(string $method, array $arguments): void
	{
		throw new NotAllowed("Cannot call an undefined method '$method'.");
	}

	/**
	 * @param mixed[] $arguments
	 */
	public static function __callStatic(string $method, array $arguments): void
	{
		throw new NotAllowed("Cannot call an undefined static method '$method'.");
	}

	/**
	 * @return mixed
	 */
	public function &__get(string $name)
	{
		if (!$this->isSet($name)) {
			throw new NotAllowed(sprintf("Cannot read an undeclared prop '$name' available props are [%s].", implode(', ', array_keys($this->values))));
		}
		return $this->values[$name];
	}

	/**
	 * @param mixed $value
	 */
	public function __set(string $name, $value): void
	{
		if ($this->isSet($name)) {
			throw new NotAllowed("Cannot write to a read-only prop '$name'.");
		}
		throw new NotAllowed("Cannot write to an undeclared prop '$name'.");
	}

	public function __isset(string $name): bool
	{
		return $this->isSet($name);
	}

	public function __unset(string $name): void
	{
		throw new NotAllowed("Cannot unset prop '$name', props are read-only.");
	}

}
