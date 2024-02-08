<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PyzTest\Zed\Task\Business;

use ArrayObject;
use Codeception\Test\Unit;
use DateTime;
use Generated\Shared\Transfer\PaginationTransfer;
use Generated\Shared\Transfer\TaskCollectionDeleteCriteriaTransfer;
use Generated\Shared\Transfer\TaskCollectionRequestTransfer;
use Generated\Shared\Transfer\TaskConditionsTransfer;
use Generated\Shared\Transfer\TaskCriteriaTransfer;
use Generated\Shared\Transfer\TaskSearchConditionsTransfer;
use Generated\Shared\Transfer\TaskTransfer;
use Generated\Shared\Transfer\UserTransfer;
use Pyz\Zed\Task\Business\Validator\TaskValidator;
use PyzTest\Zed\Task\TaskBusinessTester;

/**
 * Auto-generated group annotations
 *
 * @group PyzTest
 * @group Zed
 * @group Task
 * @group Business
 * @group Facade
 * @group TaskFacadeTest
 * Add your own group annotations below this line
 */
class TaskFacadeTest extends Unit
{
    /**
     * @var \PyzTest\Zed\Task\TaskBusinessTester
     */
    protected TaskBusinessTester $tester;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->tester->ensureDatabaseTableIsEmpty($this->tester->getTaskQuery());
    }

    /**
     * @return void
     */
    public function testGetTaskCollectionReturnsTaskCollectionWithAllTasksWhenNoConditionIsProvided(): void
    {
        // Arrange
        $userTransfer = $this->tester->haveUser();
        $amountOfTasksToCreate = 10;
        $this->tester->haveTaskCollectionWithPersistedTasks($amountOfTasksToCreate, [TaskTransfer::ID_AUTHOR => $userTransfer->getIdUser()]);
        $taskCriteriaTransfer = (new TaskCriteriaTransfer())->setTaskConditions(new TaskConditionsTransfer());

        // Act
        $taskCollectionTransfer = $this->tester->getFacade()->getTaskCollection($taskCriteriaTransfer);

        // Assert
        $this->assertCount($amountOfTasksToCreate, $taskCollectionTransfer->getTasks());
    }

    /**
     * @return void
     */
    public function testGetTaskCollectionReturnsTaskCollectionPaginatedByOffsetAndLimit(): void
    {
        // Arrange
        $limit = 2;
        $userTransfer = $this->tester->haveUser();
        $this->tester->haveTaskCollectionWithPersistedTasks(5, [TaskTransfer::ID_AUTHOR => $userTransfer->getIdUser()]);

        $paginationTransfer = (new PaginationTransfer())
            ->setOffset(1)
            ->setLimit($limit);

        $taskCriteriaTransfer = (new TaskCriteriaTransfer())
            ->setPagination($paginationTransfer)
            ->setTaskConditions(new TaskConditionsTransfer());

        // Act
        $taskCollectionTransfer = $this->tester->getFacade()->getTaskCollection($taskCriteriaTransfer);

        // Assert
        $this->assertCount($limit, $taskCollectionTransfer->getTasks());
        $this->assertNotNull($taskCollectionTransfer->getPagination());
    }

    /**
     * @return void
     */
    public function testGetTaskCollectionReturnsFilteredTaskCollectionThatMatchesExactCondition(): void
    {
        // Arrange
        $userTransfer = $this->tester->haveUser();
        $this->tester->haveTaskCollectionWithPersistedTasks(5, [TaskTransfer::ID_AUTHOR => $userTransfer->getIdUser()]);

        $taskTitle = 'A very nice task 12345!!';
        $taskTransfer = $this->tester->haveTaskPersisted([
            TaskTransfer::ID_AUTHOR => $userTransfer->getIdUser(),
            TaskTransfer::TITLE => $taskTitle,
        ]);

        $matchingTaskConditionsTransfer = (new TaskConditionsTransfer())->setTitle($taskTransfer->getTitle());
        $matchingTaskCriteriaTransfer = (new TaskCriteriaTransfer())->setTaskConditions($matchingTaskConditionsTransfer);

        $nonMatchingTaskConditionsTransfer = (new TaskConditionsTransfer())->setTitle('task 12345');
        $nonMatchingTaskCriteriaTransfer = (new TaskCriteriaTransfer())->setTaskConditions($nonMatchingTaskConditionsTransfer);

        // Act
        $matchingTaskCollectionTransfer = $this->tester->getFacade()->getTaskCollection($matchingTaskCriteriaTransfer);
        $nonMatchingTaskCollectionTransfer = $this->tester->getFacade()->getTaskCollection($nonMatchingTaskCriteriaTransfer);

        // Assert
        $this->assertCount(0, $nonMatchingTaskCollectionTransfer->getTasks());
        $this->assertCount(1, $matchingTaskCollectionTransfer->getTasks());
    }

    /**
     * @return void
     */
    public function testGetTaskCollectionReturnsTaskCollectionWithTasksThatPartiallyMatchesTitleOrDescriptionToSearchString(): void
    {
        // Arrange
        $userTransfer = $this->tester->haveUser();
        $this->tester->haveTaskCollectionWithPersistedTasks(5, [TaskTransfer::ID_AUTHOR => $userTransfer->getIdUser()]);

        $example = 'A very nice task 12345!!';
        $this->tester->haveTaskPersisted([
            TaskTransfer::ID_AUTHOR => $userTransfer->getIdUser(),
            TaskTransfer::TITLE => $example,
        ]);

        $this->tester->haveTaskPersisted([
            TaskTransfer::ID_AUTHOR => $userTransfer->getIdUser(),
            TaskTransfer::DESCRIPTION => $example,
        ]);

        $taskSearchConditionsTransfer = (new TaskSearchConditionsTransfer())->setSearchString('task 12345');
        $taskCriteriaTransfer = (new TaskCriteriaTransfer())->setTaskSearchConditions($taskSearchConditionsTransfer);

        // Act
        $taskCollectionTransfer = $this->tester->getFacade()->getTaskCollection($taskCriteriaTransfer);

        // Assert
        $this->assertCount(2, $taskCollectionTransfer->getTasks());
    }

    /**
     * @return void
     */
    public function testGetTaskCollectionReturnsTaskCollectionWithSingleTaskByTaskId(): void
    {
        // Arrange
        $userTransfer = $this->tester->haveUser();
        $taskCollectionTransfer = $this->tester->haveTaskCollectionWithPersistedTasks(5, [TaskTransfer::ID_AUTHOR => $userTransfer->getIdUser()]);

        $taskId = $taskCollectionTransfer->getTasks()->getIterator()->current()->getIdTaskOrFail();
        $taskConditions = (new TaskConditionsTransfer())->setTaskIds([$taskId]);
        $taskCriteriaTransfer = (new TaskCriteriaTransfer())->setTaskConditions($taskConditions);

        // Act
        $taskCollectionTransfer = $this->tester->getFacade()->getTaskCollection($taskCriteriaTransfer);

        // Assert
        $this->assertCount(1, $taskCollectionTransfer->getTasks());
        $this->assertSame($taskId, $taskCollectionTransfer->getTasks()->getIterator()->current()->getIdTask());
    }

    /**
     * @return void
     */
    public function testCreateTaskCollectionReturnsSuccessfullTaskCollectionResponse(): void
    {
        // Arrange
        $userTransfer = $this->tester->haveUser();
        $taskCollectionRequestTransfer = $this->tester->haveTaskCollectionRequestTransfer([
            TaskTransfer::ID_AUTHOR => $userTransfer->getIdUser(),
        ]);

        // Act
        $taskCollectionResponseTransfer = $this->tester->getFacade()->createTaskCollection($taskCollectionRequestTransfer);

        // Assert
        $this->tester->assertTaskCollectionIsPersistedWithCorrectValues($taskCollectionRequestTransfer, $taskCollectionResponseTransfer);
    }

    /**
     * @return void
     */
    public function testCreateTaskCollectionReturnFailedTaskCollectionResponseWhenAuthorIsMissing(): void
    {
        // Arrange
        $taskCollectionRequestTransfer = $this->tester->haveTaskCollectionRequestTransfer();

        // Act
        $taskCollectionResponseTransfer = $this->tester->getFacade()->createTaskCollection($taskCollectionRequestTransfer);

        // Assert
        $this->assertFalse($taskCollectionResponseTransfer->getIsSuccessful());
    }

    /**
     * @return void
     */
    public function testCreateTaskCollectionReturnsFailedTaskCollectionResponseWhenAssigedUserDoesNotExist(): void
    {
        // Arrange
        $userTransfer = $this->tester->haveUser();
        $taskCollectionRequestTransfer = $this->tester->haveTaskCollectionRequestTransfer([
            TaskTransfer::ID_AUTHOR => $userTransfer->getIdUser(),
            TaskTransfer::ID_ASSIGNED_USER => 12345,
        ]);

        // Act
        $taskCollectionResponseTransfer = $this->tester->getFacade()->createTaskCollection($taskCollectionRequestTransfer);

        // Assert
        $this->tester->assertTaskCollectionFailedWithErrorMessage($taskCollectionRequestTransfer, $taskCollectionResponseTransfer, TaskValidator::ASSIGNED_USER_NOT_FOUND_MESSAGE);
    }

    /**
     * @return void
     */
    public function testUpdateTaskCollectionReturnsSuccessfullTaskCollectionResponse(): void
    {
        // Arrange
        $userTransfer = $this->tester->haveUser();
        $taskCollectionRequestTransfer = $this->tester->haveTaskCollectionRequestTransfer([
            TaskTransfer::ID_AUTHOR => $userTransfer->getIdUser(),
        ]);
        $taskCollectionResponseTransfer = $this->tester->getFacade()->createTaskCollection($taskCollectionRequestTransfer);

        foreach ($taskCollectionResponseTransfer->getTasks() as $task) {
            $task->setTitle('Updated');
            $task->setDescription('Updated');
        }

        // Act
        $updatedTaskCollectionResponseTransfer = $this->tester->getFacade()->updateTaskCollection($taskCollectionRequestTransfer);

        // Assert
        $this->tester->assertTaskCollectionIsPersistedWithCorrectValues($taskCollectionRequestTransfer, $updatedTaskCollectionResponseTransfer);
    }

    /**
     * @return void
     */
    public function testUpdateTaskCollectionReturnsFailedTaskCollectionResponseWhenTaskDoesNotExist(): void
    {
        // Arrange
        $userTransfer = $this->tester->haveUser();
        $taskCollectionRequestTransfer = $this->tester->haveTaskCollectionRequestTransfer([
            TaskTransfer::ID_AUTHOR => $userTransfer->getIdUser(),
        ]);

        foreach ($taskCollectionRequestTransfer->getTasks() as $task) {
            $task->setIdTask(123456);
            $task->setTitle('Updated');
            $task->setDescription('Updated');
        }

        // Act
        $taskCollectionResponseTransfer = $this->tester->getFacade()->updateTaskCollection($taskCollectionRequestTransfer);

        // Assert
        $this->tester->assertTaskCollectionFailedWithErrorMessage($taskCollectionRequestTransfer, $taskCollectionResponseTransfer, 'Task not found');
    }

    /**
     * @return void
     */
    public function testDeleteTaskCollectionDeletesTasks(): void
    {
        // Arrange
        $userTransfer = $this->tester->haveUser();
        $taskCollectionRequestTransfer = $this->tester->haveTaskCollectionRequestTransfer([
            TaskTransfer::ID_AUTHOR => $userTransfer->getIdUser(),
        ]);
        $taskCollectionResponseTransfer = $this->tester->getFacade()->createTaskCollection($taskCollectionRequestTransfer);
        $taskCollectionDeleteCriteria = (new TaskCollectionDeleteCriteriaTransfer())->setTaskIds($this->tester->getTaskIds($taskCollectionResponseTransfer));

        // Act
        $this->tester->getFacade()->deleteTaskCollection($taskCollectionDeleteCriteria);

        // Assert
        $this->tester->assertTasksAreDeleted($taskCollectionDeleteCriteria);
    }

    /**
     * @return void
     */
    public function testSendTaskOverdueEmailsReturnsNoErrors(): void
    {
        // Arrange
        $this->tester->addDependencies();
        $userTransfer = $this->tester->haveUser([UserTransfer::USERNAME => 'test@test.com']);
        $amountOfTasksToCreate = 5;
        $taskCollectionTransfer = $this->tester->haveTaskCollectionWithPersistedTasks($amountOfTasksToCreate, [
            TaskTransfer::ID_AUTHOR => $userTransfer->getIdUser(),
            TaskTransfer::DUE_DATE => (new DateTime())->setTimestamp(strtotime('-1 day'))->format('Y-m-d'),
        ]);
        $taskCollectionRequestTransfer = (new TaskCollectionRequestTransfer())->setTasks($taskCollectionTransfer->getTasks());

        // Act
        $taskCollectionResponseTransfer = $this->tester->getFacade()->sendTaskOverdueEmails($taskCollectionRequestTransfer);

        // Assert
        $this->assertCount(0, $taskCollectionResponseTransfer->getErrors());
        $this->assertCount($amountOfTasksToCreate, $taskCollectionResponseTransfer->getTasks());
    }

    /**
     * @return void
     */
    public function testSendTaskOverdueEmailsReturnsSuccesfullySentTasksAndErrorsForTasksWhereUserHasInvalidEmail(): void
    {
        // Arrange
        $this->tester->addDependencies();
        $userTransferWithInvalidEmail = $this->tester->haveUser();
        $userTransferWithValidEmail = $this->tester->haveUser([UserTransfer::USERNAME => 'test@test.com']);

        $invalidAmountOfTasksToCreate = 2;
        $taskCollectionTransferWithInvalidUserEmail = $this->tester->haveTaskCollectionWithPersistedTasks($invalidAmountOfTasksToCreate, [
            TaskTransfer::ID_AUTHOR => $userTransferWithInvalidEmail->getIdUser(),
            TaskTransfer::DUE_DATE => (new DateTime())->setTimestamp(strtotime('-1 day'))->format('Y-m-d'),
        ]);

        $validAmountOfTasksToCreate = 3;
        $taskCollectionTransferWithValidUserEmail = $this->tester->haveTaskCollectionWithPersistedTasks($validAmountOfTasksToCreate, [
            TaskTransfer::ID_AUTHOR => $userTransferWithValidEmail->getIdUser(),
            TaskTransfer::DUE_DATE => (new DateTime())->setTimestamp(strtotime('-1 day'))->format('Y-m-d'),
        ]);

        $taskCollectionRequestTransfer = new TaskCollectionRequestTransfer();
        $taskCollectionRequestTransfer = $this->addTasksToTaskCollectionRequestTransfer($taskCollectionTransferWithInvalidUserEmail->getTasks(), $taskCollectionRequestTransfer);
        $taskCollectionRequestTransfer = $this->addTasksToTaskCollectionRequestTransfer($taskCollectionTransferWithValidUserEmail->getTasks(), $taskCollectionRequestTransfer);

        // Act
        $taskCollectionResponseTransfer = $this->tester->getFacade()->sendTaskOverdueEmails($taskCollectionRequestTransfer);

        // Assert
        $this->assertCount($validAmountOfTasksToCreate, $taskCollectionResponseTransfer->getTasks());
        $this->assertCount($invalidAmountOfTasksToCreate, $taskCollectionResponseTransfer->getErrors());
    }

    /**
     * @param \ArrayObject<int, \Generated\Shared\Transfer\TaskTransfer> $tasks
     * @param \Generated\Shared\Transfer\TaskCollectionRequestTransfer $taskCollectionRequestTransfer
     *
     * @return \Generated\Shared\Transfer\TaskCollectionRequestTransfer
     */
    protected function addTasksToTaskCollectionRequestTransfer(
        ArrayObject $tasks,
        TaskCollectionRequestTransfer $taskCollectionRequestTransfer,
    ): TaskCollectionRequestTransfer {
        foreach ($tasks as $task) {
            $taskCollectionRequestTransfer->addTask($task);
        }

        return $taskCollectionRequestTransfer;
    }
}
