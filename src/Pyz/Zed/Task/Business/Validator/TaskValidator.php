<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Task\Business\Validator;

use Closure;
use Generated\Shared\Transfer\ErrorTransfer;
use Generated\Shared\Transfer\TaskCollectionResponseTransfer;
use Generated\Shared\Transfer\TaskTransfer;
use Generated\Shared\Transfer\UserConditionsTransfer;
use Generated\Shared\Transfer\UserCriteriaTransfer;
use Pyz\Zed\Task\Dependency\Facade\TaskToUserFacadeInterface;
use Pyz\Zed\Task\TaskConfig;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskValidator implements TaskValidatorInterface
{
    /**
     * @var string
     */
    public const ASSIGNED_USER_NOT_FOUND_MESSAGE = 'Assigned User not found.';

    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    protected ValidatorInterface $validator;

    /**
     * @var \Pyz\Zed\Task\Dependency\Facade\TaskToUserFacadeInterface
     */
    protected TaskToUserFacadeInterface $userFacade;

    /**
     * @param \Symfony\Component\Validator\Validator\ValidatorInterface $validator
     * @param \Pyz\Zed\Task\Dependency\Facade\TaskToUserFacadeInterface $userFacade
     */
    public function __construct(ValidatorInterface $validator, TaskToUserFacadeInterface $userFacade)
    {
        $this->validator = $validator;
        $this->userFacade = $userFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\TaskCollectionResponseTransfer $taskCollectionResponseTransfer
     *
     * @return \Generated\Shared\Transfer\TaskCollectionResponseTransfer
     */
    public function validate(TaskCollectionResponseTransfer $taskCollectionResponseTransfer): TaskCollectionResponseTransfer
    {
        foreach ($taskCollectionResponseTransfer->getTasks() as $task) {
            $constraintViolationList = $this->executeTaskValidation($task);

            if ($constraintViolationList->count()) {
                $taskCollectionResponseTransfer = $this->buildErrorMessages($constraintViolationList, $taskCollectionResponseTransfer);
            }
        }

        $taskCollectionResponseTransfer->setIsSuccessful($taskCollectionResponseTransfer->getErrors()->count() === 0);

        return $taskCollectionResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\TaskTransfer $taskTransfer
     *
     * @return \Symfony\Component\Validator\ConstraintViolationListInterface
     */
    protected function executeTaskValidation(TaskTransfer $taskTransfer): ConstraintViolationListInterface
    {
        $constraintCollection = new Collection(
            $this->getTaskContraints(),
        );

        return $this->validator->validate($taskTransfer->toArray(true, true), $constraintCollection);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getTaskContraints(): array
    {
        return [
            TaskTransfer::ID_TASK => [
                new Optional(),
                new Type(['type' => 'integer']),
            ],
            TaskTransfer::ID_AUTHOR => [
                new NotBlank(),
                new Type(['type' => 'integer']),
            ],
            TaskTransfer::ID_ASSIGNED_USER => [
                new Optional(),
                new Type(['type' => 'integer']),
                new Callback(['callback' => $this->checkUserExists()]),
            ],
            TaskTransfer::TITLE => [
                new NotBlank(),
                new Length(['max' => TaskConfig::TASK_TITLE_LENGTH]),
            ],
            TaskTransfer::DESCRIPTION => [
                new Optional(),
            ],
            TaskTransfer::STATUS => [
                new Length(['max' => TaskConfig::TASK_STATUS_LENGTH]),
                new Choice([
                    TaskConfig::TASK_STATUS_TO_DO,
                    TaskConfig::TASK_STATUS_IN_PROGRESS,
                    TaskConfig::TASK_STATUS_COMPLETED,
                    TaskConfig::TASK_STATUS_OVERDUE,
                ]),
            ],
            TaskTransfer::DUE_DATE => [
                new NotBlank(),
                new Date(),
            ],
        ];
    }

    /**
     * @param \Symfony\Component\Validator\ConstraintViolationListInterface $constraintViolationList
     * @param \Generated\Shared\Transfer\TaskCollectionResponseTransfer $taskCollectionResponseTransfer
     *
     * @return \Generated\Shared\Transfer\TaskCollectionResponseTransfer
     */
    protected function buildErrorMessages(
        ConstraintViolationListInterface $constraintViolationList,
        TaskCollectionResponseTransfer $taskCollectionResponseTransfer,
    ): TaskCollectionResponseTransfer {
        foreach ($constraintViolationList as $violation) {
            $errorTransfer = new ErrorTransfer();
            $errorMessage = sprintf('Error found at %s => %s', $violation->getPropertyPath(), $violation->getMessage());
            $errorTransfer->setMessage($errorMessage);
            $taskCollectionResponseTransfer->addError($errorTransfer);
        }

        return $taskCollectionResponseTransfer;
    }

    /**
     * @return \Closure
     */
    protected function checkUserExists(): Closure
    {
        return function ($userId, ExecutionContextInterface $contextInterface): void {
            if (!$userId) {
                return;
            }

            $userConditionsTransfer = (new UserConditionsTransfer())->setUserIds([$userId]);
            $userCriteriaTransfer = (new UserCriteriaTransfer())->setUserConditions($userConditionsTransfer);
            $userCollection = $this->userFacade->getUserCollection($userCriteriaTransfer);

            if (!$userCollection->getUsers()->count()) {
                $contextInterface->addViolation(static::ASSIGNED_USER_NOT_FOUND_MESSAGE);
            }
        };
    }
}
