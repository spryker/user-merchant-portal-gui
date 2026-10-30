<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\UserMerchantPortalGui\Dependency\Client;

use Generated\Shared\Transfer\SecurityCheckAttemptContextTransfer;
use Generated\Shared\Transfer\SecurityCheckAttemptResponseTransfer;
use Generated\Shared\Transfer\SecurityCheckAuthContextTransfer;
use Generated\Shared\Transfer\SecurityCheckAuthResponseTransfer;

interface UserMerchantPortalGuiToSecurityBlockerClientInterface
{
    public function incrementLoginAttemptCount(SecurityCheckAuthContextTransfer $securityCheckAuthContextTransfer): SecurityCheckAuthResponseTransfer;

    public function isAccountBlocked(SecurityCheckAuthContextTransfer $securityCheckAuthContextTransfer): SecurityCheckAuthResponseTransfer;

    public function incrementAttemptCount(SecurityCheckAttemptContextTransfer $securityCheckAttemptContextTransfer): SecurityCheckAttemptResponseTransfer;
}
