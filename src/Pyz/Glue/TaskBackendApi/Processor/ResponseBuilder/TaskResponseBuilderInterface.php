<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Glue\TaskBackendApi\Processor\ResponseBuilder;

use ArrayObject;
use Generated\Shared\Transfer\GlueResponseTransfer;

interface TaskResponseBuilderInterface
{
    /**
     * @param \ArrayObject<array-key, \Generated\Shared\Transfer\TaskTransfer> $taskTransfers
     *
     * @return \Generated\Shared\Transfer\GlueResponseTransfer
     */
    public function createTaskResponse(ArrayObject $taskTransfers): GlueResponseTransfer;

    /**
     * @return \Generated\Shared\Transfer\GlueResponseTransfer
     */
    public function createNoContentResponse(): GlueResponseTransfer;
}
