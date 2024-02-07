<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Task\Business\Mapper;

use Generated\Shared\Transfer\TaskCollectionDeleteCriteriaTransfer;
use Generated\Shared\Transfer\TaskCollectionResponseTransfer;
use Generated\Shared\Transfer\TaskTransfer;

class TaskMapper
{
    /**
     * @param \Generated\Shared\Transfer\TaskCollectionDeleteCriteriaTransfer $taskCollectionDeleteCriteriaTransfer
     * @param \Generated\Shared\Transfer\TaskCollectionResponseTransfer $taskCollectionResponseTransfer
     *
     * @return \Generated\Shared\Transfer\TaskCollectionResponseTransfer
     */
    public function mapTaskIdsToTaskCollectionResponseTransfer(
        TaskCollectionDeleteCriteriaTransfer $taskCollectionDeleteCriteriaTransfer,
        TaskCollectionResponseTransfer $taskCollectionResponseTransfer,
    ): TaskCollectionResponseTransfer {
        foreach ($taskCollectionDeleteCriteriaTransfer->getTaskIds() as $taskId) {
            $taskCollectionResponseTransfer->addTask(
                (new TaskTransfer())->setIdTask($taskId),
            );
        }

        return $taskCollectionResponseTransfer;
    }
}
