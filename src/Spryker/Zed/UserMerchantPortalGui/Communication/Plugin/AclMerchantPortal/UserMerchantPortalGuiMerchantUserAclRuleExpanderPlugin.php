<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\UserMerchantPortalGui\Communication\Plugin\AclMerchantPortal;

use Generated\Shared\Transfer\RuleTransfer;
use Spryker\Zed\AclMerchantPortalExtension\Dependency\Plugin\MerchantUserAclRuleExpanderPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \Spryker\Zed\UserMerchantPortalGui\Communication\UserMerchantPortalGuiCommunicationFactory getFactory()
 * @method \Spryker\Zed\UserMerchantPortalGui\UserMerchantPortalGuiConfig getConfig()
 */
class UserMerchantPortalGuiMerchantUserAclRuleExpanderPlugin extends AbstractPlugin implements MerchantUserAclRuleExpanderPluginInterface
{
    /**
     * @uses {@link \Spryker\Shared\Acl\AclConstants::VALIDATOR_WILDCARD}
     *
     * @var string
     */
    protected const RULE_VALIDATOR_WILDCARD = '*';

    /**
     * @uses {@link \Spryker\Shared\Acl\AclConstants::ALLOW}
     *
     * @var string
     */
    protected const RULE_TYPE_ALLOW = 'allow';

    /**
     * {@inheritDoc}
     * - Adds `user-merchant-portal-gui` to list of `AclRules`.
     *
     * @api
     *
     * @param list<\Generated\Shared\Transfer\RuleTransfer> $ruleTransfers
     *
     * @return list<\Generated\Shared\Transfer\RuleTransfer>
     */
    public function expand(array $ruleTransfers): array
    {
        $ruleTransfers[] = (new RuleTransfer())
            ->setBundle('user-merchant-portal-gui')
            ->setController(static::RULE_VALIDATOR_WILDCARD)
            ->setAction(static::RULE_VALIDATOR_WILDCARD)
            ->setType(static::RULE_TYPE_ALLOW);

        return $ruleTransfers;
    }
}
