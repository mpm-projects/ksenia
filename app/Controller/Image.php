<?php

namespace Controller;
use \App;
use Doctrine\MongoDB\GridFSFile;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Image implements ControllerProviderInterface
{
    function imageIndex(App $app, Request $req, $projectId)
    {
        /** @var \Entity\Project $project */
        $project = $app->projectService->find($projectId);
        !$project and  $app->abort(404);
        return $app->twig->render('image_index', array('project' => $project));
    }

    function imageRead(App $app, Request $req, $projectId, $imageId)
    {
        /** @var \Entity\Project $project */
        $project = $app->projectService->find($projectId);
        !$project and $app->abort(404);
        $image = $project->getImageById($imageId);
        return $app->twig->render('image_read', array('project' => $project, 'image' => $image));
    }

    function imageCreate(App $app, Request $req, $projectId)
    {
        $project = $app->projectService->find($projectId);
        !$projectId and $app->abort(404, 'project not found');
        $image = new \Entity\Image();
        $image->setProject($project);
        $formType = new \Form\Image();
        $form = $app->formFactory->create($formType, $image);
        if ('POST' === $req->getMethod()) {
            if ($form->handleRequest($req)->isValid()) {
                $app->imageService->create($image);
                return $app->redirect($app->url_generator->generate('project_read', array('id' => $projectId)));
            }
        }
        return $app->twig->render('image_create', array('project' => $project, 'form' => $form->createView()));

    }

    function imageUpdate(App $app, Request $req, $projectId, $imageId)
    {
        /** @var \Entity\Project $project */
        $project = $app->projectService->find($projectId);
        if (!$projectId) {
            $app->abort(404, 'project not found');
        }
        $image = $project->getImageById($imageId);
        if (!$image) {
            $app->abort(404);
        }
        $form = $app->formFactory->create(new \Form\Image(), $image);
        if ('POST' === $req->getMethod()) {
            if ($form->handleRequest($req)->isValid()) {
                $app->imageService->update($image);
                return $app->redirect($app->url_generator->generate('project_read', array('id' => $projectId)));
            }
        }
        return $app->twig->render('image_update', array(
            'project' => $project, 'form' => $form->createView(), 'image' => $image));

    }

    function imagePublish(App $app, Request $req, $projectId, $imageId, $_format)
    {
        /** @var \Entity\Project $project */
        $project = $app->projectService->find($projectId);
        if (!$projectId) {
            $app->abort(404, 'project not found');
        }
        $image = $project->getImageById($imageId);
        if (!$image) {
            $app->abort(404);
        }
        $image->setIsPublished($image->getIsPublished() === true ? false : true);
        $app->imageService->update($image);
        $app->projectService->update($project);
        if ($_format == "json") {
            return $app->json(array(
                'status' => 200, "message" => "ok",
                "image" => array(
                    'id' => $image->getId(),
                    'isPublished' => $image->getIsPublished()
                )), 200);
        }
        return "";
    }

    function imageMarkasPoster(App $app, Request $req, $imageId, $projectId)
    {
        /** @var \Entity\Project $project */
        $project = $app->projectService->find($projectId);
        if (!$project) $app->abort(404);
        $image = $project->getImageById($imageId);
        $app->imageService->markAsPoster($image);
        return new Response($app->serializer->serialize(
            array('status' => 'ok',
                'image' => $image->jsonSerialize(),
                'message' => 'image marked as project'),
            $req->getRequestFormat()));
    }

    function imageDelete(App $app, Request $req, $projectId, $imageId)
    {
        /** @var \Entity\Project $project */
        $project = $app->projectService->find($projectId);

        if (!$project) {
            $app->abort(404);
        }
        $image = $project->getImageById($imageId);
        if (!$image) {
            $app->abort(404);
        }
        $app->imageService->remove($image);
        if ($req->isXmlHttpRequest()) {
            return $app->json(array('status' => 200, 'message' => 'ok'), 200);
        }
        return $app->redirect($app->url_generator->generate('image_index', array('projectId' => $projectId)));
    }

    function imageUpload(App $app, Request $req, $projectId)
    {
        /** @var \Entity\Project $project */
        $project = $app->projectService->find($projectId);
        if ($project == null) {
            $app->abort(404);
        }
        $form = $app->formFactory->create(new \Form\Upload);
        if ($req->isXmlHttpRequest() && "POST" == $req->getMethod()) {
            try {
                if ($form->handleRequest($req)->isValid()) {
                    $data = $form->getData();
                    //loginfile_put_contents('php://stdout',var_export($files,true));
                    foreach ($data['images'] as $file) {
                        /** @var UploadedFile $file */
                        $image = $app->imageService->fromFile($file);
                        $image->setTitle($file->getClientOriginalName());
                        $image->setDescription($file->getClientOriginalName());
                        $image->setBasename($file->getClientOriginalName());
                        $image->setExtension($file->getClientOriginalExtension());
                        $project->addImage($image);
                        $app->imageService->update($image);
                    }
                    $app->projectService->update($project);
                    return $app->json(array('status' => 200, 'message' => 'ok'), 200);
                } else {
                    throw new \Exception('Form is not valid');
                }
            } catch (\Exception $e) {
                file_put_contents('php://stdout', var_export($e->getTraceAsString(), true));
                return $app->json(array('status' => 500, 'message' => $e->getMessage(), 'errors' => $form->getErrors()), 500);
            }
        }
        return $app->twig->render('image_upload', array('project' => $project, 'form' => $form->createView()));
    }

    /**
     * @inheritdoc
     */
    public
    function connect(Application $app)
    {
        /**
         * IMAGE MANAGEMENT
         */
        /** @var \Silex\ControllerCollection $imageController */
        $imageController = $app['controllers_factory'];
        $imageController->match('/', array($this, 'imageIndex'))
            ->bind('image_index');
        $imageController->match('/new', array($this, 'imageCreate'))
            ->bind('image_create');
        $imageController->post('/{imageId}/mark-as-poster.{_format}', array($this, 'imageMarkasPoster'))
            ->value('_format', 'json')
            ->bind('image_mark_as_poster');
        $imageController->match('/{imageId}/update', array($this, 'imageUpdate'))
            ->bind('image_update');
        $imageController->delete('/{imageId}/delete', array($this, 'imageDelete'))
            ->bind('image_delete');
        $imageController->post('/{imageId}/publish.{_format}', array($this, 'imagePublish'))
            ->value('_format', 'json')
            ->bind('image_publish');
        $imageController->match('/upload-multiple', array($this, 'imageUpload'))
            ->bind('image_upload');
        $imageController->post('/{imageId}/poster', array($this), 'imagePoster')
            ->bind('image_poster');
        $imageController->get('/{imageId}', array($this, 'imageRead'))
            ->bind('image_read');
        return $imageController->assert('imageId', '^[\:\w]+$');
    }
}