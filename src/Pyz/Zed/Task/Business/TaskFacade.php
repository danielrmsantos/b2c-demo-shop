<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Task\Business;

use Generated\Shared\Transfer\TaskCollectionDeleteCriteriaTransfer;
use Generated\Shared\Transfer\TaskCollectionRequestTransfer;
use Generated\Shared\Transfer\TaskCollectionResponseTransfer;
use Generated\Shared\Transfer\TaskCollectionTransfer;
use Generated\Shared\Transfer\TaskCriteriaTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \Pyz\Zed\Task\Business\TaskBusinessFactory getFactory()
 * @method \Pyz\Zed\Task\Persistence\TaskRepositoryInterface getRepository()
 * @method \Pyz\Zed\Task\Persistence\TaskEntityManagerInterface getEntityManager()
 */
class TaskFacade extends AbstractFacade implements TaskFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\TaskCriteriaTransfer $taskCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\TaskCollectionTransfer
     */
    public function getTaskCollection(TaskCriteriaTransfer $taskCriteriaTransfer): TaskCollectionTransfer
    {
        return $this->getFactory()->createTaskReader()->getTaskCollection($taskCriteriaTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\TaskCollectionRequestTransfer $taskCollectionRequestTransfer
     *
     * @return \Generated\Shared\Transfer\TaskCollectionResponseTransfer
     */
    public function createTaskCollection(TaskCollectionRequestTransfer $taskCollectionRequestTransfer): TaskCollectionResponseTransfer
    {
        return $this->getFactory()->createTaskWriter()->createTaskCollection($taskCollectionRequestTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\TaskCollectionRequestTransfer $taskCollectionRequestTransfer
     *
     * @return \Generated\Shared\Transfer\TaskCollectionResponseTransfer
     */
    public function updateTaskCollection(TaskCollectionRequestTransfer $taskCollectionRequestTransfer): TaskCollectionResponseTransfer
    {
        return $this->getFactory()->createTaskWriter()->updateTaskCollection($taskCollectionRequestTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\TaskCollectionDeleteCriteriaTransfer $taskCollectionDeleteCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\TaskCollectionResponseTransfer
     */
    public function deleteTaskCollection(TaskCollectionDeleteCriteriaTransfer $taskCollectionDeleteCriteriaTransfer): TaskCollectionResponseTransfer
    {
        return $this->getFactory()->createTaskDeleter()->deleteTaskCollection($taskCollectionDeleteCriteriaTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\TaskCollectionRequestTransfer $taskCollectionRequestTransfer
     *
     * @return \Generated\Shared\Transfer\TaskCollectionResponseTransfer
     */
    public function sendTaskOverdueEmails(TaskCollectionRequestTransfer $taskCollectionRequestTransfer): TaskCollectionResponseTransfer
    {
        return $this->getFactory()->createTaskEmailSender()->sendTaskOverdueEmails($taskCollectionRequestTransfer);
    }
}
