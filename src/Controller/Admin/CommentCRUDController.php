<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Comment;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CommentCRUDController extends CRUDController
{
    /**
     * @param $id
     *
     * @return RedirectResponse
     */
    public function updateStatusAction($id): RedirectResponse
    {
        $status = $this->getRequest()->query->get('status');

        /**
         * @var Comment $comment
         */
        $comment = $this->admin->getSubject();

        if (!$comment) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id: %s', $id));
        }

        if ($status === 'accept') {
            $comment->setEnable(true);
        }

        if ($status === 'block') {
            $comment->setEnable(false);
        }

        $this->getDoctrine()->getManager()->persist($comment);
        $this->getDoctrine()->getManager()->flush();


        $this->addFlash('sonata_flash_success', 'Status was updated');

        return new RedirectResponse($this->admin->generateUrl('list'));
    }
}
