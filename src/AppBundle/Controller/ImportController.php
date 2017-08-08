<?php

namespace AppBundle\Controller;


use AppBundle\Entity\FileImport;
use AppBundle\Entity\Schema;
use AppBundle\Form\ImportType;
use AppBundle\Service\CsvImporter;
use AppBundle\Service\CsvParser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("import")
 */
class ImportController extends Controller
{

    /**
     * @Route("/", name="import_index")
     * @Template
     *
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm(ImportType::class);

        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $import = $this->persistFileImport($form);

            return $this->redirectToRoute('import_content', [
                'id' => $import->getId()
            ]);
        }

        return [
            'form' => $form->createView()
        ];
    }

    /**
     * @Route("/{id}", name="import_content")
     * @Template
     *
     * @param Request $request
     * @param CsvParser $parser
     * @param CsvImporter $importer
     * @param FileImport $import
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function contentAction(Request $request, CsvParser $parser, CsvImporter $importer, FileImport $import)
    {
        $content = $parser->parseFile($import->getPath());
        $attributes = $this->getDoctrine()
            ->getRepository('AppBundle:Attribute')
            ->findFromSchema($import->getSchema());

        $post = $request->request;
        if ($post->count()) {
            $importer->import($post, $import, $content);

            unlink($import->getPath());

            $em = $this->getDoctrine()->getManager();
            $em->remove($import);
            $em->flush();

            return $this->redirectToRoute('instance_index', [
                'id' => $import->getSchema()->getId()
            ]);
        }

        return [
            'import' => $import,
            'content' => $content,
            'schema' => $import->getSchema(),
            'attributes' => $attributes
        ];
    }

    /**
     * @param $form
     * @return FileImport
     */
    private function persistFileImport(FormInterface $form): FileImport
    {
        $data = $form->getData();
        /** @var UploadedFile $file */
        $file = $data['file'];
        /** @var Schema $schema */
        $schema = $data['schema'];

        $moved = $file->move('../var/upload', sha1($file->getPathname()));

        $import = new FileImport();
        $import
            ->setPath($moved->getPathname())
            ->setSchema($schema)
            ->setUploadedBy($this->getUser());
        $em = $this->getDoctrine()->getManager();
        $em->persist($import);
        $em->flush();
        return $import;
    }

    /**
     * @Route("/{id}/delete", name="import_delete")
     *
     * @param FileImport $import
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(FileImport $import)
    {
        unlink($import->getPath());

        $em = $this->getDoctrine()->getManager();
        $em->remove($import);
        $em->flush();

        return $this->redirectToRoute('import_index');
    }

}
