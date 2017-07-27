<?php

namespace AppBundle\Service;


use AppBundle\Entity\FileImport;
use AppBundle\Entity\Instance;
use AppBundle\Entity\Property;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CsvImporter
{

    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var TokenStorageInterface
     */
    private $tsi;

    /**
     * @var \AppBundle\Repository\AttributeRepository|\Doctrine\ORM\EntityRepository
     */
    private $attrRepo;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tsi)
    {
        $this->em = $em;
        $this->tsi = $tsi;
        $this->attrRepo = $em->getRepository('AppBundle:Attribute');
    }

    /**
     * @param ParameterBag $bag
     * @param FileImport $import
     * @param array $lines
     */
    public function import(ParameterBag $bag, FileImport $import, array $lines)
    {
        $userows = $bag->get('use');
        $useixs = array_keys($userows);

        $uselines = array_filter($lines, function ($ix) use ($useixs) {
            return in_array($ix, $useixs);
        }, ARRAY_FILTER_USE_KEY);

        $selectedAttrs = $bag->get('attr');

        foreach ($uselines as $line) {
            $instance = new Instance();
            $instance
                ->setSchema($import->getSchema())
                ->setCreatedBy($this->tsi->getToken()->getUser());
            $this->createProperties($selectedAttrs, $line, $instance);

            $this->em->persist($instance);
        }

        $this->em->flush();
    }

    /**
     * @param array $selectedAttrs
     * @param array $line
     * @param Instance $instance
     */
    public function createProperties(array $selectedAttrs, array $line, Instance $instance)
    {
        foreach ($selectedAttrs as $attrIx => $attrValue) {
            $linevalue = $line[$attrIx];

            $prop = new Property();
            $prop->setInstance($instance);

            if ($attrValue === '_name') {
                $instance->setName($linevalue);
            } else {
                $attribute = $this->attrRepo->find($attrValue);
                $prop
                    ->setAttribute($attribute)
                    ->setValue($linevalue);
            }

            $this->em->persist($prop);
        }
    }

}
