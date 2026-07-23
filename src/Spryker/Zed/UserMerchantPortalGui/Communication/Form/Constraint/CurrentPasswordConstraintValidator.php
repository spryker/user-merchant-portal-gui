<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\UserMerchantPortalGui\Communication\Form\Constraint;

use Generated\Shared\Transfer\UserConditionsTransfer;
use Generated\Shared\Transfer\UserCriteriaTransfer;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CurrentPasswordConstraintValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     * @param \Symfony\Component\Validator\Constraint $constraint
     *
     * @throws \Symfony\Component\Validator\Exception\UnexpectedTypeException
     *
     * @return void
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof CurrentPasswordConstraint) {
            throw new UnexpectedTypeException($constraint, CurrentPasswordConstraint::class);
        }

        if (!$this->isValidPassword($value, $constraint)) {
            $this->context
                ->buildViolation($constraint->getMessage())
                ->addViolation();
        }
    }

    protected function isValidPassword(?string $password, CurrentPasswordConstraint $constraint): bool
    {
        if (!$password) {
            return false;
        }

        $merchantUserFacade = $constraint->getMerchantUserFacade();
        $idUser = $merchantUserFacade->getCurrentMerchantUser()->getUserOrFail()->getIdUserOrFail();

        $userCriteriaTransfer = (new UserCriteriaTransfer())
            ->setUserConditions((new UserConditionsTransfer())->addIdUser($idUser));

        $userTransfer = $merchantUserFacade->getUserCollection($userCriteriaTransfer)->getUsers()->getIterator()->current();

        return $merchantUserFacade->isValidPassword($password, $userTransfer->getPasswordOrFail());
    }
}
