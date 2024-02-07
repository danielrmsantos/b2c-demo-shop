<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PyzTest\Glue\TaskBackendApi;

use Codeception\Actor;
use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueRequestUserTransfer;
use Generated\Shared\Transfer\GlueResourceTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Generated\Shared\Transfer\TaskTransfer;
use Generated\Shared\Transfer\UserTransfer;

/**
 * Inherited Methods
 *
 * @method void wantTo($text)
 * @method void wantToTest($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause($vars = [])
 *
 * @SuppressWarnings(\PyzTest\Glue\TaskBackendApi\PHPMD)
 */
class TaskBackendApiTester extends Actor
{
    use _generated\TaskBackendApiTesterActions;

    /**
     * @var string
     */
    protected const REQUESTED_FORMAT = 'application/json';

    /**
     * @param \Generated\Shared\Transfer\UserTransfer|null $userTransfer
     *
     * @return \Generated\Shared\Transfer\GlueRequestTransfer
     */
    public function haveGlueRequestTransferWithRequestUserTransfer(?UserTransfer $userTransfer = null): GlueRequestTransfer
    {
        $userTransfer = $userTransfer ?? $this->haveUser();
        $glueRequestUserTransfer = (new GlueRequestUserTransfer())->setSurrogateIdentifier($userTransfer->getIdUserOrFail());

        return (new GlueRequestTransfer())
            ->setRequestedFormat(static::REQUESTED_FORMAT)
            ->setResource((new GlueResourceTransfer()))
            ->setRequestUser($glueRequestUserTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\TaskTransfer $taskTransfer
     * @param \Generated\Shared\Transfer\GlueResponseTransfer $glueResponseTransfer
     * @param int $expectedAmountOfResources
     *
     * @return void
     */
    public function assertGlueResponseHasCorrectData(
        TaskTransfer $taskTransfer,
        GlueResponseTransfer $glueResponseTransfer,
        int $expectedAmountOfResources,
    ): void {
        /** @var \Generated\Shared\Transfer\GlueResourceTransfer $glueResourceTransfer */
        $glueResourceTransfer = $glueResponseTransfer->getResources()->getIterator()->current();

        /** @var \Generated\Shared\Transfer\TaskBackendApiAttributesTransfer $taskBackendApiAttributesTransfer */
        $taskBackendApiAttributesTransfer = $glueResourceTransfer->getAttributesOrFail();

        $this->assertSame($taskTransfer->getTitle(), $taskBackendApiAttributesTransfer->getTitle());
        $this->assertSame($taskTransfer->getDescription(), $taskBackendApiAttributesTransfer->getDescription());
        $this->assertSame($taskTransfer->getDueDate(), $taskBackendApiAttributesTransfer->getDueDate());
        $this->assertCount($expectedAmountOfResources, $glueResponseTransfer->getResources());
        $this->assertCount(0, $glueResponseTransfer->getErrors());
    }

    /**
     * @param \Generated\Shared\Transfer\GlueResponseTransfer $glueResponseTransfer
     * @param int $expectedStatus
     * @param string $expectedErrorMessage
     *
     * @return void
     */
    public function assertResponseHasHttpStatusAndErrorMessage(
        GlueResponseTransfer $glueResponseTransfer,
        int $expectedStatus,
        string $expectedErrorMessage,
    ): void {
        /** @var \Generated\Shared\Transfer\GlueErrorTransfer $glueErrorTransfer */
        $glueErrorTransfer = $glueResponseTransfer->getErrors()->getIterator()->current();

        $this->assertSame($expectedStatus, $glueResponseTransfer->getHttpStatus());
        $this->assertStringContainsString($expectedErrorMessage, $glueErrorTransfer->getMessage());
    }
}
