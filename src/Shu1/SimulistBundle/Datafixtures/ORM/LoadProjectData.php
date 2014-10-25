<?php

namespace Shu1\SimulistBundle\Datafixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shu1\SimulistBundle\Entity\Project;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * LoadProjectData Class
 *
 * @author Shuichi Ohsawa<ohsawa0515@gmail.com>
 */
class LoadProjectData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     *
     * @var ContainerInterface
     */
    private $container;

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\DependencyInjection.ContainerAwareInterface::setContainer()
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $entityManager = $this->container->get('doctrine')->getManager();

        // auto incrementのリセット
        $connection = $entityManager->getConnection();
        $connection->exec('ALTER TABLE project AUTO_INCREMENT = 1;');

        $project = new Project();
        $project->setName('買い物リスト');
        $project->setIdentify('abcdefg');
        $manager->persist($project);
        $manager->flush();
        $this->addReference('shopping_list', $project);

        $project = new Project();
        $project->setName('俺のタスク');
        $project->setIdentify('hijklmn');
        $manager->persist($project);
        $manager->flush();
        $this->addReference('oreno_task', $project);
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 1;
    }
} 