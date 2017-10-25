<?php

namespace AppBundle\Form;

use AppBundle\Model\RepositoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VcsRepositoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('url');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', RepositoryRepository::class);
    }

    public function getBlockPrefix()
    {
        return 'appbundle_vcs_repository';
    }
}
