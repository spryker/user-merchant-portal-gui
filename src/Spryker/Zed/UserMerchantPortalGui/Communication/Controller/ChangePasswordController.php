<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\UserMerchantPortalGui\Communication\Controller;

use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Spryker\Zed\UserMerchantPortalGui\Communication\Form\ChangePasswordForm;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Spryker\Zed\UserMerchantPortalGui\Communication\UserMerchantPortalGuiCommunicationFactory getFactory()
 */
class ChangePasswordController extends AbstractController
{
    /**
     * @var string
     */
    protected const RESPONSE_NOTIFICATION_MESSAGE_SUCCESS = 'Success! The Password is updated.';

    /**
     * @var string
     */
    protected const RESPONSE_NOTIFICATION_MESSAGE_ERROR = 'Please resolve all errors.';

    /**
     * @var string
     */
    protected const ERROR_ACCESS_DENIED_CAUSE = 'access_denied';

    protected const string RESPONSE_NOTIFICATION_MESSAGE_PASSWORD_CHANGE_LIMIT_EXCEEDED = 'You reached the limit of password change attempts. Please try again later.';

    public function indexAction(Request $request): JsonResponse
    {
        $changePasswordForm = $this->getFactory()->createChangePasswordForm();

        if ($this->isFormSubmitRequest($changePasswordForm, $request) && !$this->reservePasswordChangeAttempt()) {
            return $this->createNotificationResponse(
                $this->createResponseData($changePasswordForm),
                static::RESPONSE_NOTIFICATION_MESSAGE_PASSWORD_CHANGE_LIMIT_EXCEEDED,
                false,
            );
        }

        $changePasswordForm->handleRequest($request);
        $responseData = $this->createResponseData($changePasswordForm);

        if (!$changePasswordForm->isSubmitted()) {
            return new JsonResponse($responseData);
        }

        if ($changePasswordForm->isValid()) {
            $formData = $changePasswordForm->getData();

            $this->getFactory()
                ->createMerchantUserUpdater()
                ->updateCurrentMerchantUserPassword(
                    $formData[ChangePasswordForm::FIELD_NEW_PASSWORD],
                );

            return $this->createNotificationResponse($responseData, static::RESPONSE_NOTIFICATION_MESSAGE_SUCCESS, true);
        }

        $errorMessage = static::RESPONSE_NOTIFICATION_MESSAGE_ERROR;

        foreach ($changePasswordForm->getErrors(true) as $error) {
            /** @var \Symfony\Component\Form\FormError $error */
            if ($error->getCause() !== static::ERROR_ACCESS_DENIED_CAUSE) {
                continue;
            }

            $errorMessage = $error->getMessage();
        }

        return $this->createNotificationResponse($responseData, $errorMessage, false);
    }

    protected function isFormSubmitRequest(FormInterface $form, Request $request): bool
    {
        return $request->isMethod($form->getConfig()->getMethod()) && $request->request->has($form->getName());
    }

    /**
     * @return array<string, mixed>
     */
    protected function createResponseData(FormInterface $changePasswordForm): array
    {
        return [
            'form' => $this->renderView(
                '@UserMerchantPortalGui/Partials/change-password-form-overlay.twig',
                [
                    'changePasswordForm' => $changePasswordForm->createView(),
                ],
            )->getContent(),
        ];
    }

    protected function reservePasswordChangeAttempt(): bool
    {
        $idUser = $this->getFactory()
            ->getMerchantUserFacade()
            ->getCurrentMerchantUser()
            ->getUserOrFail()
            ->getIdUserOrFail();

        return $this->getFactory()
            ->createPasswordChangeLimiter()
            ->reserveAttempt($idUser);
    }

    /**
     * @param array<string, mixed> $responseData
     */
    protected function createNotificationResponse(array $responseData, string $message, bool $isSuccess): JsonResponse
    {
        $zedUiFormResponseBuilder = $this->getFactory()
            ->getZedUiFactory()
            ->createZedUiFormResponseBuilder();

        $translatedMessage = $this->getFactory()
            ->getTranslatorFacade()
            ->trans($message);

        if (!$isSuccess) {
            $zedUiFormResponseBuilder->addErrorNotification($translatedMessage);
        } else {
            $zedUiFormResponseBuilder
                ->addSuccessNotification($translatedMessage)
                ->addActionCloseDrawer();
        }

        return new JsonResponse(array_merge($responseData, $zedUiFormResponseBuilder->createResponse()->toArray()));
    }
}
