<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Task\Business\Validator;

use Generated\Shared\Transfer\TaskCollectionResponseTransfer;

interface TaskValidatorInterface
{
    /**
     * @param \Generated\Shared\Transfer\TaskCollectionResponseTransfer $taskCollectionResponseTransfer
     *
     * @return \Generated\Shared\Transfer\TaskCollectionResponseTransfer
     */
    public function validate(TaskCollectionResponseTransfer $taskCollectionResponseTransfer): TaskCollectionResponseTransfer;
}
