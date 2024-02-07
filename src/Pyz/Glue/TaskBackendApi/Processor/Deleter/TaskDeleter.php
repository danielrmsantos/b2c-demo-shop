<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Glue\TaskBackendApi\Processor\Deleter;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Generated\Shared\Transfer\TaskCollectionDeleteCriteriaTransfer;
use Pyz\Glue\TaskBackendApi\Dependency\Facade\TaskBackendApiToTaskFacadeInterface;
use Pyz\Glue\TaskBackendApi\Processor\ResponseBuilder\TaskResponseBuilderInterface;

class TaskDeleter implements TaskDeleterInterface
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
     * @param \Pyz\Glue\TaskBackendApi\Dependency\Facade\TaskBackendApiToTaskFacadeInterface $taskFacade
     * @param \Pyz\Glue\TaskBackendApi\Processor\ResponseBuilder\TaskResponseBuilderInterface $taskResponseBuilder
     */
    public function __construct(TaskBackendApiToTaskFacadeInterface $taskFacade, TaskResponseBuilderInterface $taskResponseBuilder)
    {
        $this->taskFacade = $taskFacade;
        $this->taskResponseBuilder = $taskResponseBuilder;
    }

    /**
     * @inheritDoc
     */
    public function deleteTaskCollection(GlueRequestTransfer $glueRequestTransfer): GlueResponseTransfer
    {
        $taskCollectionDeleteCriteria = (new TaskCollectionDeleteCriteriaTransfer())->addIdTask((int)$glueRequestTransfer->getResourceOrFail()->getIdOrFail());

        $this->taskFacade->deleteTaskCollection($taskCollectionDeleteCriteria);

        return $this->taskResponseBuilder->createNoContentResponse();
    }
}
