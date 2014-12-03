<?php

namespace Shu1\SimulistBundle\Form;

use Shu1\SimulistBundle\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * リストのプロジェクト作成フォーム
 *
 * @author Shuichi Ohsawa<ohsawa0515@gmail.com>
 */
class ProjectType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                'text',
                [
                    'label'           => 'リスト名',
                    'max_length'      => 300,
                    'invalid_message' => 'リスト名が正しくありません',
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'Shu1\SimulistBundle\Entity\Project',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'projecttype';
    }
}