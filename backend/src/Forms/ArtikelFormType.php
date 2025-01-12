<?php

namespace App\Forms;

use App\Entity\Department;
use App\Entity\Material\Artikel;
use App\Entity\Material\Hersteller;
use App\Entity\Material\Lieferant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArtikelFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name')
            ->add('description', TextareaType::class)
            ->add('model', TextareaType::class)
            ->add('url', TextareaType::class)
            ->add('preis', TextType::class)
             ->add('departments', EntityType::class, [
                'class' => Department::class, // The entity class
                'choice_label' => 'name', // Field to display in the dropdown
                'multiple' => true, // Allow multiple selections
                'expanded' => false, // Use dropdown (set to true for checkboxes)
                'required' => false, // Field is optional
                'placeholder' => 'Select departments', // Placeholder text
            ])
            ->add('herstellers', EntityType::class, [
                'class' => Hersteller::class, // The entity class
                'choice_label' => 'name', // Field to display in the dropdown
                'multiple' => true, // Allow multiple selections
                'expanded' => false, // Use dropdown (set to true for checkboxes)
                'required' => false, // Field is optional
                'placeholder' => 'Select departments', // Placeholder text
            ])
            ->add('lieferants', EntityType::class, [
                'class' => Lieferant::class, // The entity class
                'choice_label' => 'name', // Field to display in the dropdown
                'multiple' => true, // Allow multiple selections
                'expanded' => false, // Use dropdown (set to true for checkboxes)
                'required' => false, // Field is optional
                'placeholder' => 'Select departments', // Placeholder text
            ])
            ->add('artikelToHerstRefnummers', CollectionType::class, [
                'entry_type' => ArtikelToHerstRefnummerType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required' => false,
                'empty_data' => []
            ])
            ->add('artikelToLieferantBestellnummers', CollectionType::class, [
                'entry_type' => ArtikelToLieferantBestellnummerType::class,
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
