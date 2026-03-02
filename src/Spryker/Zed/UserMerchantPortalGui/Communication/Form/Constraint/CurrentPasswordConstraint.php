<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\UserMerchantPortalGui\Communication\Form\Constraint;

use Spryker\Zed\UserMerchantPortalGui\Dependency\Facade\UserMerchantPortalGuiToMerchantUserFacadeInterface;
use Symfony\Component\Validator\Constraint;

class CurrentPasswordConstraint extends Constraint
{
    /**
     * @var string
     */
    public const OPTION_MERCHANT_USER_FACADE = 'merchantUserFacade';

    /**
     * @var \Spryker\Zed\UserMerchantPortalGui\Dependency\Facade\UserMerchantPortalGuiToMerchantUserFacadeInterface
     */
    protected UserMerchantPortalGuiToMerchantUserFacadeInterface $merchantUserFacade;

    public function getMerchantUserFacade(): UserMerchantPortalGuiToMerchantUserFacadeInterface
    {
        return $this->merchantUserFacade;
    }

    public function getMessage(): string
    {
        return 'The value is incorrect. Please try again.';
    }
}
