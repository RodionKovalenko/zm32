<?php

namespace App\Forms;

use App\Entity\Material\Artikel;
use App\Entity\Material\ArtikelToLieferBestellnummer;
use App\Entity\Material\Lieferant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArtikelToLieferantBestellnummerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('artikel', EntityType::class, [
                'class' => Artikel::class,
                'choice_label' => 'name', // Adjust accordingly
            ])
            ->add('lieferant', EntityType::class, [
                'class' => Lieferant::class,
                'choice_label' => 'name', // Adjust accordingly
            ])
            ->add('bestellnummer', TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => ArtikelToLieferBestellnummer::class,
                'csrf_protection' => false, // Ensure CSRF protection is enabled,
                'allow_extra_fields' => true,
            ]
        );
    }

    public function getBlockPrefix(): string
    {
        return 'artikelToLieferantBestellnummer';
    }
}