<?php

namespace Shu1\SimulistBundle\Controller;

use Shu1\SimulistBundle\Entity\Lists;
use Shu1\SimulistBundle\Form\TaskType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Todoリストのプロジェクトコントローラ
 *
 * @Route("/project")
 * @author Shuichi Ohsawa<ohsawa0515@gmail.com>
 */
class ProjectController extends Controller
{
    /**
     * Todoリストの一覧
     *
     * @Route("/{identify}", name="project_index")
     * @Template()
     *
     * @param string identify
     *
     * @return array
     */
    public function indexAction($identify)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $project       = $entityManager->getRepository('Shu1SimulistBundle:Project')->findOneBy(
            [
                'identify' => $identify,
            ]
        );
        // 見つからない場合はTOPにリダイレクト
        if (!$project) {
            return $this->redirect($this->generateUrl('index'));
        }

        $queryBuilder = $entityManager->createQueryBuilder();
        try {
            $queryBuilder
                ->select('l, p')
                ->from('Shu1SimulistBundle:Lists', 'l')
                ->innerJoin('l.project', 'p')
                ->where('p.identify = :identify')
                ->setParameter('identify', $identify);
            $lists = $queryBuilder->getQuery()->getResult();
        } catch (\Exception $exception) {
            $this->get('logger')->error($exception->getMessage());

            return [];
        }

        // CSRF対策のトークン発行
        $token = $this->get('form.csrf_provider')->generateCsrfToken('csrf_token');

        return [
            'project'    => $project,
            'lists'      => $lists,
            'csrf_token' => $token
        ];
    }

    /**
     * タスクの追加
     *
     * @Route("/add/", name="project_add")
     * @Method({"POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function addAction(Request $request)
    {
        // TODO レスポンス形式はとりあえず適当
        // TODO addAction, doneAction, deleteActionは別コントローラに移す(TaskControllerとか)
        $task = $request->get('task');

        // CSRF対策のトークン検証
        $token = $request->headers->get('X-CSRF-Token');
        if (false === $this->get('form.csrf_provider')->isCsrfTokenValid('csrf_token', $token)) {
            return new Response('token is invalid.', 400);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $project = $entityManager->getRepository('Shu1SimulistBundle:Project')->findOneBy(
            [
                'identify' => $request->get('identify'),
            ]
        );

        if (!$project) {
            return new Response('ng', 404);
        }

        $lists = new Lists();
        $lists->setTodo($task);
        $lists->setProject($project);
        // TODO 他のTODOのPositionを変更する処理が必要
        $lists->setPosition(0);
        $lists->setStatus(0);

        $validator = $this->get('validator');
        $errors    = $validator->validate($lists);

        if (count($errors) > 0) {
            return new Response((string)$errors, 400);
        }

        $entityManager->persist($lists);
        $entityManager->flush();

        return new Response('ok', 200);

    }

    /**
     * タスクの完了
     *
     * @Route("/done/", name="project_done")
     * @Method({"POST"})
     *
     * @return Respose
     */
    public function doneAction(Request $request)
    {
        // TODO レスポンス形式はとりあえず適当
        // FIXME update_atが動いていない
        $id     = $request->get('id');
        $status = $request->get('status');

        $entityManager = $this->getDoctrine()->getManager();
        $queryBuilder  = $entityManager->createQueryBuilder();
        try {
            $queryBuilder
                ->update('Shu1SimulistBundle:Lists', 'l')
                ->set('l.status', $status)
                ->where('l.id = :id')
                ->setParameter('id', $id);
            $result = $queryBuilder->getQuery()->execute();
            $entityManager->flush();
        } catch (\Exception $exception) {
            $this->get('logger')->error($exception->getMessage());

            return new Response('ng', 404);
        }

        if (!$request) {
            return new Response('ng', 404);
        }

        return new Response('ok', 200);
    }


    /**
     * タスクの削除
     *
     * @Route("/delete/", name="project_delete")
     * @Method({"POST"})
     *
     * @return Respose
     */
    public function deleteAction(Request $request)
    {
        // TODO レスポンス形式はとりあえず適当
        // TODO HTTPメソッドをPOSTからDELETEに変更する予定
        // TODO プロジェクトIDはいらないかも？ジョインするだけ遅くなるし意味がない気がする

        $id       = $request->get('id');
        $identify = $request->get('identify');

        $entityManager = $this->getDoctrine()->getManager();
        $queryBuilder  = $entityManager->createQueryBuilder();
        try {
            $queryBuilder
                ->select('l, p')
                ->from('Shu1SimulistBundle:Lists', 'l')
                ->innerJoin('l.project', 'p')
                ->where('p.identify = :identify')
                ->andWhere('l.id = :id')
                ->setParameter('identify', $identify)
                ->setParameter('id', $id);
            $list = $queryBuilder->getQuery()->getSingleResult();
        } catch (\Exception $exception) {
            $this->get('logger')->error($exception->getMessage());

            return new Response('ng', 404);
        }

        // 見つからない場合
        if (!$list) {
            return new Response('ng', 404);
        }

        $entityManager->remove($list);
        $entityManager->flush();

        return new Response('ok', 200);
    }

} 