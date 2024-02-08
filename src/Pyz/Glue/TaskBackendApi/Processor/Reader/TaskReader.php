<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Glue\TaskBackendApi\Processor\Reader;

use ArrayObject;
use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Generated\Shared\Transfer\TaskConditionsTransfer;
use Generated\Shared\Transfer\TaskCriteriaTransfer;
use Generated\Shared\Transfer\TaskSearchConditionsTransfer;
use Pyz\Glue\TaskBackendApi\Dependency\Facade\TaskBackendApiToTaskFacadeInterface;
use Pyz\Glue\TaskBackendApi\Mapper\TaskMapperInterface;
use Pyz\Glue\TaskBackendApi\Processor\ResponseBuilder\ErrorResponseBuilderInterface;
use Pyz\Glue\TaskBackendApi\Processor\ResponseBuilder\TaskResponseBuilderInterface;
use Symfony\Component\HttpFoundation\Response;

class TaskReader implements TaskReaderInterface
{
    /**
     * @var \Pyz\Glue\TaskBackendApi\Dependency\Facade\TaskBackendApiToTaskFacadeInterface
     */
    protected TaskBackendApiToTaskFacadeInterface $taskFacade;

    /**
     * @var \Pyz\Glue\TaskBackendApi\Processor\ResponseBuilder\TaskResponseBuilderInterface
     */
    protected TaskResponseBuilderInterface $taskResponseBuilder;

    /**
     * @var \Pyz\Glue\TaskBackendApi\Processor\ResponseBuilder\ErrorResponseBuilderInterface
     */
    protected ErrorResponseBuilderInterface $errorResponseBuilder;

    /**
     * @var \Pyz\Glue\TaskBackendApi\Mapper\TaskMapperInterface
     */
    protected TaskMapperInterface $taskMapper;

    /**
     * @param \Pyz\Glue\TaskBackendApi\Dependency\Facade\TaskBackendApiToTaskFacadeInterface $taskFacade
     * @param \Pyz\Glue\TaskBackendApi\Processor\ResponseBuilder\TaskResponseBuilderInterface $taskResponseBuilder
     * @param \Pyz\Glue\TaskBackendApi\Processor\ResponseBuilder\ErrorResponseBuilderInterface $errorResponseBuilder
     * @param \Pyz\Glue\TaskBackendApi\Mapper\TaskMapperInterface $taskMapper
     */
    public function __construct(
        TaskBackendApiToTaskFacadeInterface $taskFacade,
        TaskResponseBuilderInterface $taskResponseBuilder,
        ErrorResponseBuilderInterface $errorResponseBuilder,
        TaskMapperInterface $taskMapper,
    ) {
        $this->taskFacade = $taskFacade;
        $this->taskResponseBuilder = $taskResponseBuilder;
        $this->errorResponseBuilder = $errorResponseBuilder;
        $this->taskMapper = $taskMapper;
    }

    /**
     * @inheritDoc
     */
    public function getTaskCollection(GlueRequestTransfer $glueRequestTransfer): GlueResponseTransfer
    {
        $taskConditionsTransfer = $this->taskMapper->mapGlueFilterTransfersToTaskConditionsTransfer($glueRequestTransfer->getFilters(), new TaskConditionsTransfer());
        $taskSearchConditionsTransfer = $this->taskMapper->mapGlueRequestToTaskSearchConditionsTransfer($glueRequestTransfer, new TaskSearchConditionsTransfer());

        $taskCriteriaTransfer = (new TaskCriteriaTransfer())
            ->setPagination($glueRequestTransfer->getPagination())
            ->setSortCollection($glueRequestTransfer->getSortings())
            ->setTaskConditions($taskConditionsTransfer)
            ->setTaskSearchConditions($taskSearchConditionsTransfer);

        $taskTransfers = $this->taskFacade->getTaskCollection($taskCriteriaTransfer)->getTasks();

        $glueResponseTransfer = $this->taskResponseBuilder->createTaskResponse($taskTransfers);

        return $glueResponseTransfer->setPagination($glueRequestTransfer->getPagination())->setSortings($glueRequestTransfer->getSortings());
    }

    /**
     * @inheritDoc
     */
    public function getTask(GlueRequestTransfer $glueRequestTransfer): GlueResponseTransfer
    {
        $taskConditionsTransfer = (new TaskConditionsTransfer())->addIdTask((int)$glueRequestTransfer->getResourceOrFail()->getIdOrFail());
        $taskCriteriaTransfer = (new TaskCriteriaTransfer())->setTaskConditions($taskConditionsTransfer);

        $taskTransfers = $this->taskFacade->getTaskCollection($taskCriteriaTransfer)->getTasks();

        if ($taskTransfers->count() === 1) {
            return $this->taskResponseBuilder->createTaskResponse(
                new ArrayObject([$taskTransfers->getIterator()->current()]),
            );
        }

        return $this->errorResponseBuilder->createErrorResponseFromErrorMessage('Task not found.', Response::HTTP_NOT_FOUND);
    }
}
