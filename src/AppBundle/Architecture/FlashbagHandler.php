<?php

namespace AppBundle\Architecture;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Session;

class FlashbagHandler
{

    /**
     * @var FlashBag
     */
    private $flashbag;

    /**
     * @var Translator
     */
    private $trans;

    public function __construct(Session $session, Translator $trans)
    {
        $this->flashbag = $session->getFlashBag();
        $this->trans = $trans;
    }

    public function addSuccess($msg)
    {
        $this->flashbag->add('success', $msg);

        return $this;
    }

    public function addInfo($msg)
    {
        $this->flashbag->add('info', $msg);

        return $this;
    }

    public function addWarning($msg)
    {
        $this->flashbag->add('warning', $msg);

        return $this;
    }

    public function addDanger($msg)
    {
        $this->flashbag->add('danger', $msg);

        return $this;
    }

    public function addSaveSuccess()
    {
        $this->addSuccess($this->trans->trans('alert.success.save'));

        return $this;
    }


    public function addDeleteSuccess()
    {
        $this->addSuccess($this->trans->trans('alert.success.delete'));

        return $this;
    }

}
