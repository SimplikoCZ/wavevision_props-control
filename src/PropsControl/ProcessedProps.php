<?php declare (strict_types=1);

namespace Wavevision\PropsControl;

use Wavevision\PropsControl\Exceptions\NotAllowed;

/**
 * @internal
 */
final class ProcessedProps
{
    /**
     * @var mixed[]
     */
    private array $values;

    public function __construct()
    {
        $this->values = [];
    }

    /**
     * @return mixed[]
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param mixed $value
     */
    public function __set(string $name, $value): void
    {
        $this->values[$name] = $value;
    }

    /**
     * @return mixed
     */
    public function &__get(string $name)
    {
        if (!$this->isSet($name)) {
            throw new NotAllowed("Cannot read an undeclared prop '$name'.");
        }
        return $this->values[$name];
    }

    public function __isset(string $name): bool
    {
        return $this->isSet($name);
    }

    public function __unset(string $name): void
    {
        throw new NotAllowed("Cannot unset prop '$name', props are read-only.");
    }

    public function isSet(string $prop): bool
    {
        return array_key_exists($prop, $this->values);
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

}
