<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\UserMerchantPortalGui\Communication\Limiter;

use Generated\Shared\Transfer\SecurityCheckAttemptContextTransfer;
use Spryker\Zed\UserMerchantPortalGui\Dependency\Client\UserMerchantPortalGuiToSecurityBlockerClientInterface;
use Spryker\Zed\UserMerchantPortalGui\UserMerchantPortalGuiConfig;

class PasswordChangeLimiter implements PasswordChangeLimiterInterface
{
    protected const string SECURITY_CHECK_TYPE = 'merchant-portal-user:password_change';

    protected const string IDENTIFIER_PATTERN_USER = 'user:%d';

    public function __construct(
        protected UserMerchantPortalGuiToSecurityBlockerClientInterface $securityBlockerClient,
        protected UserMerchantPortalGuiConfig $userMerchantPortalGuiConfig
    ) {
    }

    public function reserveAttempt(int $idUser): bool
    {
        $limit = $this->userMerchantPortalGuiConfig->getPasswordChangeAttemptLimit();

        if ($limit <= 0) {
            return true;
        }

        $securityCheckAttemptContextTransfer = (new SecurityCheckAttemptContextTransfer())
            ->setType(static::SECURITY_CHECK_TYPE)
            ->setIdentifier(sprintf(static::IDENTIFIER_PATTERN_USER, $idUser))
            ->setTtl($this->userMerchantPortalGuiConfig->getPasswordChangeAttemptLimitTtl());

        return $this->securityBlockerClient
            ->incrementAttemptCount($securityCheckAttemptContextTransfer)
            ->getNumberOfAttemptsOrFail() <= $limit;
    }
}
