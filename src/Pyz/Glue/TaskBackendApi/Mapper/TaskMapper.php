<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Glue\TaskBackendApi\Mapper;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResourceTransfer;
use Generated\Shared\Transfer\TaskBackendApiAttributesTransfer;
use Generated\Shared\Transfer\TaskCollectionRequestTransfer;
use Generated\Shared\Transfer\TaskTransfer;
use Pyz\Glue\TaskBackendApi\Dependency\Service\TaskBackendApiToUtilEncodingServiceInterface;
use Pyz\Glue\TaskBackendApi\TaskBackendApiConfig;

class TaskMapper implements TaskMapperInterface
{
    /**
     * @var \Pyz\Glue\TaskBackendApi\Dependency\Service\TaskBackendApiToUtilEncodingServiceInterface
     */
    protected TaskBackendApiToUtilEncodingServiceInterface $utilEncodingService;

    /**
     * @param \Pyz\Glue\TaskBackendApi\Dependency\Service\TaskBackendApiToUtilEncodingServiceInterface $utilEncodingService
     */
    public function __construct(TaskBackendApiToUtilEncodingServiceInterface $utilEncodingService)
    {
        $this->utilEncodingService = $utilEncodingService;
    }

    /**
     * @param \Generated\Shared\Transfer\TaskTransfer $taskTransfer
     * @param \Generated\Shared\Transfer\GlueResourceTransfer $glueResourceTransfer
     *
     * @return \Generated\Shared\Transfer\GlueResourceTransfer
     */
    public function mapTaskTransferToTaskResourceTransfer(
        TaskTransfer $taskTransfer,
        GlueResourceTransfer $glueResourceTransfer,
    ): GlueResourceTransfer {
        $taskBackendApiAttributesTransfer = (new TaskBackendApiAttributesTransfer())->fromArray($taskTransfer->toArray(), true);

        return $glueResourceTransfer
            ->setType(TaskBackendApiConfig::RESOURCE_TASK)
            ->setId((string)$taskTransfer->getIdTaskOrFail())
            ->setAttributes($taskBackendApiAttributesTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     * @param \Generated\Shared\Transfer\TaskCollectionRequestTransfer $taskCollectionRequestTransfer
     *
     * @return \Generated\Shared\Transfer\TaskCollectionRequestTransfer
     */
    public function mapGlueRequestToTaskCollectionRequestTransfer(
        GlueRequestTransfer $glueRequestTransfer,
        TaskCollectionRequestTransfer $taskCollectionRequestTransfer,
    ): TaskCollectionRequestTransfer {
        $dataCollection = $this->utilEncodingService->decodeJson($glueRequestTransfer->getContent(), true)['data'] ?? null;

        if ($dataCollection === null || $dataCollection === []) {
            return $taskCollectionRequestTransfer;
        }

        if ($glueRequestTransfer->getResourceOrFail()->getId() !== null) {
            return $this->mapSingleDataCollectionToTaskCollectionTransfer($dataCollection, $glueRequestTransfer, $taskCollectionRequestTransfer);
        }

        return $this->mapDataCollectionToTaskCollectionTransfer($dataCollection, $glueRequestTransfer, $taskCollectionRequestTransfer);
    }

    /**
     * @param array<mixed> $dataCollection
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     * @param \Generated\Shared\Transfer\TaskCollectionRequestTransfer $taskCollectionRequestTransfer
     *
     * @return \Generated\Shared\Transfer\TaskCollectionRequestTransfer
     */
    protected function mapSingleDataCollectionToTaskCollectionTransfer(
        array $dataCollection,
        GlueRequestTransfer $glueRequestTransfer,
        TaskCollectionRequestTransfer $taskCollectionRequestTransfer,
    ): TaskCollectionRequestTransfer {
        $taskTransfer = $this->buildTaskTransfer($dataCollection, $glueRequestTransfer);
        $taskTransfer->setIdTask((int)$glueRequestTransfer->getResourceOrFail()->getId());
        $taskCollectionRequestTransfer->addTask($taskTransfer);

        return $taskCollectionRequestTransfer;
    }

    /**
     * @param array<mixed> $dataCollection
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     * @param \Generated\Shared\Transfer\TaskCollectionRequestTransfer $taskCollectionRequestTransfer
     *
     * @return \Generated\Shared\Transfer\TaskCollectionRequestTransfer
     */
    protected function mapDataCollectionToTaskCollectionTransfer(
        array $dataCollection,
        GlueRequestTransfer $glueRequestTransfer,
        TaskCollectionRequestTransfer $taskCollectionRequestTransfer,
    ): TaskCollectionRequestTransfer {
        foreach ($dataCollection as $item) {
            $taskTransfer = $this->buildTaskTransfer($item, $glueRequestTransfer);
            $taskCollectionRequestTransfer->addTask($taskTransfer);
        }

        return $taskCollectionRequestTransfer;
    }

    /**
     * @param array<mixed> $dataCollection
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return \Generated\Shared\Transfer\TaskTransfer
     */
    protected function buildTaskTransfer(array $dataCollection, GlueRequestTransfer $glueRequestTransfer): TaskTransfer
    {
        $taskTransfer = (new TaskTransfer())->fromArray($dataCollection, true);
        $taskTransfer->setIdAuthor($this->getUserIdFromGlueRequest($glueRequestTransfer));

        return $taskTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return int
     */
    protected function getUserIdFromGlueRequest(GlueRequestTransfer $glueRequestTransfer): int
    {
        return $glueRequestTransfer->getRequestUserOrFail()->getSurrogateIdentifierOrFail();
    }
}
