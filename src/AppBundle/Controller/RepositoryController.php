<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Repository;
use AppBundle\Service\SatisJsonBuilder;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\RepositoryType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Repository controller.
 *
 * @Route("repositories")
 */
class RepositoryController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Lists all repository entities.
     *
     * @throws \InvalidArgumentException
     * @throws \LogicException
     *
     * @Route("/", name="repository_index")
     * @Method("GET")
     */
    public function cgetAction()
    {
        $em = $this->getDoctrine()->getManager();

        $repositories = $em->getRepository('AppBundle:Repository')->findAll();
        $view = $this->view($repositories, 200)
            ->setTemplate('repository/index.html.twig')
            ->setTemplateVar('repositories');

        return $this->handleView($view);
    }

    /**
     * Finds and displays a repository entity.
     *
     * @param Repository $repository
     * @return Response
     * @throws \InvalidArgumentException
     *
     * @Route("/{id}", name="repository_show", requirements={"id": "[\-a-f0-9]{36}+"})
     * @Method("GET")
     */
    public function getAction(Repository $repository)
    {
        return $this->handleView($this->createShowView($repository, Response::HTTP_OK));
    }

    /**
     * Creates a new repository entity.
     *
     * @throws \InvalidArgumentException
     * @throws \LogicException
     *
     * @Route("/", name="repository_post")
     * @Method({"POST"})
     */
    public function postAction(Request $request)
    {
        $repository = new Repository();
        $form = $this->createForm(
            RepositoryType::class,
            $repository,
            ['action' => $this->generateUrl('repository_post')]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($repository);
            $em->flush();

            return $this->handleView(
                $this->createShowView(
                    $repository,
                    Response::HTTP_CREATED,
                    $this->generateUrl('repository_show', ['id' => $repository->getId()])
                )
            );
        }

        $view = $this->view($form, Response::HTTP_BAD_REQUEST)
            ->setTemplate('repository/new.html.twig')
            ->setTemplateVar('form');

        return $this->handleView($view);
    }

    /**
     * Creates a new repository entity.
     *
     * @Route("/new", name="repository_new")
     * @Method({"GET"})
     * @throws \InvalidArgumentException
     */
    public function newAction(Request $request)
    {
        $repository = new Repository();
        $form = $this->createForm(
            RepositoryType::class,
            $repository,
            ['action' => $this->generateUrl('repository_post')]
        );
        $form->handleRequest($request);

        $view = $this->view($repository, Response::HTTP_OK)
            ->setTemplate('repository/new.html.twig')
            ->setTemplateVar('data')
            ->setTemplateData(['form' => $form->createView()]);

        return $this->handleView($view);
    }

    /**
     * Displays a form to edit an existing repository entity.
     *
     * @param Request $request
     * @param Repository $repository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \LogicException
     * @throws \InvalidArgumentException
     *
     * @Route("/{id}", name="repository_put")
     * @Method({"PUT"})
     */
    public function putAction(Request $request, Repository $repository)
    {
        $editForm = $this->createForm(
            RepositoryType::class,
            $repository,
            ['action' => $this->generateUrl('repository_put', ['id' => $repository->getId()]), 'method' => 'PUT']
        );
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->handleView(
                $this->createShowView(
                    $repository,
                    Response::HTTP_ACCEPTED,
                    $this->generateUrl('repository_edit', ['id' => $repository->getId()])
                )
            );
        }

        $view = $this->view($editForm, Response::HTTP_BAD_REQUEST)
            ->setTemplate('repository/edit.html.twig')
            ->setTemplateVar('form')
            ->setTemplateData(['delete_form' => $this->createDeleteForm($repository)->createView()]);
        return $this->handleView($view);
    }

    /**
     * Displays a form to edit an existing repository entity.
     *
     * @param Request $request
     * @param Repository $repository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \InvalidArgumentException
     *
     * @Route("/{id}/edit", name="repository_edit")
     * @Method({"GET"})
     */
    public function editAction(Request $request, Repository $repository)
    {
        $deleteForm = $this->createDeleteForm($repository);
        $editForm = $this->createForm(
            RepositoryType::class,
            $repository,
            ['action' => $this->generateUrl('repository_put', ['id' => $repository->getId()]), 'method' => 'PUT']
        );
        $editForm->handleRequest($request);

        $view = $this->view($repository, Response::HTTP_OK)
            ->setTemplate('repository/edit.html.twig')
            ->setTemplateVar('repository')
            ->setTemplateData(['form' => $editForm->createView(), 'delete_form' => $deleteForm->createView()]);
        return $this->handleView($view);
    }

    /**
     * Deletes a repository entity.
     *
     * @param Request $request
     * @param Repository $repository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \LogicException
     * @throws \InvalidArgumentException
     *
     * @Route("/{id}", name="repository_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Repository $repository)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($repository);
        $em->flush();

        return $this->redirectToRoute('repository_index');
    }

    /**
     * Creates a form to delete a repository entity.
     *
     * @param Repository $repository The repository entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Repository $repository)
    {
        return $this->createFormBuilder()
            ->setMethod('DELETE')
            ->setAction($this->generateUrl('repository_delete', array('id' => $repository->getId())))
            ->getForm()
        ;
    }

    /**
     * @param Repository $repository
     * @param $responseCode
     * @param $htmlRedirect
     * @return \FOS\RestBundle\View\View
     * @throws \InvalidArgumentException
     */
    private function createShowView(Repository $repository, $responseCode, $htmlRedirect = null)
    {
        $view = $this->view($repository, $responseCode)
            ->setTemplate('repository/show.html.twig')
            ->setTemplateVar('repository')
            ->setTemplateData(
                [
                    'delete_form' => $this->createDeleteForm($repository),
                    'satis_json_builder' => $this->get(SatisJsonBuilder::class)
                ]
            );
        if ($htmlRedirect) {
            $view->setLocation($htmlRedirect);
        }
        return $view;
    }
}
