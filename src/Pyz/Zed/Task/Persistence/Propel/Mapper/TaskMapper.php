<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Task\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\TaskCollectionTransfer;
use Generated\Shared\Transfer\TaskTransfer;
use Orm\Zed\Task\Persistence\PyzTask;
use Propel\Runtime\Collection\ObjectCollection;

class TaskMapper
{
    /**
     * @param \Generated\Shared\Transfer\TaskTransfer $taskTransfer
     * @param \Orm\Zed\Task\Persistence\PyzTask $pyzTask
     *
     * @return \Orm\Zed\Task\Persistence\PyzTask
     */
    public function mapTaskTransferToTaskEntity(TaskTransfer $taskTransfer, PyzTask $pyzTask): PyzTask
    {
        $taskEntity = $pyzTask->fromArray($taskTransfer->toArray());

        if (!$taskEntity->getFkUser()) {
            $taskEntity->setFkUser($taskTransfer->getIdAuthorOrFail());
        }

        $taskEntity->setFkAssignedUser($taskTransfer->getIdAssignedUser());

        return $taskEntity;
    }

    /**
     * @param \Orm\Zed\Task\Persistence\PyzTask $pyzTask
     * @param \Generated\Shared\Transfer\TaskTransfer $taskTransfer
     *
     * @return \Generated\Shared\Transfer\TaskTransfer
     */
    public function mapTaskEntityToTaskTransfer(PyzTask $pyzTask, TaskTransfer $taskTransfer): TaskTransfer
    {
        $taskTransfer = $taskTransfer->fromArray($pyzTask->toArray(), true);

        $taskTransfer->setIdAuthor($pyzTask->getFkUser());
        $taskTransfer->setIdAssignedUser($pyzTask->getFkAssignedUser());

        return $taskTransfer;
    }

    /**
     * @param \Propel\Runtime\Collection\ObjectCollection $taskEntities
     * @param \Generated\Shared\Transfer\TaskCollectionTransfer $taskCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\TaskCollectionTransfer
     */
    public function mapTaskEntitiesToTaskCollectionTransfer(
        ObjectCollection $taskEntities,
        TaskCollectionTransfer $taskCollectionTransfer,
    ): TaskCollectionTransfer {
        foreach ($taskEntities as $taskEntity) {
            $taskCollectionTransfer->addTask(
                $this->mapTaskEntityToTaskTransfer($taskEntity, new TaskTransfer()),
            );
        }

        return $taskCollectionTransfer;
    }
}
