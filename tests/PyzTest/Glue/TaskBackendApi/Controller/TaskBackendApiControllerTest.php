<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PyzTest\Glue\TaskBackendApi\Controller;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\GlueFilterTransfer;
use Generated\Shared\Transfer\GlueResourceTransfer;
use Generated\Shared\Transfer\PaginationTransfer;
use Generated\Shared\Transfer\TaskTransfer;
use Orm\Zed\Task\Persistence\PyzTaskQuery;
use Pyz\Glue\TaskBackendApi\Controller\TaskBackendApiController;
use Pyz\Glue\TaskBackendApi\TaskBackendApiConfig;
use PyzTest\Glue\TaskBackendApi\TaskBackendApiTester;
use Symfony\Component\HttpFoundation\Response;

/**
 * Auto-generated group annotations
 *
 * @group PyzTest
 * @group Glue
 * @group TaskBackendApi
 * @group Controller
 * @group TaskBackendApiControllerTest
 * Add your own group annotations below this line
 */
class TaskBackendApiControllerTest extends Unit
{
    /**
     * @var \PyzTest\Glue\TaskBackendApi\TaskBackendApiTester
     */
    protected TaskBackendApiTester $tester;

    /**
     * @var \Pyz\Glue\TaskBackendApi\Controller\TaskBackendApiController
     */
    protected TaskBackendApiController $controller;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->controller = new TaskBackendApiController();
        $this->tester->ensureDatabaseTableIsEmpty(PyzTaskQuery::create());
    }

    /**
     * @return void
     */
    public function testGetCollectionActionReturnsCollection(): void
    {
        //Arrange
        $userTransfer = $this->tester->haveUser();
        $amountOfTasksToCreate = 10;
        $taskCollectionTransfer = $this->tester->haveTaskCollectionWithPersistedTasks($amountOfTasksToCreate, [TaskTransfer::ID_AUTHOR => $userTransfer->getIdUser()]);
        /** @var \Generated\Shared\Transfer\TaskTransfer $taskTransfer */
        $taskTransfer = $taskCollectionTransfer->getTasks()->getIterator()->current();

        $glueRequestTransfer = $this->tester->haveGlueRequestTransferWithRequestUserTransfer($userTransfer);

        //Act
        $glueResponseTransfer = $this->controller->getCollectionAction($glueRequestTransfer);

        //Assert
        $this->tester->assertGlueResponseHasCorrectData($taskTransfer, $glueResponseTransfer, $amountOfTasksToCreate);
    }

    /**
     * @return void
     */
    public function testGetCollectionActionReturnsCollectionPaginatedByOffsetAndLimit(): void
    {
        //Arrange
        $userTransfer = $this->tester->haveUser();
        $taskCollectionTransfer = $this->tester->haveTaskCollectionWithPersistedTasks(10, [TaskTransfer::ID_AUTHOR => $userTransfer->getIdUser()]);

        /** @var \Generated\Shared\Transfer\TaskTransfer $taskTransfer */
        $taskTransfer = $taskCollectionTransfer->getTasks()->getIterator()->current();

        $glueRequestTransfer = $this->tester->haveGlueRequestTransferWithRequestUserTransfer($userTransfer);

        $limit = 2;
        $paginationTransfer = (new PaginationTransfer())->setOffset(0)->setLimit($limit);
        $glueRequestTransfer->setPagination($paginationTransfer);

        //Act
        $glueResponseTransfer = $this->controller->getCollectionAction($glueRequestTransfer);

        //Assert
        $this->tester->assertGlueResponseHasCorrectData($taskTransfer, $glueResponseTransfer, $limit);
        $this->assertNotNull($glueResponseTransfer->getPagination());
    }

    /**
     * @return void
     */
    public function testGetCollectionActionReturnsFilteredTaskCollectionThatMatchesExactCondition(): void
    {
        //Arrange
        $userTransfer = $this->tester->haveUser();
        $this->tester->haveTaskCollectionWithPersistedTasks(10, [TaskTransfer::ID_AUTHOR => $userTransfer->getIdUser()]);

        $taskTitle = 'A very nice task 12345!!';
        $taskTransfer = $this->tester->haveTaskPersisted([
            TaskTransfer::ID_AUTHOR => $userTransfer->getIdUser(),
            TaskTransfer::TITLE => $taskTitle,
        ]);

        $glueRequestTransfer = $this->tester->haveGlueRequestTransferWithRequestUserTransfer($userTransfer);

        $glueFilterTransfer = (new GlueFilterTransfer())->setResource(TaskBackendApiConfig::RESOURCE_TASK)->setField('title')->setValue($taskTitle);
        $glueRequestTransfer->addFilter($glueFilterTransfer);

        //Act
        $glueResponseTransfer = $this->controller->getCollectionAction($glueRequestTransfer);

        //Assert
        $this->tester->assertGlueResponseHasCorrectData($taskTransfer, $glueResponseTransfer, 1);
    }

    /**
     * @return void
     */
    public function testGetCollectionActionReturnsTaskCollectionWithTasksThatPartiallyMatchesTitleOrDescriptionToQueryString(): void
    {
        //Arrange
        $userTransfer = $this->tester->haveUser();
        $this->tester->haveTaskCollectionWithPersistedTasks(10, [TaskTransfer::ID_AUTHOR => $userTransfer->getIdUser()]);

        $example = 'A very nice task 12345!!';
        $this->tester->haveTaskPersisted([
            TaskTransfer::ID_AUTHOR => $userTransfer->getIdUser(),
            TaskTransfer::TITLE => $example,
        ]);

        $this->tester->haveTaskPersisted([
            TaskTransfer::ID_AUTHOR => $userTransfer->getIdUser(),
            TaskTransfer::DESCRIPTION => $example,
        ]);

        $glueRequestTransfer = $this->tester->haveGlueRequestTransferWithRequestUserTransfer($userTransfer);
        $glueRequestTransfer->setQueryFields([TaskBackendApiConfig::QUERY_STRING_PARAMETER => 'task 12345']);

        //Act
        $glueResponseTransfer = $this->controller->getCollectionAction($glueRequestTransfer);

        //Assert
        $this->assertCount(2, $glueResponseTransfer->getResources());
    }

    /**
     * @return void
     */
    public function testGetActionReturnsTaskById(): void
    {
        //Arrange
        $userTransfer = $this->tester->haveUser();
        $taskCollectionTransfer = $this->tester->haveTaskCollectionWithPersistedTasks(5, [TaskTransfer::ID_AUTHOR => $userTransfer->getIdUser()]);

        /** @var \Generated\Shared\Transfer\TaskTransfer $taskTransfer */
        $taskTransfer = $taskCollectionTransfer->getTasks()->getIterator()->current();
        $resourceTransfer = (new GlueResourceTransfer())->setId((string)$taskTransfer->getIdTask());
        $glueRequestTransfer = $this->tester->haveGlueRequestTransferWithRequestUserTransfer($userTransfer);
        $glueRequestTransfer->setResource($resourceTransfer);

        //Act
        $glueResponseTransfer = $this->controller->getAction($glueRequestTransfer);

        // Assert
        $this->tester->assertGlueResponseHasCorrectData($taskTransfer, $glueResponseTransfer, 1);
    }

    /**
     * @return void
     */
    public function testGetActionReturnNotFoundHttpStatusWhenTaskDoesNotExist(): void
    {
        //Arrange
        $resourceTransfer = (new GlueResourceTransfer())->setId('10101010');
        $glueRequestTransfer = $this->tester->haveGlueRequestTransferWithRequestUserTransfer();
        $glueRequestTransfer->setResource($resourceTransfer);

        //Act
        $glueResponseTransfer = $this->controller->getAction($glueRequestTransfer);

        // Assert
        $this->tester->assertResponseHasHttpStatusAndErrorMessage($glueResponseTransfer, Response::HTTP_NOT_FOUND, 'Task not found');
    }

    /**
     * @return void
     */
    public function testPostActionReturnsCollectionWithCreatedTasks(): void
    {
        //Arrange
        $taskTransfer = $this->tester->haveTaskTransfer();

        $tasksToCreate = [
            $taskTransfer->toArray(),
            $this->tester->haveTaskTransfer()->toArray(),
        ];

        $glueRequestTransfer = $this->tester->haveGlueRequestTransferWithRequestUserTransfer();
        $glueRequestTransfer->setContent(json_encode(['data' => $tasksToCreate]));

        //Act
        $glueResponseTransfer = $this->controller->postAction($glueRequestTransfer);

        // Assert
        $this->tester->assertGlueResponseHasCorrectData($taskTransfer, $glueResponseTransfer, count($tasksToCreate));
    }

    /**
     * @return void
     */
    public function testPatchActionReturnsUpdatedTask(): void
    {
        //Arrange
        $userTransfer = $this->tester->haveUser();
        $taskTransfer = $this->tester->haveTaskPersisted([TaskTransfer::ID_AUTHOR => $userTransfer->getIdUser()]);

        $taskTransfer->setTitle('An Updated Test Title');
        $taskTransfer->setDueDate('2024-01-01');

        $glueRequestTransfer = $this->tester->haveGlueRequestTransferWithRequestUserTransfer();
        $glueRequestTransfer->setContent(json_encode(['data' => $taskTransfer->toArray()]));
        $glueRequestTransfer->getResourceOrFail()->setId((string)$taskTransfer->getIdTask());

        //Act
        $glueResponseTransfer = $this->controller->patchAction($glueRequestTransfer);

        // Assert
        $this->tester->assertGlueResponseHasCorrectData($taskTransfer, $glueResponseTransfer, 1);
    }

    /**
     * @return void
     */
    public function testPatchActionReturnsBadRequestHttpStatusWhenPayloadIsInvalid(): void
    {
        //Arrange
        $userTransfer = $this->tester->haveUser();
        $taskTransfer = $this->tester->haveTaskPersisted([TaskTransfer::ID_AUTHOR => $userTransfer->getIdUser()]);

        $taskTransfer->setTitle('');

        $glueRequestTransfer = $this->tester->haveGlueRequestTransferWithRequestUserTransfer();
        $glueRequestTransfer->setContent(json_encode(['data' => $taskTransfer->toArray()]));
        $glueRequestTransfer->getResourceOrFail()->setId((string)$taskTransfer->getIdTask());

        //Act
        $glueResponseTransfer = $this->controller->patchAction($glueRequestTransfer);

        // Assert
        $this->tester->assertResponseHasHttpStatusAndErrorMessage($glueResponseTransfer, Response::HTTP_BAD_REQUEST, 'This value should not be blank');
    }

    /**
     * @return void
     */
    public function testPatchActionReturnsBadRequestHttpStatusWhenAssignedUserDoesNotExist(): void
    {
        //Arrange
        $userTransfer = $this->tester->haveUser();
        $taskTransfer = $this->tester->haveTaskPersisted([TaskTransfer::ID_AUTHOR => $userTransfer->getIdUser()]);

        $taskTransfer->setIdAssignedUser(12345);

        $glueRequestTransfer = $this->tester->haveGlueRequestTransferWithRequestUserTransfer();
        $glueRequestTransfer->setContent(json_encode(['data' => $taskTransfer->toArray()]));
        $glueRequestTransfer->getResourceOrFail()->setId((string)$taskTransfer->getIdTask());

        //Act
        $glueResponseTransfer = $this->controller->patchAction($glueRequestTransfer);

        // Assert
        $this->tester->assertResponseHasHttpStatusAndErrorMessage($glueResponseTransfer, Response::HTTP_BAD_REQUEST, 'Assigned User not found');
    }

    /**
     * @return void
     */
    public function testDeleteActionReturnsNoContentHttpStatus(): void
    {
        //Arrange
        $userTransfer = $this->tester->haveUser();
        $taskTransfer = $this->tester->haveTaskPersisted([TaskTransfer::ID_AUTHOR => $userTransfer->getIdUser()]);

        $glueRequestTransfer = $this->tester->haveGlueRequestTransferWithRequestUserTransfer();
        $glueRequestTransfer->getResourceOrFail()->setId((string)$taskTransfer->getIdTask());

        //Act
        $glueResponseTransfer = $this->controller->deleteAction($glueRequestTransfer);

        // Assert
        $this->tester->assertSame(Response::HTTP_NO_CONTENT, $glueResponseTransfer->getHttpStatus());
        $this->tester->assertCount(0, $glueResponseTransfer->getErrors());
    }
}
