<?php

namespace Shu1\SimulistBundle\Datafixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shu1\SimulistBundle\Entity\Lists;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * LoadListsData Class
 *
 * @author Shuichi Ohsawa<ohsawa0515@gmail.com>
 */
class LoadListsData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
        $connection->exec('ALTER TABLE lists AUTO_INCREMENT = 1;');

        $lists = new Lists();
        $lists->setProject($this->getReference('shopping_list'));
        $lists->setTodo('お茶ペットボトル500ml');
        $lists->setPosition(0);
        $manager->persist($lists);
        $manager->flush();

        $lists = new Lists();
        $lists->setProject($this->getReference('shopping_list'));
        $lists->setTodo('味噌');
        $lists->setPosition(1);
        $manager->persist($lists);
        $manager->flush();

        $lists = new Lists();
        $lists->setProject($this->getReference('shopping_list'));
        $lists->setTodo('ネギ');
        $lists->setPosition(2);
        $manager->persist($lists);
        $manager->flush();

        $lists = new Lists();
        $lists->setProject($this->getReference('shopping_list'));
        $lists->setTodo('白菜');
        $lists->setPosition(3);
        $manager->persist($lists);
        $manager->flush();

        $lists = new Lists();
        $lists->setProject($this->getReference('shopping_list'));
        $lists->setTodo('豚肉');
        $lists->setPosition(4);
        $manager->persist($lists);
        $manager->flush();

        $lists = new Lists();
        $lists->setProject($this->getReference('oreno_task'));
        $lists->setTodo('部屋を掃除する');
        $lists->setPosition(0);
        $manager->persist($lists);
        $manager->flush();

        $lists = new Lists();
        $lists->setProject($this->getReference('oreno_task'));
        $lists->setTodo('転居ハガキを作成する');
        $lists->setTimeLimit(new \DateTime('2014-10-31 12:00'));
        $lists->setPosition(1);
        $manager->persist($lists);
        $manager->flush();

        $lists = new Lists();
        $lists->setProject($this->getReference('oreno_task'));
        $lists->setTodo('DVDを返却する');
        $lists->setTimeLimit(new \DateTime('2014-11-1 10:00'));
        $lists->setPosition(2);
        $manager->persist($lists);
        $manager->flush();
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 2;
    }
} 