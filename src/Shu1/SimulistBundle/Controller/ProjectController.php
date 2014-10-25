<?php

namespace Shu1\SimulistBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

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
            echo $exception->getMessage();
            exit;
        }

        return [
            'project' => $project,
            'lists'   => $lists,
        ];
    }

    /**
     * Todoリストの削除
     *
     * @Route("/{identify}/{id}/delete", name="project_delete")
     *
     * @param int    $id
     * @param string $identify
     *
     * @return mixed
     */
    public function deleteAction($id, $identify)
    {
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
            echo $exception->getMessage();
            exit;
        }

        // 見つからない場合
        if (!$list) {
            throw $this->createNotFoundException('リストが見つかりませんでした');
        }

        $entityManager->remove($list);
        $entityManager->flush();

        return $this->redirect($this->generateUrl('project_index', ['identify' => $identify]));
    }

} 