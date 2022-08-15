<?php
declare(strict_types = 1);

namespace Warehouse\Example;

use Warehouse\Domain\Model\Repository\EntityInterface;

class ColorEntity implements EntityInterface {
  private string $id;
  private string $name;

  public function __construct(mixed $id, string $name) {
    $this->id   = $id;
    $this->name = $name;
  }

  public function getId(): mixed {
    return $this->id;
  }

  public function getName(): string {
    return $this->name;
  }
}
