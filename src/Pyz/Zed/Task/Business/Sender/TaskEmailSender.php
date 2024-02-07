<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Task\Business\Sender;

use Generated\Shared\Transfer\ErrorTransfer;
use Generated\Shared\Transfer\MailTransfer;
use Generated\Shared\Transfer\TaskCollectionRequestTransfer;
use Generated\Shared\Transfer\TaskCollectionResponseTransfer;
use Generated\Shared\Transfer\TaskMailDataTransfer;
use Generated\Shared\Transfer\TaskTransfer;
use Generated\Shared\Transfer\UserConditionsTransfer;
use Generated\Shared\Transfer\UserCriteriaTransfer;
use Generated\Shared\Transfer\UserTransfer;
use Pyz\Zed\Task\Communication\Plugin\Mail\TaskOverdueMailTypeBuilderPlugin;
use Pyz\Zed\Task\Dependency\Facade\TaskToMailFacadeInterface;
use Pyz\Zed\Task\Dependency\Facade\TaskToUserFacadeInterface;
use Pyz\Zed\Task\Dependency\Service\TaskToUtilValidateServiceInterface;

class TaskEmailSender implements TaskEmailSenderInterface
{
    /**
     * @var \Pyz\Zed\Task\Dependency\Facade\TaskToUserFacadeInterface
     */
    protected TaskToUserFacadeInterface $userFacade;

    /**
     * @var \Pyz\Zed\Task\Dependency\Facade\TaskToMailFacadeInterface
     */
    protected TaskToMailFacadeInterface $mailFacade;

    /**
     * @var \Pyz\Zed\Task\Dependency\Service\TaskToUtilValidateServiceInterface
     */
    protected TaskToUtilValidateServiceInterface $utilValidateService;

    /**
     * @param \Pyz\Zed\Task\Dependency\Facade\TaskToUserFacadeInterface $userFacade
     * @param \Pyz\Zed\Task\Dependency\Facade\TaskToMailFacadeInterface $mailFacade
     * @param \Pyz\Zed\Task\Dependency\Service\TaskToUtilValidateServiceInterface $utilValidateService
     */
    public function __construct(
        TaskToUserFacadeInterface $userFacade,
        TaskToMailFacadeInterface $mailFacade,
        TaskToUtilValidateServiceInterface $utilValidateService,
    ) {
        $this->userFacade = $userFacade;
        $this->mailFacade = $mailFacade;
        $this->utilValidateService = $utilValidateService;
    }

    /**
     * @inheritDoc
     */
    public function sendTaskOverdueEmails(TaskCollectionRequestTransfer $taskCollectionRequestTransfer): TaskCollectionResponseTransfer
    {
        $taskCollectionResponseTransfer = new TaskCollectionResponseTransfer();

        foreach ($taskCollectionRequestTransfer->getTasks() as $taskTransfer) {
            $userTransfer = $this->getUserTransfer($taskTransfer);

            $userEmailIsValid = $this->utilValidateService->isEmailFormatValid($userTransfer->getUsernameOrFail());

            if (!$userEmailIsValid) {
                $errorTransfer = $this->buildErrorTransfer($taskTransfer, $userTransfer);
                $taskCollectionResponseTransfer->addError($errorTransfer);

                continue;
            }

            $mailTransfer = $this->buildMailTransfer($taskTransfer, $userTransfer);
            $this->mailFacade->handleMail($mailTransfer);

            $taskCollectionResponseTransfer->addTask($taskTransfer);
        }

        $taskCollectionResponseTransfer->setIsSuccessful(true);

        return $taskCollectionResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\TaskTransfer $taskTransfer
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     *
     * @return \Generated\Shared\Transfer\MailTransfer
     */
    protected function buildMailTransfer(TaskTransfer $taskTransfer, UserTransfer $userTransfer): MailTransfer
    {
        $mailDataTransfer = (new TaskMailDataTransfer())
            ->setTitle(sprintf('Hello %s', $userTransfer->getFirstNameOrFail()))
            ->setHead('You have an overdue task.')
            ->setBody(sprintf('Task with title `%s` is overdue since `%s`', $taskTransfer->getTitleOrFail(), $taskTransfer->getDueDateOrFail()));

        return (new MailTransfer())
            ->setType(TaskOverdueMailTypeBuilderPlugin::MAIL_TYPE)
            ->setTask($taskTransfer)
            ->setUser($userTransfer)
            ->setTaskMailData($mailDataTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\TaskTransfer $taskTransfer
     *
     * @return \Generated\Shared\Transfer\UserTransfer
     */
    protected function getUserTransfer(TaskTransfer $taskTransfer): UserTransfer
    {
        $userConditionsTransfer = (new UserConditionsTransfer())->setUserIds([$taskTransfer->getIdAssignedUser() ?? $taskTransfer->getIdAuthor()]);
        $userCriteriaTransfer = (new UserCriteriaTransfer())->setUserConditions($userConditionsTransfer);
        $userCollectionTransfer = $this->userFacade->getUserCollection($userCriteriaTransfer);

        return $userCollectionTransfer->getUsers()->getIterator()->current();
    }

    /**
     * @param \Generated\Shared\Transfer\TaskTransfer $taskTransfer
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     *
     * @return \Generated\Shared\Transfer\ErrorTransfer
     */
    protected function buildErrorTransfer(TaskTransfer $taskTransfer, UserTransfer $userTransfer): ErrorTransfer
    {
        return (new ErrorTransfer())
            ->setEntityIdentifier((string)$taskTransfer->getIdTask())
            ->setMessage(sprintf('User email `%s` is not valid.', $userTransfer->getUsernameOrFail()));
    }
}
