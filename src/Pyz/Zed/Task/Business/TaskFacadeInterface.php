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

interface TaskFacadeInterface
{
    /**
     * Specification:
     * - Retrieves task entities filtered by criteria from Persistence.
     * - Uses `TaskCriteriaTransfer.taskConditions.taskIds` to filter by task ids.
     * - Returns `TaskCollectionTransfer` filled with found tasks.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\TaskCriteriaTransfer $taskCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\TaskCollectionTransfer
     */
    public function getTaskCollection(TaskCriteriaTransfer $taskCriteriaTransfer): TaskCollectionTransfer;

    /**
     * Specification:
     * - Stores tasks at Persistence.
     * - Requires `TaskCollectionRequestTransfer.tasks` to be set.
     * - Requires `Task.title` to be set.
     * - Requires `Task.dueDate` to be set.
     * - Requires `Task.status` to be set.
     * - Validates `Task.title` length.
     * - Validates `Task.status` to be one of "to-do", "in-progress", "completed", "overdue".
     * - Validates `Task.due` date is valid.
     * - Returns `TaskCollectionResponseTransfer` with persisted tasks and errors if any occurred.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\TaskCollectionRequestTransfer $taskCollectionRequestTransfer
     *
     * @return \Generated\Shared\Transfer\TaskCollectionResponseTransfer
     */
    public function createTaskCollection(TaskCollectionRequestTransfer $taskCollectionRequestTransfer): TaskCollectionResponseTransfer;

    /**
     * Specification:
     * - Updates tasks at Persistence.
     * - Requires `TaskCollectionRequestTransfer.tasks` to be set.
     * - Requires `Task.title` to be set.
     * - Requires `Task.dueDate` to be set.
     * - Requires `Task.status` to be set.
     * - Validates `Task.title` length.
     * - Validates `Task.status` to be one of "to-do", "in-progress", "completed", "overdue".
     * - Validates `Task.due` date is valid.
     * - Returns `TaskCollectionResponseTransfer` with updated tasks and errors if any occurred.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\TaskCollectionRequestTransfer $taskCollectionRequestTransfer
     *
     * @return \Generated\Shared\Transfer\TaskCollectionResponseTransfer
     */
    public function updateTaskCollection(TaskCollectionRequestTransfer $taskCollectionRequestTransfer): TaskCollectionResponseTransfer;

    /**
     * Specification:
     * - Deletes tasks at Persistence.
     * - Requires `TaskCollectionDeleteCriteriaTransfer.taskIds` to be set.
     * - Returns `TaskCollectionResponseTransfer`.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\TaskCollectionDeleteCriteriaTransfer $taskCollectionDeleteCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\TaskCollectionResponseTransfer
     */
    public function deleteTaskCollection(TaskCollectionDeleteCriteriaTransfer $taskCollectionDeleteCriteriaTransfer): TaskCollectionResponseTransfer;

    /**
     * Specification:
     * - Send emails for tasks that are overdue.
     * - Requires `TaskCollectionRequestTransfer.tasks` to be set.
     * - Returns `TaskCollectionResponseTransfer` with overdue tasks that were sent and errors if any occurred.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\TaskCollectionRequestTransfer $taskCollectionRequestTransfer
     *
     * @return \Generated\Shared\Transfer\TaskCollectionResponseTransfer
     */
    public function sendTaskOverdueEmails(TaskCollectionRequestTransfer $taskCollectionRequestTransfer): TaskCollectionResponseTransfer;
}
