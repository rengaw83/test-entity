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
     * @return class-string<T>
     */
    abstract protected static function getEntityClass(): string;

    /**
     *               [
     *               'id' => 5,
     *               'boolean' => true,
     *               'name' => '#TITLE',
     *               ];.
     */
    abstract protected static function getEntityProperties(): array;

    /**
     * @return T
     */
    private function getEntity(): ?object
    {
        $modelClass = static::getEntityClass();

        return new $modelClass();
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

        $this->setInaccessibleProperty($entity, $property, $value);

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
}
