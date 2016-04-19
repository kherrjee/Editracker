<?php



/*

 * This file is part of the FOSUserBundle package.

 *

 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>

 *

 * For the full copyright and license information, please view the LICENSE

 * file that was distributed with this source code.

 */



namespace Matrix\MatrixUserBundle\Controller;


use Matrix\MatrixBundle\Entity\Document;

use FOS\UserBundle\FOSUserEvents;

use FOS\UserBundle\Event\FormEvent;

use FOS\UserBundle\Event\FilterUserResponseEvent;

use FOS\UserBundle\Event\GetResponseUserEvent;

use FOS\UserBundle\Model\UserInterface;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;



/**
 * Controller managing the user profile
 *
 * @author Christophe Coevoet <stof@notk.org>
 */

class ProfileController extends Controller

{

    public function showAction()

    {
        $max_redords = 10;
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUsers();

        // $count = $user->count();
        // $pagination = array(
        //     'page'                   => $page,
        //     'route'                  => 'matrix_edi_viewAllUser',
        //     'route_params'           => array()
        //     );


        // if($max_records > 0){
        //     $pagination['pages_count'] = max(ceil($count/$max_records), 1);
        // }

        return $this->render('MatrixEdiBundle:Matrix:viewAllUsers.html.twig', array('user' => $user));
    }

     public function showUserAction($id)
    {
        
        $user = $this->getDoctrine()
            ->getManager()
            ->getRepository('MatrixUserBundle:User')
            ->find($id);

        if (!is_object($user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        return $this->render('FOSUserBundle:Profile:show_content.html.twig', array('user' => $user));
    }

  

    
    /**
     * Edit the user
     */

    public function editAction(Request $request)

    {

        $user = $this->getUser();

        if (!is_object($user) || !$user instanceof UserInterface) {

            throw new AccessDeniedException('This user does not have access to this section.');

        }

        
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */

        $dispatcher = $this->get('event_dispatcher');



        $event = new GetResponseUserEvent($user, $request);

        // $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);



        if (null !== $event->getResponse()) {

            return $event->getResponse();

        }



        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */

        $formFactory = $this->get('fos_user.profile.form.factory');



        $form = $formFactory->createForm();

        $form->setData($user);



        $form->handleRequest($request);



        if ($form->isValid()) {

            /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */

            $userManager = $this->get('fos_user.user_manager');



            $event = new FormEvent($form, $request);

            //$dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);



            $userManager->updateUser($user);
            $fullname = $user->getFullname();


            if (null === $response = $event->getResponse()) {

                //$url = $this->generateUrl('fos_user_profile_show');
                $session = $this->getRequest()->getSession();
                $session->getFlashBag()->add('message', $fullname. ' is successfully updated');
                $url = $this->generateUrl('matrix_edi_viewUser', array('id' => $user->getId()));
                $response = new RedirectResponse($url);

            }



            // $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));



            return $response;

        }



        return $this->render('FOSUserBundle:Profile:edit_content.html.twig', array(

            'form' => $form->createView()

        ));

    }



    public function editUserAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('MatrixUserBundle:User')->find($id);

        if (!is_object($user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.profile.form.factory');

        $form = $formFactory->createForm(); 
        $form->setData($user); 
        $form->handleRequest($request); 

        if ($form->isValid()) { 
            /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */

            $userManager = $this->get('fos_user.user_manager');
            $user->setLastLogin(new \DateTime());
            $userManager->updateUser($user); 
            $fullname = $user->getFullname();

            $session = $this->getRequest()->getSession();
            $session->getFlashBag()->add('message', $fullname. ' is successfully updated'); 

            $url = $this->generateUrl('matrix_edi_viewUser', array('id' => $user->getId())); 
            $response =  new RedirectResponse($url);
        }

        return $this->render('FOSUserBundle:Profile:edit_content.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function updateUserAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('MatrixUserBundle:User')->find($id);

        if (!is_object($user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.profile.form.factory');

        $form = $formFactory->createForm(); 
        $form->setData($user); 
        $form->handleRequest($request); 

        $userManager = $this->get('fos_user.user_manager');
        $users = $userManager->findUsers();
        
        if ($form->isValid()) { 
            /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */


            $user->setLastLogin(new \DateTime());
            $userManager->updateUser($user);
            $fullname = $user->getFullname();

            $session = $this->getRequest()->getSession();
            $session->getFlashBag()->add('message', $fullname. ' is successfully updated'); 

            $url = $this->generateUrl('matrix_edi_viewAllUser'); 
            return new RedirectResponse($url);

        }

        return $this->render('FOSUserBundle:Profile:update.html.twig', array(
            'user' => $user,
            'form' => $form->createView()
        ));
    }


     public function deleteUserAction($id){
        
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('MatrixUserBundle:User')->find($id);
        
        if ($user ==  null) {
            throw new NotFoundHttpException('User not found for user ' . $user);
        } 
            $userManager = $this->get('fos_user.user_manager');
            $userManager->deleteUser($user);
            $fullname = $user->getFullname();

            $session = $this->getRequest()->getSession();
            $session->getFlashBag()->add('message', $fullname . ' is successfully deleted');

            $url = $this->generateUrl('matrix_edi_viewAllUser'); 
            return new RedirectResponse($url);

    }

    public function uploadAction(Request $request)
    {
        $document = new Document();
        $form = $this->createFormBuilder($document)
        ->add('name')
        ->add('file')
        ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($document);
            $em->flush();
            $url = $this->generateUrl('matrix_edi_viewUser');

            return $this->redirectToRoute($url);
        }

    return array('form' => $form->createView());
    }

   
}

