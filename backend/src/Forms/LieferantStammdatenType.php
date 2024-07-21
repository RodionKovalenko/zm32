<?php

namespace App\Forms;

use App\Entity\Stammdaten\LieferantStammdaten;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieferantStammdatenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', null, ['required' => false])
            ->add('adresse', TextType::class)
            ->add('plz', TextType::class)
            ->add('ort', TextType::class)
            ->add('telefon', TextType::class)
            ->add('url', TextType::class)
            ->add('startdatum', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('enddatum', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => LieferantStammdaten::class,
                'csrf_protection' => false, // Ensure CSRF protection is enabled,
                'allow_extra_fields' => true
            ]
        );
    }

    public function getBlockPrefix(): string
    {
        return 'lieferantStammdaten';
    }
}
