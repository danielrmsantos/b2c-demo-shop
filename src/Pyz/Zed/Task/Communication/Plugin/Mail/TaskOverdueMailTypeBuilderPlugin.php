<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Task\Communication\Plugin\Mail;

use Generated\Shared\Transfer\MailRecipientTransfer;
use Generated\Shared\Transfer\MailTemplateTransfer;
use Generated\Shared\Transfer\MailTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\MailExtension\Dependency\Plugin\MailTypeBuilderPluginInterface;

/**
 * @method \Pyz\Zed\Task\TaskConfig getConfig()
 * @method \Pyz\Zed\Task\Business\TaskFacadeInterface getFacade()
 */
class TaskOverdueMailTypeBuilderPlugin extends AbstractPlugin implements MailTypeBuilderPluginInterface
{
    /**
     * @var string
     */
    public const MAIL_TYPE = 'TASK_OVERDUE_MAIL';

    /**
     * @var string
     */
    protected const MAIL_TEMPLATE_HTML = 'Task/mail/overdue.html.twig';

    /**
     * @var string
     */
    protected const MAIL_TEMPLATE_TEXT = 'Task/mail/overdue.text.twig';

    /**
     * @var string
     */
    protected const MAIL_SUBJECT = 'You have an overdue task.';

    /**
     * @var string
     */
    protected const PARAMETER_NAME = '%name%';

    /**
     * {@inheritDoc}
     * - Returns the name of mail for Task overdue mail.
     *
     * @api
     *
     * @return string
     */
    public function getName(): string
    {
        return static::MAIL_TYPE;
    }

    /**
     * {@inheritDoc}
     * - Requires `Mail.user.userName` to be set.
     * - Builds the `MailTransfer` with data for Task overdue mail.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MailTransfer $mailTransfer
     *
     * @return \Generated\Shared\Transfer\MailTransfer
     */
    public function build(MailTransfer $mailTransfer): MailTransfer
    {
        $userTransfer = $mailTransfer->requireUser()->getUserOrFail();

        return $mailTransfer
            ->setSubject(static::MAIL_SUBJECT)
            ->addTemplate(
                (new MailTemplateTransfer())
                    ->setName(static::MAIL_TEMPLATE_HTML)
                    ->setIsHtml(true),
            )
            ->addTemplate(
                (new MailTemplateTransfer())
                    ->setName(static::MAIL_TEMPLATE_TEXT)
                    ->setIsHtml(false),
            )
            ->addRecipient(
                (new MailRecipientTransfer())
                    ->setEmail($userTransfer->getUsernameOrFail()),
            );
    }
}
