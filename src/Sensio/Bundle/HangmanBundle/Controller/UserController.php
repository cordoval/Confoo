<?php

namespace Sensio\Bundle\HangmanBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\HangmanBundle\Entity\User;
use Sensio\Bundle\HangmanBundle\Form\UserType;

class UserController extends Controller
{
    /**
     * @Route("/registration", name="registration")
     * @Template()
     */
    public function registrationAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(new UserType(), $user);

        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($user);
                $em->flush();

                return $this->redirect($this->generateUrl('registration_success'));
            }
        }

        return array('form' => $form->createView());
    }
}