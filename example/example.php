<?php
declare(strict_types = 1);

require_once __DIR__ . '/../vendor/autoload.php';

use Warehouse\Example\ColorEntity;
use Warehouse\Domain\Model\Repository\Filter;
use Warehouse\Infrastructure\Model\Repository\Memory\MemoryFilterProcessor;
use Warehouse\Infrastructure\Model\Repository\Memory\MemoryRepository;
use Warehouse\Infrastructure\Model\Repository\Memory\MemorySortProcessor;

$colors = [
  'red',
  'green',
  'yellow',
  'blue',
  'cyan',
  'pink',
  'purple',
  'orange',
  'white',
  'black'
];

$repository = new MemoryRepository(
  new MemoryFilterProcessor(),
  new MemorySortProcessor()
);

foreach ($colors as $index => $name) {
  $repository->add(new ColorEntity($index, $name));
}

echo 'List all colors:', PHP_EOL;
var_dump($repository->findAll()->toArray());
echo PHP_EOL;

echo 'List all names containing "R":', PHP_EOL;
$filter = new Filter();
$filter->field('name')->contains('R');
var_dump($repository->findBy($filter)->toArray());
echo PHP_EOL;

echo 'List all colors with "id" between 2 and 6:', PHP_EOL;
$filter = new Filter();
$filter->field('id')->isBetween(2, 6);
var_dump($repository->findBy($filter)->toArray());
echo PHP_EOL;
