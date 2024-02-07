<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Task\Business\Sender;

use Generated\Shared\Transfer\TaskCollectionRequestTransfer;
use Generated\Shared\Transfer\TaskCollectionResponseTransfer;

interface TaskEmailSenderInterface
{
    /**
     * @param \Generated\Shared\Transfer\TaskCollectionRequestTransfer $taskCollectionRequestTransfer
     *
     * @return \Generated\Shared\Transfer\TaskCollectionResponseTransfer
     */
    public function sendTaskOverdueEmails(TaskCollectionRequestTransfer $taskCollectionRequestTransfer): TaskCollectionResponseTransfer;
}
