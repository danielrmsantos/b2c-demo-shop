<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Glue\TaskBackendApi;

use Pyz\Glue\TaskBackendApi\Dependency\Facade\TaskBackendApiToTaskFacadeInterface;
use Pyz\Glue\TaskBackendApi\Dependency\Service\TaskBackendApiToUtilEncodingServiceInterface;
use Pyz\Glue\TaskBackendApi\Mapper\TaskMapper;
use Pyz\Glue\TaskBackendApi\Mapper\TaskMapperInterface;
use Pyz\Glue\TaskBackendApi\Processor\Deleter\TaskDeleter;
use Pyz\Glue\TaskBackendApi\Processor\Deleter\TaskDeleterInterface;
use Pyz\Glue\TaskBackendApi\Processor\Reader\TaskReader;
use Pyz\Glue\TaskBackendApi\Processor\Reader\TaskReaderInterface;
use Pyz\Glue\TaskBackendApi\Processor\ResponseBuilder\ErrorResponseBuilder;
use Pyz\Glue\TaskBackendApi\Processor\ResponseBuilder\ErrorResponseBuilderInterface;
use Pyz\Glue\TaskBackendApi\Processor\ResponseBuilder\TaskResponseBuilder;
use Pyz\Glue\TaskBackendApi\Processor\ResponseBuilder\TaskResponseBuilderInterface;
use Pyz\Glue\TaskBackendApi\Processor\Writer\TaskWriter;
use Pyz\Glue\TaskBackendApi\Processor\Writer\TaskWriterInterface;
use Spryker\Glue\Kernel\Backend\AbstractFactory;

/**
 * @method \Pyz\Glue\TaskBackendApi\TaskBackendApiFactory getConfig()
 */
class TaskBackendApiFactory extends AbstractFactory
{
 /**
  * @return \Pyz\Glue\TaskBackendApi\Processor\Reader\TaskReaderInterface
  */
    public function createTaskReader(): TaskReaderInterface
    {
        return new TaskReader(
            $this->getTaskFacade(),
            $this->createTaskResponseBuilder(),
            $this->createErrorResponseBuilder(),
        );
    }

    /**
     * @return \Pyz\Glue\TaskBackendApi\Processor\Writer\TaskWriterInterface
     */
    public function createTaskWriter(): TaskWriterInterface
    {
        return new TaskWriter(
            $this->getTaskFacade(),
            $this->createTaskResponseBuilder(),
            $this->createErrorResponseBuilder(),
            $this->createTaskMapper(),
        );
    }

    /**
     * @return \Pyz\Glue\TaskBackendApi\Processor\Deleter\TaskDeleterInterface
     */
    public function createTaskDeleter(): TaskDeleterInterface
    {
        return new TaskDeleter(
            $this->getTaskFacade(),
            $this->createTaskResponseBuilder(),
        );
    }

    /**
     * @return \Pyz\Glue\TaskBackendApi\Dependency\Facade\TaskBackendApiToTaskFacadeInterface
     */
    public function getTaskFacade(): TaskBackendApiToTaskFacadeInterface
    {
        return $this->getProvidedDependency(TaskBackendApiDependencyProvider::FACADE_TASK);
    }

    /**
     * @return \Pyz\Glue\TaskBackendApi\Dependency\Service\TaskBackendApiToUtilEncodingServiceInterface
     */
    public function getServiceUtilEncoding(): TaskBackendApiToUtilEncodingServiceInterface
    {
        return $this->getProvidedDependency(TaskBackendApiDependencyProvider::SERVICE_UTIL_ENCODING);
    }

    /**
     * @return \Pyz\Glue\TaskBackendApi\Processor\ResponseBuilder\TaskResponseBuilderInterface
     */
    public function createTaskResponseBuilder(): TaskResponseBuilderInterface
    {
        return new TaskResponseBuilder($this->createTaskMapper());
    }

    /**
     * @return \Pyz\Glue\TaskBackendApi\Processor\ResponseBuilder\ErrorResponseBuilderInterface
     */
    public function createErrorResponseBuilder(): ErrorResponseBuilderInterface
    {
        return new ErrorResponseBuilder();
    }

    /**
     * @return \Pyz\Glue\TaskBackendApi\Mapper\TaskMapperInterface
     */
    public function createTaskMapper(): TaskMapperInterface
    {
        return new TaskMapper($this->getServiceUtilEncoding());
    }
}
