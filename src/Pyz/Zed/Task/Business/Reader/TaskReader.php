<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Task\Business\Reader;

use Generated\Shared\Transfer\TaskCollectionTransfer;
use Generated\Shared\Transfer\TaskCriteriaTransfer;
use Pyz\Zed\Task\Persistence\TaskRepositoryInterface;

class TaskReader implements TaskReaderInterface
{
 /**
  * @var \Pyz\Zed\Task\Persistence\TaskRepositoryInterface
  */
    protected TaskRepositoryInterface $taskRepository;

    /**
     * @param \Pyz\Zed\Task\Persistence\TaskRepositoryInterface $taskRepository
     */
    public function __construct(TaskRepositoryInterface $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    /**
     * @inheritDoc
     */
    public function getTaskCollection(TaskCriteriaTransfer $taskCriteriaTransfer): TaskCollectionTransfer
    {
        return $this->taskRepository->getTaskCollection($taskCriteriaTransfer);
    }
}
