<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Architecture\ContainerServices;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Form\SettingsType;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @author laniger
 *         @Route("/settings")
 */
class SettingsController extends Controller
{
    use ContainerServices;

    /**
     * @Route("/", name="settings_index")
     * @Template()
     */
    public function settingsAction(Request $req)
    {
        $form = $this->createForm(new SettingsType(), [
            'email' => $this->getUser()->getEmail()
        ]);
        $form->handleRequest($req);
        return [
            'form' => $form->createView()
        ];
    }
}