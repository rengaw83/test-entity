# Testing Entities Trait

A helper class for testing,
especially for unit tests to easily test properties of entities or data transfer objects.

## Installation

Install via composer:

```
dcrr composer req --dev r83dev/test-entity
```

## Usage

Use the `EntityPropertiesTrait` in your test and create `getEntityClass` and `getEntityProperties` methods:

<details>
<summary>Example entity <code>MyEntity</code></summary>

```php
class MyEntity
{
    private int $id;

    private string $name = '';

    private ?Collection $categories;

    private bool $active = false;

    public function __construct(
        private readonly string $key
    ) {
        $this->categories = new ArrayCollection();
    }

    public function getId(): int
    {
        if (!isset($this->id)) {
            throw new \LogicException('Entity not yet initialized or made persistent.');
        }

        return $this->id;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function setCategories(Collection $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }
}
```

</details>

```php

/**
 * @extends R83Dev\TestEntity\EntityPropertiesTrait<MyEntity>
 */
class MyEntityTest {
    use R83Dev\TestEntity\EntityPropertiesTrait;
    
    /**
     * The entities fully qualified class name.
     */
    protected static function getEntityClass(): string
    {
        return MyEntity::class;
    }

    /**
     * Constructor arguments required to create the entity instance.
     * Required for entities with mandatory constructor arguments only
     */
    protected static function getEntityConstructorArguments(): array
    {
        return ['key'];
    }

    /**
     * All properties of the entity.
     * Properties can be private, protected, public, readonly, ...
     */
    protected static function getEntityProperties(): array
    {
        return [
            'id' => 5,
            'name' => 'My Entity',
            'categories' => new ArrayCollection(['category1']),
            'active' => true,
        ];
    }

    /**
     * Add your own custom tests to check special logic.
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function getIdThrowsException(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Entity not yet initialized or made persistent.');
        $this->getEntity()->getId();
    }

}
```

The Trait will now test the getters, setters and issers of the properties
