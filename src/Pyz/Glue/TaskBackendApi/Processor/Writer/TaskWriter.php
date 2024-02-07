<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Glue\TaskBackendApi\Processor\Writer;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Generated\Shared\Transfer\TaskCollectionRequestTransfer;
use Pyz\Glue\TaskBackendApi\Dependency\Facade\TaskBackendApiToTaskFacadeInterface;
use Pyz\Glue\TaskBackendApi\Mapper\TaskMapperInterface;
use Pyz\Glue\TaskBackendApi\Processor\ResponseBuilder\ErrorResponseBuilderInterface;
use Pyz\Glue\TaskBackendApi\Processor\ResponseBuilder\TaskResponseBuilderInterface;

class TaskWriter implements TaskWriterInterface
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
    public function createTask(GlueRequestTransfer $glueRequestTransfer): GlueResponseTransfer
    {
        $taskCollectionRequestTransfer = $this->taskMapper->mapGlueRequestToTaskCollectionRequestTransfer($glueRequestTransfer, new TaskCollectionRequestTransfer());

        if (!$taskCollectionRequestTransfer->getTasks()->count()) {
            return $this->errorResponseBuilder->createErrorResponseFromErrorMessage('Wrong request body.');
        }

        $taskCollectionResponseTransfer = $this->taskFacade->createTaskCollection($taskCollectionRequestTransfer);

        $errorTransfers = $taskCollectionResponseTransfer->getErrors();

        if ($errorTransfers->count()) {
            return $this->errorResponseBuilder->createErrorResponse($errorTransfers);
        }

        return $this->taskResponseBuilder->createTaskResponse(
            $taskCollectionResponseTransfer->getTasks(),
        );
    }

    /**
     * @inheritDoc
     */
    public function updateTask(GlueRequestTransfer $glueRequestTransfer): GlueResponseTransfer
    {
        $taskCollectionRequestTransfer = $this->taskMapper->mapGlueRequestToTaskCollectionRequestTransfer($glueRequestTransfer, new TaskCollectionRequestTransfer());

        if (!$taskCollectionRequestTransfer->getTasks()->count()) {
            return $this->errorResponseBuilder->createErrorResponseFromErrorMessage('Wrong request body.');
        }

        $taskCollectionResponseTransfer = $this->taskFacade->updateTaskCollection($taskCollectionRequestTransfer);

        $errorTransfers = $taskCollectionResponseTransfer->getErrors();

        if ($errorTransfers->count()) {
            return $this->errorResponseBuilder->createErrorResponse($errorTransfers);
        }

        return $this->taskResponseBuilder->createTaskResponse(
            $taskCollectionResponseTransfer->getTasks(),
        );
    }
}
