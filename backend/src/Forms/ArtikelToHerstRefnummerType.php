<?php

namespace App\Forms;

use App\Entity\Material\Artikel;
use App\Entity\Material\ArtikelToHerstRefnummer;
use App\Entity\Material\Hersteller;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArtikelToHerstRefnummerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('artikel', EntityType::class, [
                'class' => Artikel::class,
                'choice_label' => 'name', // Adjust accordingly
            ])
            ->add('hersteller', EntityType::class, [
                'class' => Hersteller::class,
                'choice_label' => 'name', // Adjust accordingly
            ])
            ->add('refnummer', TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => ArtikelToHerstRefnummer::class,
                'csrf_protection' => false, // Ensure CSRF protection is enabled,
                'allow_extra_fields' => true,
            ]
        );
    }

    public function getBlockPrefix(): string
    {
        return 'artikelToHerstRefnummer';
    }
}