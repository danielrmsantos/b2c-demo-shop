<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Task;

use Spryker\Zed\Kernel\AbstractBundleConfig;

class TaskConfig extends AbstractBundleConfig
{
    /**
     * @var string
     */
    public const TASK_STATUS_TO_DO = 'to-do';

    /**
     * @var string
     */
    public const TASK_STATUS_IN_PROGRESS = 'in-progress';

    /**
     * @var string
     */
    public const TASK_STATUS_COMPLETED = 'completed';

    /**
     * @var string
     */
    public const TASK_STATUS_OVERDUE = 'overdue';

    /**
     * @var int
     */
    public const TASK_TITLE_LENGTH = 255;

    /**
     * @var int
     */
    public const TASK_STATUS_LENGTH = 36;
}
