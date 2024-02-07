<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Glue\TaskBackendApi\Mapper;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResourceTransfer;
use Generated\Shared\Transfer\TaskCollectionRequestTransfer;
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
    public function mapGlueRequestToTaskCollectionRequestTransfer(
        GlueRequestTransfer $glueRequestTransfer,
        TaskCollectionRequestTransfer $taskCollectionRequestTransfer,
    ): TaskCollectionRequestTransfer;
}
