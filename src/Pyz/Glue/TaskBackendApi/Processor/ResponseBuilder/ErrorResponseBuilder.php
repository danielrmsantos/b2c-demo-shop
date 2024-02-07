<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Glue\TaskBackendApi\Processor\ResponseBuilder;

use ArrayObject;
use Generated\Shared\Transfer\ErrorTransfer;
use Generated\Shared\Transfer\GlueErrorTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Symfony\Component\HttpFoundation\Response;

class ErrorResponseBuilder implements ErrorResponseBuilderInterface
{
 /**
  * @inheritDoc
  *
  * @param \ArrayObject<int, \Generated\Shared\Transfer\ErrorTransfer> $errorTransfers
  * @param int|null $httpStatus
  */
    public function createErrorResponse(ArrayObject $errorTransfers, ?int $httpStatus = null): GlueResponseTransfer
    {
        $errorCode = $httpStatus ?: Response::HTTP_BAD_REQUEST;

        $glueResponseTransfer = (new GlueResponseTransfer())->setHttpStatus($errorCode);

        foreach ($errorTransfers as $errorTransfer) {
            $glueErrorTransfer = (new GlueErrorTransfer())
                ->setMessage($errorTransfer->getMessage())
                ->setStatus($errorCode);

            $glueResponseTransfer->addError($glueErrorTransfer);
        }

        return $glueResponseTransfer;
    }

    /**
     * @inheritDoc
     */
    public function createErrorResponseFromErrorMessage(string $errorMessage, ?int $httpStatus = null): GlueResponseTransfer
    {
        $errorTransfer = (new ErrorTransfer())->setMessage($errorMessage);

        $glueResponseTransfer = $this->createErrorResponse(new ArrayObject([$errorTransfer]), $httpStatus);

        if ($httpStatus) {
            $glueResponseTransfer->setHttpStatus($httpStatus);
        }

        return $glueResponseTransfer;
    }
}
