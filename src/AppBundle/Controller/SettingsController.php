<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Architecture\ContainerServices;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Form\SettingsType;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Architecture\RepositoryServices;

/**
 *
 * @author laniger
 *         @Route("/settings")
 */
class SettingsController extends Controller
{
    use ContainerServices, RepositoryServices;

    /**
     * @Route("/", name="settings_index")
     * @Template()
     */
    public function settingsAction()
    {
        $form = $this->createUserForm();

        return [
            'form' => $form->createView()
        ];
    }

    private function createUserForm()
    {
        $form = $this->createForm(new SettingsType(), $this->getUser(), [
            'action' => $this->generateUrl('settings_user_save')
        ]);
        return $form;
    }

    /**
     * @Route("/usersave", name="settings_user_save")
     * @Template("AppBundle:Settings:settings.html.twig")
     *
     * @param Request $req
     */
    public function userSettingsSaveAction(Request $req)
    {
        $form = $this->createUserForm();
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user = $this->getUser();

            $user->setEmail($data->getEmail());
            if($form['newpassword']->getData()) {
                $newpw = $form['newpassword']->getData();
                $hash = $this->getPasswordEncoderFactory()->getEncoder($user)->encodePassword($newpw, null);
                $user->setPassword($hash);
            }

            $this->getUserRepository()->persistUser($user);

            return $this->redirect($this->generateUrl('settings_index'));
        }

        return [
            'form' => $form->createView()
        ];
    }
}