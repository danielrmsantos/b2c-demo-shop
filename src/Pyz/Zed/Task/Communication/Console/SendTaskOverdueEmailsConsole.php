<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Task\Communication\Console;

use ArrayObject;
use DateTime;
use Generated\Shared\Transfer\TaskCollectionRequestTransfer;
use Generated\Shared\Transfer\TaskConditionsTransfer;
use Generated\Shared\Transfer\TaskCriteriaTransfer;
use Spryker\Zed\Kernel\BundleConfigResolverAwareTrait;
use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method \Pyz\Zed\Task\Business\TaskFacadeInterface getFacade()
 * @method \Pyz\Zed\Task\Persistence\TaskRepositoryInterface getRepository()
 * @method \Pyz\Zed\Task\TaskConfig getConfig()
 */
class SendTaskOverdueEmailsConsole extends Console
{
    use BundleConfigResolverAwareTrait;

    /**
     * @var string
     */
    public const COMMAND_NAME = 'tasks:send-overdue-emails';

    /**
     * @var string
     */
    public const COMMAND_DESCRIPTION = 'Sends emails about overdue tasks.';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_TEMPLATE = '<error>Failed to send Overdue email for Task %s: %s</error>';

    /**
     * @return void
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName(static::COMMAND_NAME)
            ->setDescription(static::COMMAND_DESCRIPTION);
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $taskCollectionTransfer = $this->getFacade()->getTaskCollection(
            $this->createTaskCriteriaTransfer(),
        );

        $taskCollectionResponseTransfer = $this->getFacade()->sendTaskOverdueEmails(
            (new TaskCollectionRequestTransfer())->setTasks($taskCollectionTransfer->getTasks()),
        );

        /** @var \ArrayObject<array-key, \Generated\Shared\Transfer\ErrorTransfer> $errorTransfers */
        $errorTransfers = $taskCollectionResponseTransfer->getErrors();

        if ($errorTransfers->count() === 0) {
            return static::CODE_SUCCESS;
        }

        $this->outputErrors($errorTransfers, $output);

        return static::CODE_ERROR;
    }

    /**
     * @return \Generated\Shared\Transfer\TaskCriteriaTransfer
     */
    protected function createTaskCriteriaTransfer(): TaskCriteriaTransfer
    {
        $taskConditionsTransfer = (new TaskConditionsTransfer())
            ->setDueDate((new DateTime())->setTimestamp(strtotime('-1 day'))->format('Y-m-d'));

        return (new TaskCriteriaTransfer())->setTaskConditions($taskConditionsTransfer);
    }

    /**
     * @param \ArrayObject<array-key, \Generated\Shared\Transfer\ErrorTransfer> $errorTransfers
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    protected function outputErrors(ArrayObject $errorTransfers, OutputInterface $output): void
    {
        foreach ($errorTransfers as $errorTransfer) {
            $output->writeln(
                sprintf(
                    static::ERROR_MESSAGE_TEMPLATE,
                    $errorTransfer->getEntityIdentifierOrFail(),
                    $errorTransfer->getMessageOrFail(),
                ),
            );
        }
    }
}
