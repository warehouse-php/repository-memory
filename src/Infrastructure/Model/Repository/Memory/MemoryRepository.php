<?php
declare(strict_types = 1);

namespace Warehouse\Infrastructure\Model\Repository\Memory;

use Exception;
use Warehouse\Domain\Model\Exception\RecordNotFoundException;
use Warehouse\Domain\Model\Repository\EntityInterface;
use Warehouse\Domain\Model\Repository\FilterProcessorInterface;
use Warehouse\Domain\Model\Repository\PaginatedRepositoryInterface;
use Warehouse\Domain\Model\Repository\ReadRepositoryInterface;
use Warehouse\Domain\Model\Repository\SortProcessorInterface;
use Warehouse\Domain\Model\Repository\WriteRepositoryInterface;

final class MemoryRepository implements PaginatedRepositoryInterface, ReadRepositoryInterface, WriteRepositoryInterface {
  private FilterProcessorInterface $filterProcessor;
  private SortProcessorInterface $sortProcessor;
  /**
   * @var \Warehouse\Domain\Model\Repository\EntityInterface[]
   */
  private array $data = [];

  public function __construct(
    FilterProcessorInterface $filterProcessor,
    SortProcessorInterface $sortProcessor,
    EntityInterface ...$entities
  ) {
    $this->filterProcessor = $filterProcessor;
    $this->sortProcessor   = $sortProcessor;

    $this->add($entities);
  }

  /** PaginatedRepositoryInterface **/
  public function findAll(CursorInterface $cursor = null): PageInterface {
    // if ($cursor === null) {
    //   return new ResultPage($this->data, count($this->data), 1, 1);
    // }

    // $data = $this->findBy($cursor->getFilter(), $cursor->getSort());
    // if (count($cursor->getDistinctFields()) > 0) {}

    // return new ResultPage(
    //   $data->slice(0, $cursor->pageSize())
    //   $data->count(),
    //   $cursor->getPageNumber(),
    //   ceil(count($data) / $cursor->getPageSize())
    // );
  }


  /** ReadRepositoryInterface **/
  public function count(FilterInterface $filter = null): int {
    if ($filter === null) {
      return count($this->data);
    }

    return count($this->findBy($filter));
  }

  public function exists(mixed $id): bool {
    return array_key_exists($id, $this->data);
  }

  public function find(mixed $id, FieldsInterface $fields = null): EntityInterface {
    if ($this->exists($id)) {
      return $this->data[$id];
    }

    throw new RecordNotFoundException();
  }

  public function findBy(FilterInterface $filter, Sort $sort = null, FieldsInterface $fields = null): CollectionInterface {
    $data = $this->data;

    if ($filter !== null) {
      $data = InMemoryFilter::filter($data, $filter);
    }

    if ($sort !== null) {
      $data = Sorter::sort($data, $sort);
    }

    return array_values($data);
  }

  public function findDistinctBy(FieldsInterface $distinctFields, FilterInterface $filter = null, Sort $sort = null, FieldsInterface $fields = null): CollectionInterface {}

  /** WriteRepositoryInterface **/
  public function add(EntityInterface ...$entities): static {
    foreach ($entities as $entity) {
      $id = $entity->getId();
      $this->data[$id] = clone $entity;
    }

    return $this;
  }

  public function remove(EntityInterface ...$entities): static {
    foreach ($entities as $entity) {
      $id = $entity->getId();
      if ($this->exists($id)) {
        unset($this->data[$id]);
      }
    }

    return $this;
  }

  public function inTransaction(callable $transaction): static {
    $backup = $this->data;
    try {
      $transaction();
    } catch (Exception $exception) {
      $this->data = $backup;

      throw $exception;
    }
  }
}
