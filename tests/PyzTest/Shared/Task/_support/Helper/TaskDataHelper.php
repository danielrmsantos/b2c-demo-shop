<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PyzTest\Shared\Task\Helper;

use Codeception\Module;
use Generated\Shared\DataBuilder\TaskBuilder;
use Generated\Shared\DataBuilder\TaskCollectionRequestBuilder;
use Generated\Shared\Transfer\TaskCollectionRequestTransfer;
use Generated\Shared\Transfer\TaskCollectionTransfer;
use Generated\Shared\Transfer\TaskTransfer;
use Orm\Zed\Task\Persistence\PyzTask;
use Pyz\Zed\Task\Persistence\Propel\Mapper\TaskMapper;

class TaskDataHelper extends Module
{
    /**
     * @param array $seed
     *
     * @return \Generated\Shared\Transfer\TaskTransfer
     */
    public function haveTaskTransfer(array $seed = []): TaskTransfer
    {
        return (new TaskBuilder())->seed($seed)->build();
    }

    /**
     * @param array $seed
     *
     * @return \Generated\Shared\Transfer\TaskCollectionRequestTransfer
     */
    public function haveTaskCollectionRequestTransfer(array $seed = []): TaskCollectionRequestTransfer
    {
        return (new TaskCollectionRequestBuilder())->seed($seed)->withTask($seed)->withAnotherTask($seed)->build();
    }

    /**
     * @param array $seed
     *
     * @return \Generated\Shared\Transfer\TaskTransfer
     */
    public function haveTaskPersisted(array $seed = []): TaskTransfer
    {
        $taskMapper = (new TaskMapper());
        $taskTransfer = $this->haveTaskTransfer($seed);

        $taskEntity = $taskMapper->mapTaskTransferToTaskEntity($taskTransfer, new PyzTask());

        $taskEntity->save();

        return $taskMapper->mapTaskEntityToTaskTransfer($taskEntity, $taskTransfer);
    }

    /**
     * @param int $amountOfTasks
     * @param array $seed
     *
     * @return TaskCollectionTransfer
     */
    public function haveTaskCollectionWithPersistedTasks(int $amountOfTasks, array $seed = []): TaskCollectionTransfer
    {
        $taskCollection = new TaskCollectionTransfer();

        for ($i = 0; $i < $amountOfTasks; $i++) {
            $task = $this->haveTaskPersisted($seed);
            $taskCollection->addTask($task);
        }

        return $taskCollection;
    }
}
