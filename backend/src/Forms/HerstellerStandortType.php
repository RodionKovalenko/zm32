<?php

namespace App\Forms;

use App\Entity\Material\HerstellerStandort;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HerstellerStandortType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', null, ['required' => false])
            ->add('adresse', TextType::class)
            ->add('plz', TextType::class)
            ->add('ort', TextType::class)
            ->add('telefon', TextType::class)
            ->add('url', TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => HerstellerStandort::class,
                'csrf_protection' => false, // Ensure CSRF protection is enabled,
                'allow_extra_fields' => true
            ]
        );
    }

    public function getBlockPrefix(): string
    {
        return 'herstellerstandort';
    }
}
