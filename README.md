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

```php
class MyEntityTest {
    use R83Dev\TestEntity\EntityPropertiesTrait;
    
    protected static function getEntityClass(): string
    {
        return MyEntity::class;
    }

    protected static function getEntityProperties(): array
    {
        return [
            'id' => 5
            'name' => 'My Entity',
            'categories' => new ArrayCollection(['category1']),
            'sort' => 14,
        ];
    }

}
```

The Trait will now test the getters, setters and issers of the properties
