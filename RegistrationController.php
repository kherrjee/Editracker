<?php



namespace Matrix\MatrixUserBundle\Controller;



use FOS\UserBundle\FOSUserEvents;

use FOS\UserBundle\Event\FormEvent;

use FOS\UserBundle\Event\GetResponseUserEvent;

use FOS\UserBundle\Event\FilterUserResponseEvent;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use FOS\UserBundle\Model\UserInterface;



class RegistrationController extends Controller

{

    public function registerAction(Request $request)

    {

        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */

        $formFactory = $this->get('fos_user.registration.form.factory');

        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */

        $userManager = $this->get('fos_user.user_manager');

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */

        $dispatcher = $this->get('event_dispatcher');



        $user = $userManager->createUser();

        $user->setEnabled(true);



        $event = new GetResponseUserEvent($user, $request);

       // $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);



        if (null !== $event->getResponse()) {

            return $event->getResponse();

        }



        $form = $formFactory->createForm();

        $form->setData($user);



        $form->handleRequest($request);



        if ($form->isValid()) {

            // $event = new FormEvent($form, $request);

            // $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);



            $userManager->updateUser($user);

            $fullname = $user->getFullname();

            if (null === $response = $event->getResponse()) {   
                $session = $this->getRequest()->getSession();
                $session->getFlashBag()->add('message', $fullname.   ' is sucessfully added');
                $url = $this->generateUrl('matrix_edi_viewAllUser');

                $response = new RedirectResponse($url);

            }



            //$dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));



            return $response;

        }



        return $this->render('MatrixUserBundle::addUser.html.twig', array(

            'form' => $form->createView(),

        ));

    }

}

?>