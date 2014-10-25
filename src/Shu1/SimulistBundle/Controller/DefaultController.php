<?php

namespace Shu1\SimulistBundle\Controller;

use Shu1\SimulistBundle\Entity\Project;
use Shu1\SimulistBundle\Form\ProjectType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * DefaultController Class
 *
 * @author Shuichi Ohsawa<ohsawa0515@gmail.com>
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="index")
     * @Template()
     *
     * @return array
     */
    public function indexAction()
    {
        return [];
    }

    /**
     * @Route("/new", name="new")
     * @Template()
     *
     * @param Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function newAction(Request $request)
    {
        $project = new Project();
        $form    = $this->createForm(new ProjectType(), $project);
        $form->handleRequest($request);

        // フォーム内容のバリデーション
        if ($form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            // 識別子の生成
            $identify = hash('sha256', (uniqid(mt_rand(), true)));

            $project->setIdentify($identify);
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirect($this->generateUrl('project_index', ['identify' => $identify]));
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
