<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Task\Dependency\Service;

class TaskToUtilValidateServiceBridge implements TaskToUtilValidateServiceInterface
{
    /**
     * @var \Spryker\Service\UtilValidate\UtilValidateServiceInterface
     */
    protected $utilValidateService;

    /**
     * @param \Spryker\Service\UtilValidate\UtilValidateServiceInterface $utilValidateService
     */
    public function __construct($utilValidateService)
    {
        $this->utilValidateService = $utilValidateService;
    }

    /**
     * @param string $email
     *
     * @return bool
     */
    public function isEmailFormatValid(string $email): bool
    {
        return $this->utilValidateService->isEmailFormatValid($email);
    }
}
