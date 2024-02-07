<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Task\Business\Deleter;

use Generated\Shared\Transfer\TaskCollectionDeleteCriteriaTransfer;
use Generated\Shared\Transfer\TaskCollectionResponseTransfer;

interface TaskDeleterInterface
{
    /**
     * @param \Generated\Shared\Transfer\TaskCollectionDeleteCriteriaTransfer $taskCollectionDeleteCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\TaskCollectionResponseTransfer
     */
    public function deleteTaskCollection(TaskCollectionDeleteCriteriaTransfer $taskCollectionDeleteCriteriaTransfer): TaskCollectionResponseTransfer;
}
