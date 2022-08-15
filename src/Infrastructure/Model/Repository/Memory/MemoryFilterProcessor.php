<?php
declare(strict_types = 1);

namespace Warehouse\Domain\Model\Repository\Memory;

use Warehouse\Domain\Model\Repository\FilterProcessorInterface;

final class MemoryFilterProcessor implements FilterProcessorInterface {
  /**
   * @var array<int, array<string, mixed>>
   */
  private array $dataSet;

  /**
   * @param array<int, array<string, mixed>> $dataSet
   */
  public function setDataSet(array &$dataSet) {
    $this->dataSet = $dataSet;
  }

  /**
   * @return array<int, array<string, mixed>>
   */
  public function getDataSet(): array {
    return $this->dataSet;
  }

  public function apply(FilterInterface $filter): void {
    foreach ($filter->get() as $fieldName => $filterSpec) {
      if (isset($this->dataSet[0][$fieldName]) === false) {
        throw new LogicException(
          sprintf(
            'Field "%s" is not included in the result set',
            $fieldName
          )
        );
      }

      foreach ($this->dataSet as $rowNum => $rowData) {
        switch ($filterSpec['filter']) {
          case 'startsWith':
            $result = str_starts_with($rowData[$fieldName], $filterSpec['value']);
            if ($filterSpec['not']) {
              $result != $result;
            }

            break;
          case 'endsWith':
          case 'contains':
          case 'isNull':
          case 'isEmpty':
          case 'isTrue':
          case 'isFalse':
          case 'isEqualTo':
          case 'isGreaterThan':
          case 'isGreaterThanOrEqualTo':
          case 'isLessThan':
          case 'isLessThanOrEqualTo':
          case 'isBetween':
          case 'inArray':
        }
      }
    }
  }
}
