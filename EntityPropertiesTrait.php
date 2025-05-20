<?php

declare(strict_types=1);

namespace R83Dev\TestEntity;

use R83Dev\TestAccessible\AccessibleTrait;

/**
 * Entity properties trait.
 *
 * A abstract testcase to test properties of an entity.
 *
 * @phpstan-template T of object
 *
 * @author Michael Wagner
 */
trait EntityPropertiesTrait
{
    use AccessibleTrait;

    /**
     * The entities fully qualified class name.
     *
     * @return class-string<T>
     */
    abstract protected static function getEntityClass(): string;

    /**
     * All properties of the entity.
     *
     * Properties can be private, protected, public, readonly, ...
     *
     * Example:
     * [
     *     'id' => 5,
     *     'boolean' => true,
     *     'name' => '#TITLE',
     *     'object' => new \stdClass(),
     * ];
     */
    abstract protected static function getEntityProperties(): array;

    /**
     * Constructor arguments required to create the entity instance.
     * Required for entities with mandatory constructor arguments only.
     */
    protected function getEntityConstructorArguments(): array
    {
        return [];
    }

    /**
     * @return T
     */
    private function getEntity(): ?object
    {
        $modelClass = static::getEntityClass();

        return new $modelClass(...$this->getEntityConstructorArguments());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    #[\PHPUnit\Framework\Attributes\DataProvider('getGetterAndSetterData')]
    public function testGetters(string $property, mixed $value): void
    {
        $entity = $this->getEntity();
        $getter = 'get'.ucfirst($property);
        if (is_bool($value) && method_exists($entity, 'is'.ucfirst($property))) {
            $getter = 'is'.ucfirst($property);
        }

        if (!$this->isEntityPropertyReadonly($property)) {
            $this->setInaccessibleProperty($entity, $property, $value);
        }

        $this->assertSame(
            $value,
            $entity->$getter(),
            sprintf(
                'The %s for property "%s" of entity "%s" does not return correct value from property.',
                (is_bool($value) ? 'isser' : 'getter'),
                $property,
                $entity::class
            )
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    #[\PHPUnit\Framework\Attributes\DataProvider('getGetterAndSetterData')]
    public function testSetters(string $property, mixed $value): void
    {
        $entity = $this->getEntity();
        $setter = 'set'.ucfirst($property);

        if ($this->isEntityPropertyReadonly($property)) {
            $this->markTestSkipped(
                'Property "'.$property.'" of "'.$entity::class.'" is read only, can\'t test setter.'
            );
        }

        $this->assertSame(
            $entity,
            method_exists($entity, $setter)
                ? $entity->$setter($value)
                : $this->setInaccessibleProperty($entity, $property, $value),
            sprintf(
                'The setter for property "%s" of entity "%s" does not return itself.',
                $property,
                $entity::class
            )
        );

        $this->assertSame(
            $value,
            $this->getInaccessibleProperty($entity, $property),
            sprintf(
                'The property "%s" of entity "%s" does not contain correct value after calling setter.',
                $property,
                $entity::class
            )
        );
    }

    public static function getGetterAndSetterData(): \Generator
    {
        $dataSet = 0;

        foreach (static::getEntityProperties() as $property => $value) {
            yield $dataSet++.'_'.$property => [$property, $value];
        }
    }

    private static function isEntityPropertyReadonly(string $property)
    {
        $refObject = new \ReflectionClass(static::getEntityClass());

        return $refObject->getProperty($property)->isReadOnly();
    }
}
