<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Task\Business;

use Pyz\Zed\Task\Business\Deleter\TaskDeleter;
use Pyz\Zed\Task\Business\Deleter\TaskDeleterInterface;
use Pyz\Zed\Task\Business\Mapper\TaskMapper;
use Pyz\Zed\Task\Business\Reader\TaskReader;
use Pyz\Zed\Task\Business\Reader\TaskReaderInterface;
use Pyz\Zed\Task\Business\Sender\TaskEmailSender;
use Pyz\Zed\Task\Business\Sender\TaskEmailSenderInterface;
use Pyz\Zed\Task\Business\Validator\TaskValidator;
use Pyz\Zed\Task\Business\Validator\TaskValidatorInterface;
use Pyz\Zed\Task\Business\Writer\TaskWriter;
use Pyz\Zed\Task\Business\Writer\TaskWriterInterface;
use Pyz\Zed\Task\Dependency\Facade\TaskToMailFacadeInterface;
use Pyz\Zed\Task\Dependency\Facade\TaskToUserFacadeInterface;
use Pyz\Zed\Task\Dependency\Service\TaskToUtilValidateServiceInterface;
use Pyz\Zed\Task\TaskDependencyProvider;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @method \Pyz\Zed\Task\Persistence\TaskEntityManagerInterface getEntityManager()()
 * @method \Pyz\Zed\Task\Persistence\TaskRepositoryInterface getRepository()
 * @method \Pyz\Zed\Task\TaskConfig getConfig()
 */
class TaskBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \Pyz\Zed\Task\Business\Reader\TaskReaderInterface
     */
    public function createTaskReader(): TaskReaderInterface
    {
        return new TaskReader(
            $this->getRepository(),
        );
    }

    /**
     * @return \Pyz\Zed\Task\Business\Writer\TaskWriterInterface
     */
    public function createTaskWriter(): TaskWriterInterface
    {
        return new TaskWriter($this->getEntityManager(), $this->createTaskValidator());
    }

    /**
     * @return \Pyz\Zed\Task\Business\Deleter\TaskDeleterInterface
     */
    public function createTaskDeleter(): TaskDeleterInterface
    {
        return new TaskDeleter($this->getEntityManager(), $this->createTaskMapper());
    }

    /**
     * @return \Pyz\Zed\Task\Business\Mapper\TaskMapper
     */
    public function createTaskMapper(): TaskMapper
    {
        return new TaskMapper();
    }

    /**
     * @return \Pyz\Zed\Task\Business\Validator\TaskValidatorInterface
     */
    public function createTaskValidator(): TaskValidatorInterface
    {
        return new TaskValidator($this->getValidator(), $this->getUserFacade());
    }

    /**
     * @return \Pyz\Zed\Task\Business\Sender\TaskEmailSenderInterface
     */
    public function createTaskEmailSender(): TaskEmailSenderInterface
    {
        return new TaskEmailSender(
            $this->getUserFacade(),
            $this->getMailFacade(),
            $this->getUtilValidateService(),
        );
    }

    /**
     * @return \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    public function getValidator(): ValidatorInterface
    {
        return Validation::createValidator();
    }

    /**
     * @return \Pyz\Zed\Task\Dependency\Facade\TaskToUserFacadeInterface
     */
    public function getUserFacade(): TaskToUserFacadeInterface
    {
        return $this->getProvidedDependency(TaskDependencyProvider::FACADE_USER);
    }

    /**
     * @return \Pyz\Zed\Task\Dependency\Facade\TaskToMailFacadeInterface
     */
    public function getMailFacade(): TaskToMailFacadeInterface
    {
        return $this->getProvidedDependency(TaskDependencyProvider::FACADE_MAIL);
    }

    /**
     * @return \Pyz\Zed\Task\Dependency\Service\TaskToUtilValidateServiceInterface
     */
    public function getUtilValidateService(): TaskToUtilValidateServiceInterface
    {
        return $this->getProvidedDependency(TaskDependencyProvider::SERVICE_UTIL_VALIDATE);
    }
}
