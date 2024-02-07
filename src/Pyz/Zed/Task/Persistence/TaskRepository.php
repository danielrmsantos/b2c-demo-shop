<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Task\Persistence;

use ArrayObject;
use Generated\Shared\Transfer\PaginationTransfer;
use Generated\Shared\Transfer\TaskCollectionTransfer;
use Generated\Shared\Transfer\TaskCriteriaTransfer;
use Orm\Zed\Task\Persistence\PyzTaskQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;

/**
 * @method \Pyz\Zed\Task\Persistence\TaskPersistenceFactory getFactory()
 */
class TaskRepository extends AbstractRepository implements TaskRepositoryInterface
{
    /**
     * @param \Generated\Shared\Transfer\TaskCriteriaTransfer $taskCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\TaskCollectionTransfer
     */
    public function getTaskCollection(TaskCriteriaTransfer $taskCriteriaTransfer): TaskCollectionTransfer
    {
        $taskQuery = $this->getFactory()->getTaskQuery();
        $taskQuery = $this->applyTaskFilters($taskQuery, $taskCriteriaTransfer);

        $sortTransfers = $taskCriteriaTransfer->getSortCollection();
        $taskQuery = $this->applySorting($taskQuery, $sortTransfers);

        $taskCollectionTransfer = new TaskCollectionTransfer();
        $paginationTransfer = $taskCriteriaTransfer->getPagination();

        if ($paginationTransfer) {
            $taskQuery = $this->applyPagination($taskQuery, $paginationTransfer);
            $taskCollectionTransfer->setPagination($paginationTransfer);
        }

        return $this->getFactory()->createTaskMapper()->mapTaskEntitiesToTaskCollectionTransfer($taskQuery->find(), $taskCollectionTransfer);
    }

    /**
     * @param \Orm\Zed\Task\Persistence\PyzTaskQuery $taskQuery
     * @param \Generated\Shared\Transfer\TaskCriteriaTransfer $taskCriteriaTransfer
     *
     * @return \Orm\Zed\Task\Persistence\PyzTaskQuery
     */
    protected function applyTaskFilters(
        PyzTaskQuery $taskQuery,
        TaskCriteriaTransfer $taskCriteriaTransfer,
    ): PyzTaskQuery {
        $taskConditionsTransfer = $taskCriteriaTransfer->getTaskConditions();

        if (!$taskConditionsTransfer) {
            return $taskQuery;
        }

        if ($taskConditionsTransfer->getTaskIds()) {
            $taskQuery->filterByIdTask_In($taskConditionsTransfer->getTaskIds());
        }

        if ($taskConditionsTransfer->getDueDate()) {
            $taskQuery->filterByDueDate($taskConditionsTransfer->getDueDateOrFail());
        }

        return $taskQuery;
    }

    /**
     * @param \Propel\Runtime\ActiveQuery\ModelCriteria $modelCriteria
     * @param \ArrayObject<array-key, \Generated\Shared\Transfer\SortTransfer> $sortTransfers
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    protected function applySorting(
        ModelCriteria $modelCriteria,
        ArrayObject $sortTransfers,
    ): ModelCriteria {
        foreach ($sortTransfers as $sortTransfer) {
            $modelCriteria->orderBy(
                $sortTransfer->getFieldOrFail(),
                $sortTransfer->getIsAscending() ? Criteria::ASC : Criteria::DESC,
            );
        }

        return $modelCriteria;
    }

    /**
     * @param \Propel\Runtime\ActiveQuery\ModelCriteria $modelCriteria
     * @param \Generated\Shared\Transfer\PaginationTransfer $paginationTransfer
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    protected function applyPagination(
        ModelCriteria $modelCriteria,
        PaginationTransfer $paginationTransfer,
    ): ModelCriteria {
        if ($paginationTransfer->getOffset() !== null && $paginationTransfer->getLimit() !== null) {
            $paginationTransfer->setNbResults($modelCriteria->count());

            return $modelCriteria
                ->offset($paginationTransfer->getOffsetOrFail())
                ->setLimit($paginationTransfer->getLimitOrFail());
        }

        if ($paginationTransfer->getPage() !== null && $paginationTransfer->getMaxPerPage()) {
            $propelModelPager = $modelCriteria->paginate(
                $paginationTransfer->getPageOrFail(),
                $paginationTransfer->getMaxPerPageOrFail(),
            );

            $paginationTransfer->setNbResults($propelModelPager->getNbResults())
                ->setFirstIndex($propelModelPager->getFirstIndex())
                ->setLastIndex($propelModelPager->getLastIndex())
                ->setFirstPage($propelModelPager->getFirstPage())
                ->setLastPage($propelModelPager->getLastPage())
                ->setNextPage($propelModelPager->getNextPage())
                ->setPreviousPage($propelModelPager->getPreviousPage());

            return $propelModelPager->getQuery();
        }

        return $modelCriteria;
    }
}
