<?php

namespace App\Forms;

use App\Entity\Material\Artikel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArtikelFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name')
            ->add('description', TextareaType::class)
            ->add('model', TextareaType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Artikel::class,
                'csrf_protection' => false, // Ensure CSRF protection is enabled,
                'allow_extra_fields' => true,
            ]
        );
    }

    public function getBlockPrefix(): string
    {
        return 'artikel';
    }
}
