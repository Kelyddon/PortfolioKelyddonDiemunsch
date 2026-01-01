<?php

namespace App\Form;

use App\Entity\Project;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            // Unmapped file field: we'll handle the upload in controller
            ->add('image', FileType::class, [
                'mapped' => false,
                'required' => false,
            ])
            // Choices provided by controller from HardSkill list
            ->add('langages', ChoiceType::class, [
                'choices' => $options['langage_choices'] ?? [],
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'help' => 'Sélectionner un ou plusieurs langages depuis vos Hard skills',
            ])
            ->add('description')
            ->add('GithubLink')
            ->add('createAt', null, [
                'widget' => 'single_text',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
            'langage_choices' => [],
        ]);
    }
}
