<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\UserMerchantPortalGui\Communication\Form\Constraint;

use Spryker\Zed\UserMerchantPortalGui\Dependency\Facade\UserMerchantPortalGuiToMerchantUserFacadeInterface;
use Symfony\Component\Validator\Constraint;

class UniqueUserEmailConstraint extends Constraint
{
    /**
     * @var string
     */
    public const ID_USER = 'idUser';

    /**
     * @var string
     */
    public const OPTION_MERCHANT_USER_FACADE = 'merchantUserFacade';

    /**
     * @var string
     */
    public const GROUP_UNIQUE_USERNAME_CHECK = 'unique_email_check';

    /**
     * @var array<string>
     */
    public $groups = [self::GROUP_UNIQUE_USERNAME_CHECK];

    /**
     * @var \Spryker\Zed\UserMerchantPortalGui\Dependency\Facade\UserMerchantPortalGuiToMerchantUserFacadeInterface
     */
    protected UserMerchantPortalGuiToMerchantUserFacadeInterface $merchantUserFacade;

    /**
     * @var int|null
     */
    protected ?int $idUser = null;

    public function getMerchantUserFacade(): UserMerchantPortalGuiToMerchantUserFacadeInterface
    {
        return $this->merchantUserFacade;
    }

    public function getMessage(): string
    {
        return 'A user with this email already exists.';
    }

    public function getIdUser(): ?int
    {
        return $this->idUser;
    }
}
