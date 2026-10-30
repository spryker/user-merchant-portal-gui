<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\UserMerchantPortalGui\Communication\Limiter;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\SecurityCheckAttemptContextTransfer;
use Generated\Shared\Transfer\SecurityCheckAttemptResponseTransfer;
use Spryker\Zed\UserMerchantPortalGui\Communication\Limiter\PasswordChangeLimiter;
use Spryker\Zed\UserMerchantPortalGui\Dependency\Client\UserMerchantPortalGuiToSecurityBlockerClientInterface;
use Spryker\Zed\UserMerchantPortalGui\UserMerchantPortalGuiConfig;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group UserMerchantPortalGui
 * @group Communication
 * @group Limiter
 * @group PasswordChangeLimiterTest
 * Add your own group annotations below this line
 */
class PasswordChangeLimiterTest extends Unit
{
    protected const int ID_USER = 7;

    protected const int OTHER_ID_USER = 8;

    protected const int TTL = 900;

    protected const string SECURITY_CHECK_TYPE = 'merchant-portal-user:password_change';

    protected const string IDENTIFIER_USER = 'user:7';

    public function testReserveAttemptReturnsFalseWhenAttemptLimitIsExceeded(): void
    {
        // Arrange
        $passwordChangeLimiter = $this->createPasswordChangeLimiter(2, $this->createCountingSecurityBlockerClient());

        // Act
        $areAttemptsReserved = [$passwordChangeLimiter->reserveAttempt(static::ID_USER), $passwordChangeLimiter->reserveAttempt(static::ID_USER), $passwordChangeLimiter->reserveAttempt(static::ID_USER)];

        // Assert
        $this->assertSame([true, true, false], $areAttemptsReserved);
    }

    public function testReserveAttemptReturnsTrueForOtherUserWhenAttemptLimitIsExceededForUser(): void
    {
        // Arrange
        $passwordChangeLimiter = $this->createPasswordChangeLimiter(1, $this->createCountingSecurityBlockerClient());
        $passwordChangeLimiter->reserveAttempt(static::ID_USER);
        $passwordChangeLimiter->reserveAttempt(static::ID_USER);

        // Act
        $isAttemptReserved = $passwordChangeLimiter->reserveAttempt(static::OTHER_ID_USER);

        // Assert
        $this->assertTrue($isAttemptReserved);
    }

    public function testReserveAttemptPassesTypeIdentifierAndTtlToSecurityBlockerClient(): void
    {
        // Arrange
        $securityBlockerClientMock = $this->createMock(UserMerchantPortalGuiToSecurityBlockerClientInterface::class);
        $passwordChangeLimiter = $this->createPasswordChangeLimiter(1, $securityBlockerClientMock);

        // Expect
        $securityBlockerClientMock->expects($this->once())
            ->method('incrementAttemptCount')
            ->with($this->callback(function (SecurityCheckAttemptContextTransfer $securityCheckAttemptContextTransfer): bool {
                return $securityCheckAttemptContextTransfer->getType() === static::SECURITY_CHECK_TYPE
                    && $securityCheckAttemptContextTransfer->getIdentifier() === static::IDENTIFIER_USER
                    && $securityCheckAttemptContextTransfer->getTtl() === static::TTL;
            }))
            ->willReturn((new SecurityCheckAttemptResponseTransfer())->setNumberOfAttempts(1));

        // Act
        $passwordChangeLimiter->reserveAttempt(static::ID_USER);
    }

    /**
     * @dataProvider getDisabledPasswordChangeAttemptLimitDataProvider
     */
    public function testReserveAttemptReturnsTrueAndDoesNotCountAttemptWhenAttemptLimitIsDisabled(int $passwordChangeAttemptLimit): void
    {
        // Arrange
        $securityBlockerClientMock = $this->createMock(UserMerchantPortalGuiToSecurityBlockerClientInterface::class);
        $securityBlockerClientMock->expects($this->never())->method('incrementAttemptCount');
        $passwordChangeLimiter = $this->createPasswordChangeLimiter($passwordChangeAttemptLimit, $securityBlockerClientMock);

        // Act
        $isAttemptReserved = $passwordChangeLimiter->reserveAttempt(static::ID_USER);

        // Assert
        $this->assertTrue($isAttemptReserved);
    }

    /**
     * @return array<string, array<int>>
     */
    public function getDisabledPasswordChangeAttemptLimitDataProvider(): array
    {
        return [
            'zero limit' => [0],
            'negative limit' => [-1],
        ];
    }

    protected function createPasswordChangeLimiter(
        int $passwordChangeAttemptLimit,
        UserMerchantPortalGuiToSecurityBlockerClientInterface $securityBlockerClient
    ): PasswordChangeLimiter {
        $userMerchantPortalGuiConfigMock = $this->createMock(UserMerchantPortalGuiConfig::class);
        $userMerchantPortalGuiConfigMock->method('getPasswordChangeAttemptLimit')->willReturn($passwordChangeAttemptLimit);
        $userMerchantPortalGuiConfigMock->method('getPasswordChangeAttemptLimitTtl')->willReturn(static::TTL);

        return new PasswordChangeLimiter($securityBlockerClient, $userMerchantPortalGuiConfigMock);
    }

    protected function createCountingSecurityBlockerClient(): UserMerchantPortalGuiToSecurityBlockerClientInterface
    {
        $numberOfAttemptsByIdentifier = [];
        $securityBlockerClientMock = $this->createMock(UserMerchantPortalGuiToSecurityBlockerClientInterface::class);
        $securityBlockerClientMock->method('incrementAttemptCount')->willReturnCallback(
            function (SecurityCheckAttemptContextTransfer $securityCheckAttemptContextTransfer) use (&$numberOfAttemptsByIdentifier): SecurityCheckAttemptResponseTransfer {
                $identifier = $securityCheckAttemptContextTransfer->getIdentifierOrFail();
                $numberOfAttemptsByIdentifier[$identifier] = ($numberOfAttemptsByIdentifier[$identifier] ?? 0) + 1;

                return (new SecurityCheckAttemptResponseTransfer())->setNumberOfAttempts($numberOfAttemptsByIdentifier[$identifier]);
            },
        );

        return $securityBlockerClientMock;
    }
}
