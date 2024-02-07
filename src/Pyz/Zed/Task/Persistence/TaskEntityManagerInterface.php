<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Task\Persistence;

use Generated\Shared\Transfer\TaskTransfer;

interface TaskEntityManagerInterface
{
    /**
     * @param \Generated\Shared\Transfer\TaskTransfer $taskTransfer
     *
     * @return \Generated\Shared\Transfer\TaskTransfer
     */
    public function createTask(TaskTransfer $taskTransfer): TaskTransfer;

    /**
     * @param \Generated\Shared\Transfer\TaskTransfer $taskTransfer
     *
     * @return \Generated\Shared\Transfer\TaskTransfer|null
     */
    public function updateTask(TaskTransfer $taskTransfer): ?TaskTransfer;

    /**
     * @param array<int> $taskIds
     *
     * @return void
     */
    public function deleteTasks(array $taskIds): void;
}
