<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Architecture\ContainerServices;
use AppBundle\Entity\User;
use Symfony\Component\BrowserKit\Response;

/**
 * provides routes and functions for security routines
 *
 * @author laniger
 */
class SecurityController extends Controller
{
    use ContainerServices;

    /**
     * @Route("/login", name="login")
     * @Template
     */
    public function loginAction()
    {
        $this->getNeo4jClient();

        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return [
            'last_username' => $lastUsername,
            'error' => $error
        ];
    }

    /**
     * @Route("/login_check", name="login_check")
     */
    public function loginCheckAction()
    {}

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {}
}