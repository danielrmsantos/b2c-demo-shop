<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Glue\TaskBackendApi\Mapper;

use ArrayObject;
use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResourceTransfer;
use Generated\Shared\Transfer\TaskCollectionRequestTransfer;
use Generated\Shared\Transfer\TaskConditionsTransfer;
use Generated\Shared\Transfer\TaskSearchConditionsTransfer;
use Generated\Shared\Transfer\TaskTransfer;

interface TaskMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\TaskTransfer $taskTransfer
     * @param \Generated\Shared\Transfer\GlueResourceTransfer $glueResourceTransfer
     *
     * @return \Generated\Shared\Transfer\GlueResourceTransfer
     */
    public function mapTaskTransferToTaskResourceTransfer(
        TaskTransfer $taskTransfer,
        GlueResourceTransfer $glueResourceTransfer,
    ): GlueResourceTransfer;

    /**
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     * @param \Generated\Shared\Transfer\TaskCollectionRequestTransfer $taskCollectionRequestTransfer
     *
     * @return \Generated\Shared\Transfer\TaskCollectionRequestTransfer
     */
    public function mapGlueRequestTransferToTaskCollectionRequestTransfer(
        GlueRequestTransfer $glueRequestTransfer,
        TaskCollectionRequestTransfer $taskCollectionRequestTransfer,
    ): TaskCollectionRequestTransfer;

    /**
     * @param \ArrayObject<array-key, \Generated\Shared\Transfer\GlueFilterTransfer> $glueFilterTransfers
     * @param \Generated\Shared\Transfer\TaskConditionsTransfer $taskConditionsTransfer
     *
     * @return \Generated\Shared\Transfer\TaskConditionsTransfer
     */
    public function mapGlueFilterTransfersToTaskConditionsTransfer(
        ArrayObject $glueFilterTransfers,
        TaskConditionsTransfer $taskConditionsTransfer,
    ): TaskConditionsTransfer;

    /**
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     * @param \Generated\Shared\Transfer\TaskSearchConditionsTransfer $taskSearchConditionsTransfer
     *
     * @return \Generated\Shared\Transfer\TaskSearchConditionsTransfer
     */
    public function mapGlueRequestToTaskSearchConditionsTransfer(
        GlueRequestTransfer $glueRequestTransfer,
        TaskSearchConditionsTransfer $taskSearchConditionsTransfer,
    ): TaskSearchConditionsTransfer;
}
