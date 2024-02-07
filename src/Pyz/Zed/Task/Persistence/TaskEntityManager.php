<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Task\Persistence;

use Generated\Shared\Transfer\TaskTransfer;
use Orm\Zed\Task\Persistence\PyzTask;
use Spryker\Zed\Kernel\Persistence\AbstractEntityManager;

/**
 * @method \Pyz\Zed\Task\Persistence\TaskPersistenceFactory getFactory()
 */
class TaskEntityManager extends AbstractEntityManager implements TaskEntityManagerInterface
{
    /**
     * @param \Generated\Shared\Transfer\TaskTransfer $taskTransfer
     *
     * @return \Generated\Shared\Transfer\TaskTransfer
     */
    public function createTask(TaskTransfer $taskTransfer): TaskTransfer
    {
        $taskEntity = $this->getFactory()->createTaskMapper()->mapTaskTransferToTaskEntity($taskTransfer, new PyzTask());

        $taskEntity->save();

        return $this->getFactory()->createTaskMapper()->mapTaskEntityToTaskTransfer($taskEntity, $taskTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\TaskTransfer $taskTransfer
     *
     * @return \Generated\Shared\Transfer\TaskTransfer|null
     */
    public function updateTask(TaskTransfer $taskTransfer): ?TaskTransfer
    {
        $taskEntity = $this->getFactory()->getTaskQuery()->filterByIdTask($taskTransfer->getIdTaskOrFail())->findOne();

        if (!$taskEntity) {
            return null;
        }

        $taskEntity = $this->getFactory()->createTaskMapper()->mapTaskTransferToTaskEntity($taskTransfer, $taskEntity);
        $taskEntity->save();

        return $this->getFactory()->createTaskMapper()->mapTaskEntityToTaskTransfer($taskEntity, $taskTransfer);
    }

    /**
     * @param array<int> $taskIds
     *
     * @return void
     */
    public function deleteTasks(array $taskIds): void
    {
        $this->getFactory()->getTaskQuery()->filterByIdTask_In($taskIds)->delete();
    }
}
