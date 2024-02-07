<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PyzTest\Zed\Task;

use Codeception\Actor;
use Codeception\Stub;
use Generated\Shared\Transfer\TaskCollectionDeleteCriteriaTransfer;
use Generated\Shared\Transfer\TaskCollectionRequestTransfer;
use Generated\Shared\Transfer\TaskCollectionResponseTransfer;
use Generated\Shared\Transfer\TaskTransfer;
use Orm\Zed\Task\Persistence\PyzTaskQuery;
use Pyz\Zed\Task\Dependency\Facade\TaskToMailFacadeInterface;
use Pyz\Zed\Task\TaskDependencyProvider;

/**
 * Inherited Methods
 *
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 * @method \Pyz\Zed\Task\Business\TaskFacadeInterface getFacade(?string $moduleName = null)
 *
 * @SuppressWarnings(\PyzTest\Zed\Task\PHPMD)
 */
class TaskBusinessTester extends Actor
{
    use _generated\TaskBusinessTesterActions;

    /**
     * @param \Generated\Shared\Transfer\TaskCollectionRequestTransfer $taskCollectionRequestTransfer
     * @param \Generated\Shared\Transfer\TaskCollectionResponseTransfer $taskCollectionResponseTransfer
     *
     * @return void
     */
    public function assertTaskCollectionIsPersistedWithCorrectValues(
        TaskCollectionRequestTransfer $taskCollectionRequestTransfer,
        TaskCollectionResponseTransfer $taskCollectionResponseTransfer,
    ): void {
        /** @var \Generated\Shared\Transfer\TaskTransfer $taskTransfer */
        $taskTransfer = $taskCollectionRequestTransfer->getTasks()->getIterator()->current();

        $taskIds = $this->getTaskIds($taskCollectionResponseTransfer);

        $persistedTaskCollection = $this->getTaskQuery()->filterByIdTask_In($taskIds)->find();

        /** @var \Orm\Zed\Task\Persistence\PyzTask $persistedTaskEntity */
        $persistedTaskEntity = $persistedTaskCollection->getIterator()->current();

        $this->assertCount(0, $taskCollectionResponseTransfer->getErrors());
        $this->assertCount($taskCollectionResponseTransfer->getTasks()->count(), $persistedTaskCollection);
        $this->assertTrue($taskCollectionResponseTransfer->getIsSuccessful());
        $this->assertSame($taskTransfer->getTitle(), $persistedTaskEntity->getTitle());
        $this->assertSame($taskTransfer->getDescription(), $persistedTaskEntity->getDescription());
        $this->assertSame($taskTransfer->getDueDate(), $persistedTaskEntity->getDueDate()->format('Y-m-d'));
        $this->assertSame($taskTransfer->getIdAuthor(), $persistedTaskEntity->getFkUser());
        $this->assertSame($taskTransfer->getIdAssignedUser(), $persistedTaskEntity->getFkAssignedUser());
    }

    /**
     * @param \Generated\Shared\Transfer\TaskCollectionDeleteCriteriaTransfer $taskCollectionDeleteCriteriaTransfer
     *
     * @return void
     */
    public function assertTasksAreDeleted(TaskCollectionDeleteCriteriaTransfer $taskCollectionDeleteCriteriaTransfer): void
    {
        $taskEntities = $this->getTaskQuery()->filterByIdTask_In($taskCollectionDeleteCriteriaTransfer->getTaskIds())->count();

        $this->assertSame(0, $taskEntities);
    }

    /**
     * @param \Generated\Shared\Transfer\TaskCollectionRequestTransfer $taskCollectionRequestTransfer
     * @param \Generated\Shared\Transfer\TaskCollectionResponseTransfer $taskCollectionResponseTransfer
     * @param string $errorMessage
     *
     * @return void
     */
    public function assertTaskCollectionFailedWithErrorMessage(
        TaskCollectionRequestTransfer $taskCollectionRequestTransfer,
        TaskCollectionResponseTransfer $taskCollectionResponseTransfer,
        string $errorMessage,
    ): void {
        $errorTransfers = $taskCollectionResponseTransfer->getErrors();

        $this->assertFalse($taskCollectionResponseTransfer->getIsSuccessful());
        $this->assertCount($taskCollectionRequestTransfer->getTasks()->count(), $errorTransfers);
        $this->assertStringContainsString($errorMessage, $errorTransfers->getIterator()->current()->getMessage());
    }

    /**
     * @param \Generated\Shared\Transfer\TaskCollectionResponseTransfer $taskCollectionResponseTransfer
     *
     * @return array<int>
     */
    public function getTaskIds(TaskCollectionResponseTransfer $taskCollectionResponseTransfer): array
    {
        return array_map(static function (TaskTransfer $taskTransfer) {
            return $taskTransfer->getIdTask();
        }, $taskCollectionResponseTransfer->getTasks()->getArrayCopy());
    }

    /**
     * @return void
     */
    public function addDependencies(): void
    {
        $this->setDependency(TaskDependencyProvider::FACADE_MAIL, Stub::makeEmpty(TaskToMailFacadeInterface::class));
    }

    /**
     * @return \Orm\Zed\Task\Persistence\PyzTaskQuery
     */
    public function getTaskQuery(): PyzTaskQuery
    {
        return PyzTaskQuery::create();
    }
}
