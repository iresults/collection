# iresults/collection

This library provides support for collections - immutable lists of items.
And `Map` - a flexible key-value-container with support for objects as keys.

## Collection

```php
<?php
use Iresults\Collection\Collection;
$collection = new Collection('a', 'b', 'c');
$result = $collection->reduce(
    fn ( ?string $carry, string $item) => ($carry ?? 'the start') . '/' . strtoupper($item)
);
assert('the start/A/B/C' === $result);
```

### Collections with typed items

Create a custom subclass of `AbstractCollection` with **your** item types:

```php
<?php
use Iresults\Collection\AbstractCollection;

/**
 * @extends AbstractCollection<Person>
 */
class PersonCollection extends AbstractCollection
{
    public function __construct(Person ...$items)
    {
        parent::__construct(...$items);
    }
}
```

Use the type-safe collection:

```php
<?php
$collection = new PersonCollection(
    new Person('Daniel'),
    new Person('Gary'),
    new Person('Loren'),
);

$result = $collection->filter(
    fn (Person $item) => 'Gary' === $item->name
);
assert($result instanceof PersonCollection);
assert(1 === $result->count());
assert([new Person('Gary')] == $result->getArrayCopy());
```

## Map

```php
<?php
use Iresults\Collection\Map;
use Iresults\Collection\Pair;

final readonly class Person {
    public function __construct(public string $name){}
}

final readonly class PersonalInformation {
    public function __construct(public int $age) {}
}


/** @var Map<Person,PersonalInformation> $exampleMap */
$exampleMap = new Map(
    new Pair(new Person('Daniel'), new PersonalInformation(37)),
    new Pair(new Person('Gary'), new PersonalInformation(61)),
    new Pair(new Person('Loren'), new PersonalInformation(23)),
);
$result = $fixture->map(
    fn (
        PersonalInformation $pi,
        Person $person,
    ) => $person->name . ' is ' . $pi->age . ' years old'
);
assert($result instanceof Map);
$expected = [
    'Daniel is 37 years old',
    'Gary is 61 years old',
    'Loren is 23 years old',
];
assert(array_values($result->getValues()) === $expected);
```
