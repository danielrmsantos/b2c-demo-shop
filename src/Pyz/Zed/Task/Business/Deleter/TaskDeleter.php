<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Task\Business\Deleter;

use Generated\Shared\Transfer\TaskCollectionDeleteCriteriaTransfer;
use Generated\Shared\Transfer\TaskCollectionResponseTransfer;
use Pyz\Zed\Task\Business\Mapper\TaskMapper;
use Pyz\Zed\Task\Persistence\TaskEntityManagerInterface;

class TaskDeleter implements TaskDeleterInterface
{
    /**
     * @var \Pyz\Zed\Task\Persistence\TaskEntityManagerInterface
     */
    protected TaskEntityManagerInterface $entityManager;

    /**
     * @var \Pyz\Zed\Task\Business\Mapper\TaskMapper
     */
    protected TaskMapper $mapper;

    /**
     * @param \Pyz\Zed\Task\Persistence\TaskEntityManagerInterface $entityManager
     * @param \Pyz\Zed\Task\Business\Mapper\TaskMapper $mapper
     */
    public function __construct(TaskEntityManagerInterface $entityManager, TaskMapper $mapper)
    {
        $this->entityManager = $entityManager;
        $this->mapper = $mapper;
    }

    /**
     * @param \Generated\Shared\Transfer\TaskCollectionDeleteCriteriaTransfer $taskCollectionDeleteCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\TaskCollectionResponseTransfer
     */
    public function deleteTaskCollection(TaskCollectionDeleteCriteriaTransfer $taskCollectionDeleteCriteriaTransfer): TaskCollectionResponseTransfer
    {
        if ($taskCollectionDeleteCriteriaTransfer->getTaskIds() === []) {
            return new TaskCollectionResponseTransfer();
        }

        $this->entityManager->deleteTasks($taskCollectionDeleteCriteriaTransfer->getTaskIds());

        return $this->mapper->mapTaskIdsToTaskCollectionResponseTransfer($taskCollectionDeleteCriteriaTransfer, new TaskCollectionResponseTransfer());
    }
}
