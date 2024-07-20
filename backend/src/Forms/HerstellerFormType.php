<?php

namespace App\Forms;

use App\Entity\Material\Hersteller;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HerstellerFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', null, ['mapped' => false])
            ->add('name', TextType::class)
            ->add('standorte', CollectionType::class, [
                'entry_type' => HerstellerStandortType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required' => false,
                'empty_data' => []
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Hersteller::class,
                'csrf_protection' => false, // Ensure CSRF protection is enabled,
                'allow_extra_fields' => true
            ]
        );
    }

    public function getBlockPrefix(): string
    {
        return 'hersteller';
    }
}
