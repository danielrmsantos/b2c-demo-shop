<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Glue\TaskBackendApi\Processor\ResponseBuilder;

use ArrayObject;
use Generated\Shared\Transfer\GlueResourceTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Pyz\Glue\TaskBackendApi\Mapper\TaskMapperInterface;
use Symfony\Component\HttpFoundation\Response;

class TaskResponseBuilder implements TaskResponseBuilderInterface
{
 /**
  * @var \Pyz\Glue\TaskBackendApi\Mapper\TaskMapperInterface
  */
    protected TaskMapperInterface $taskMapper;

    /**
     * @param \Pyz\Glue\TaskBackendApi\Mapper\TaskMapperInterface $taskMapper
     */
    public function __construct(TaskMapperInterface $taskMapper)
    {
        $this->taskMapper = $taskMapper;
    }

    /**
     * @param \ArrayObject<int, \Generated\Shared\Transfer\TaskTransfer> $taskTransfers
     *
     * @return \Generated\Shared\Transfer\GlueResponseTransfer
     */
    public function createTaskResponse(ArrayObject $taskTransfers): GlueResponseTransfer
    {
        $glueResponseTransfer = new GlueResponseTransfer();

        foreach ($taskTransfers as $taskTransfer) {
            $glueResponseTransfer->addResource(
                $this->taskMapper->mapTaskTransferToTaskResourceTransfer($taskTransfer, new GlueResourceTransfer()),
            );
        }

        return $glueResponseTransfer;
    }

    /**
     * @return \Generated\Shared\Transfer\GlueResponseTransfer
     */
    public function createNoContentResponse(): GlueResponseTransfer
    {
        return (new GlueResponseTransfer())->setHttpStatus(Response::HTTP_NO_CONTENT);
    }
}
