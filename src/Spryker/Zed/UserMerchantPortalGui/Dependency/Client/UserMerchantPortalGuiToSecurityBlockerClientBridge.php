<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\UserMerchantPortalGui\Dependency\Client;

use Generated\Shared\Transfer\SecurityCheckAuthContextTransfer;
use Generated\Shared\Transfer\SecurityCheckAuthResponseTransfer;

class UserMerchantPortalGuiToSecurityBlockerClientBridge implements UserMerchantPortalGuiToSecurityBlockerClientInterface
{
    /**
     * @var \Spryker\Client\SecurityBlocker\SecurityBlockerClientInterface
     */
    protected $securityBlockerClient;

    /**
     * @param \Spryker\Client\SecurityBlocker\SecurityBlockerClientInterface $securityBlockerClient
     */
    public function __construct($securityBlockerClient)
    {
        $this->securityBlockerClient = $securityBlockerClient;
    }

    /**
     * @param \Generated\Shared\Transfer\SecurityCheckAuthContextTransfer $securityCheckAuthContextTransfer
     *
     * @return \Generated\Shared\Transfer\SecurityCheckAuthResponseTransfer
     */
    public function incrementLoginAttemptCount(SecurityCheckAuthContextTransfer $securityCheckAuthContextTransfer): SecurityCheckAuthResponseTransfer
    {
        return $this->securityBlockerClient->incrementLoginAttemptCount($securityCheckAuthContextTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\SecurityCheckAuthContextTransfer $securityCheckAuthContextTransfer
     *
     * @return \Generated\Shared\Transfer\SecurityCheckAuthResponseTransfer
     */
    public function isAccountBlocked(SecurityCheckAuthContextTransfer $securityCheckAuthContextTransfer): SecurityCheckAuthResponseTransfer
    {
        return $this->securityBlockerClient->isAccountBlocked($securityCheckAuthContextTransfer);
    }
}
