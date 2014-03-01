<?php
/**
 * KSENIA PORTFOLIO
 * @copyright 2014 mparaiso <mparaiso@online.fr>
 * all rights reserved
 * this code was open sourced for educational purpose only.
 */
namespace Controller;

use App;
use Doctrine\Common\Collections\Criteria;
use Doctrine\MongoDB\GridFSFile;
use Entity\Image;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Administration implements ControllerProviderInterface
{
    function index(App $app)
    {
        return $app->twig->render('admin_index');
    }

    /**
     * Upload a file, send json response
     * @param App $app
     * @param Request $req
     */
    function upload(App $app, Request $req)
    {
        if ($req->isXmlHttpRequest()) {

        }
        return $app->json(array('status' => 200, "message" => "uploaded"));
    }

    function projectIndex(App $app, Request $req)
    {
        $projects = $app->projectService->findAll();
        return $app->twig->render('project_index', array('projects' => $projects));
    }

    /**
     * @route /private/project/new
     * @param App $app
     * @param Request $req
     * @return string|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    function projectNew(App $app, Request $req)
    {
        $project = new \Entity\Project();
        $form = $app->formFactory->create(new \Form\Project(), $project);
        if ('POST' === $req->getMethod()) {
            if ($form->handleRequest($req)->isValid()) {
                $app->projectService->insert($project);
                return $app->redirect($app->url_generator->generate('project_read', array('id' => $project->getId())));
            }
        }
        return $app->twig->render('project_new', array('form' => $form->createView()));
    }

    function projectUpdate(App $app, Request $req, $id)
    {
        $project = $app->projectService->find($id);
        $form = $app->formFactory->create(new \Form\Project(), $project);
        if (!$project) {
            return $app->abort(404, 'project not found');
        }
        if ('POST' === $req->getMethod()) {
            if ($form->handleRequest($req)->isValid()) {
                $app->projectService->update($project);
                return $app->redirect($app->url_generator->generate('project_read', array('id' => $project->getId())));
            }
        }
        return $app->twig->render('project_update', array('project' => $project, 'form' => $form->createView()));
    }

    function projectRead(App $app, Request $req, $id)
    {
        $project = $app->projectService->find($id);
        return $app->twig->render('project_read', array('project' => $project));
    }

    function projectDelete(App $app, Request $req, $id)
    {
        $project = $app->projectService->find($id);
        if (!$project) {
            $app->abort(404);
        }
        $app->projectService->delete($project);
        return $app->redirect($app->url_generator->generate('project_index'));
    }

    function projectClone(App $app, Request $req, $id)
    {
        $project = $app->projectService->find($id);
        if (!$project) {
            $app->abort(404);
        }
        /** @var \Entity\Project $newProject */
        $newProject = $project->copy();
        $newProject->setTitle(uniqid($newProject->getTitle() . "_"));
        $app->projectService->insert($newProject);
        return $app->redirect($app->url_generator->generate('project_read', array('id' => $newProject->getId())));
    }


    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app)
    {
        /**
         * PROJECT MANAGEMENT
         */
        /* @var \Silex\ControllerCollection $projectController */
        $projectController = $app['controllers_factory'];
        $projectController->get('/', array($this, 'projectIndex'))
            ->bind('project_index');
        $projectController->match('/new', array($this, 'projectNew'))
            ->bind('project_new');
        $projectController->get('/{id}', array($this, 'projectRead'))
            ->bind('project_read');
        $projectController->match('/{id}/update', array($this, 'projectUpdate'))
            ->bind('project_update');
        $projectController->delete('/{id}', array($this, 'projectDelete'))
            ->bind('project_delete');
        $projectController->post('/{id}/clone', array($this, 'projectClone'))
            ->bind('project_clone');
        $imageController = new \Controller\Image();
        $projectController->mount('/{projectId}/image', $imageController->connect($app));

        /**
         * ADMINISTRATION
         */
        /* @var \Silex\ControllerCollection $adminController */
        $adminController = $app['controllers_factory'];
        $adminController->get('/', array($this, 'index'))
            ->bind('admin_index');
        $adminController->post('/upload', array($this, 'upload'));
        $adminController->mount('/project', $projectController);
        $page = new Page();
        $adminController->mount('/page', $page->connect($app));

        return $adminController;
    }
}