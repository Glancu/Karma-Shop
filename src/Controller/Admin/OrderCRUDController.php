<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Component\OrderStatus;
use App\Entity\EmailTemplate;
use App\Entity\Order;
use App\Service\MailerService;
use App\Service\OrderService;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderCRUDController extends CRUDController
{
    private OrderService $orderService;
    private MailerService $mailerService;

    public function __construct(OrderService $orderService, MailerService $mailerService)
    {
        $this->orderService = $orderService;
        $this->mailerService = $mailerService;
    }

    /**
     * @param $id
     *
     * @return RedirectResponse
     *
     * @throws NonUniqueResultException
     */
    public function updateStatusAction($id): RedirectResponse
    {
        $status = $this->getRequest()->query->get('status');

        /**
         * @var Order $order
         */
        $order = $this->admin->getSubject();

        if (!$order) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id: %s', $id));
        }

        return $this->sendMailByStatusAndReturnResponse($status, $order);
    }

    /**
     * @param string $status
     * @param Order $order
     *
     * @return RedirectResponse
     *
     * @throws NonUniqueResultException
     * @throws Exception
     */
    private function sendMailByStatusAndReturnResponse(string $status, Order $order): RedirectResponse
    {
        $em = $this->getDoctrine();
        $orderService = $this->orderService;

        $emailTemplate = null;
        $emailTemplate2 = null;

        switch ($status) {
            case 'paid':
                $emailTemplate = $em->getRepository('App:EmailTemplate')
                                    ->findByType(EmailTemplate::TYPE_ORDER_PAID_TO_USER);

                $emailTemplate2 = $em->getRepository('App:EmailTemplate')
                                     ->findByType(EmailTemplate::TYPE_ORDER_PAID_TO_ADMIN);

                $order->setStatus(OrderStatus::STATUS_PAID);
                break;
            case 'not-paid':
                $emailTemplate = $em->getRepository('App:EmailTemplate')
                                    ->findByType(EmailTemplate::TYPE_ORDER_NOT_PAID_TO_USER);

                $order->setStatus(OrderStatus::STATUS_NOT_PAID);
                $order->setTransaction(null);
                break;
            case 'sent-products':
                $emailTemplate = $em->getRepository('App:EmailTemplate')
                                    ->findByType(EmailTemplate::TYPE_ORDER_SENT_PRODUCTS_TO_USER);

                $order->setStatus(OrderStatus::STATUS_SENT_PRODUCTS);
                break;
            case 'in-progress':
                $emailTemplate = $em->getRepository('App:EmailTemplate')
                                    ->findByType(EmailTemplate::TYPE_ORDER_IN_PROGRESS_TO_USER);

                $order->setStatus(OrderStatus::STATUS_IN_PROGRESS);
                break;
            default:
                break;

        }

        if ($emailTemplate !== null) {
            $orderService->replaceVariableAndSendMail($order, $emailTemplate, $order->getUser()->getEmail());

            if ($emailTemplate2 !== null) {
                $orderService->replaceVariableAndSendMail($order, $emailTemplate2,
                    $this->mailerService->getAdminEmail());
            }

            $this->getDoctrine()->getManager()->persist($order);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('sonata_flash_success', 'Email was sent!');

            return new RedirectResponse($this->admin->generateUrl('list'));
        }

        $this->addFlash('sonata_flash_error', sprintf('Email template for %s status was not found', $status));

        return new RedirectResponse($this->admin->generateUrl('list'));
    }
}
