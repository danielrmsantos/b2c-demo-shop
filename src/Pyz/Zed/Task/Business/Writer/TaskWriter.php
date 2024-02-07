<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Task\Business\Writer;

use Generated\Shared\Transfer\ErrorTransfer;
use Generated\Shared\Transfer\TaskCollectionRequestTransfer;
use Generated\Shared\Transfer\TaskCollectionResponseTransfer;
use Generated\Shared\Transfer\TaskTransfer;
use Pyz\Zed\Task\Business\Validator\TaskValidatorInterface;
use Pyz\Zed\Task\Persistence\TaskEntityManagerInterface;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;

class TaskWriter implements TaskWriterInterface
{
    use TransactionTrait;

    /**
     * @var \Pyz\Zed\Task\Persistence\TaskEntityManagerInterface
     */
    protected TaskEntityManagerInterface $taskEntityManager;

    /**
     * @var \Pyz\Zed\Task\Business\Validator\TaskValidatorInterface
     */
    protected TaskValidatorInterface $taskValidator;

    /**
     * @param \Pyz\Zed\Task\Persistence\TaskEntityManagerInterface $taskEntityManager
     * @param \Pyz\Zed\Task\Business\Validator\TaskValidatorInterface $taskValidator
     */
    public function __construct(TaskEntityManagerInterface $taskEntityManager, TaskValidatorInterface $taskValidator)
    {
        $this->taskEntityManager = $taskEntityManager;
        $this->taskValidator = $taskValidator;
    }

    /**
     * @param \Generated\Shared\Transfer\TaskCollectionRequestTransfer $taskCollectionRequestTransfer
     *
     * @return \Generated\Shared\Transfer\TaskCollectionResponseTransfer
     */
    public function createTaskCollection(TaskCollectionRequestTransfer $taskCollectionRequestTransfer): TaskCollectionResponseTransfer
    {
        $taskCollectionResponseTransfer = (new TaskCollectionResponseTransfer())->setTasks($taskCollectionRequestTransfer->getTasks());
        $taskCollectionResponseTransfer = $this->taskValidator->validate($taskCollectionResponseTransfer);

        if (!$taskCollectionResponseTransfer->getErrors()->count()) {
            $this->getTransactionHandler()->handleTransaction(function () use ($taskCollectionResponseTransfer): void {
                foreach ($taskCollectionResponseTransfer->getTasks() as $taskTransfer) {
                    $this->taskEntityManager->createTask($taskTransfer);
                }
            });
        }

        return $taskCollectionResponseTransfer;
    }

    /**
     * @inheritDoc
     */
    public function updateTaskCollection(TaskCollectionRequestTransfer $taskCollectionRequestTransfer): TaskCollectionResponseTransfer
    {
        $taskCollectionResponseTransfer = (new TaskCollectionResponseTransfer())->setTasks($taskCollectionRequestTransfer->getTasks());
        $taskCollectionResponseTransfer = $this->taskValidator->validate($taskCollectionResponseTransfer);

        if (!$taskCollectionResponseTransfer->getErrors()->count()) {
            $this->getTransactionHandler()->handleTransaction(function () use ($taskCollectionResponseTransfer): void {
                foreach ($taskCollectionResponseTransfer->getTasks() as $taskTransfer) {
                    $updatedTaskTransfer = $this->taskEntityManager->updateTask($taskTransfer);

                    if (!$updatedTaskTransfer) {
                        $taskCollectionResponseTransfer = $this->buildErrorTransfer($taskTransfer, $taskCollectionResponseTransfer);
                    }
                }
            });
        }

        return $taskCollectionResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\TaskTransfer $taskTransfer
     * @param \Generated\Shared\Transfer\TaskCollectionResponseTransfer $taskCollectionResponseTransfer
     * \
     *
     * @return \Generated\Shared\Transfer\TaskCollectionResponseTransfer
     */
    protected function buildErrorTransfer(
        TaskTransfer $taskTransfer,
        TaskCollectionResponseTransfer $taskCollectionResponseTransfer,
    ): TaskCollectionResponseTransfer {
        $errorTransfer = (new ErrorTransfer())
            ->setMessage('Task not found.')
            ->setEntityIdentifier((string)$taskTransfer->getIdTaskOrFail());

        return $taskCollectionResponseTransfer->addError($errorTransfer)->setIsSuccessful(false);
    }
}
