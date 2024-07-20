<?php

namespace App\Forms;

use App\Entity\Bestellung;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BestellungFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('artikel')
            ->add('description', TextareaType::class)
            ->add('descriptionZusatz', TextareaType::class)
            ->add('preis', TextType::class)
            ->add('amount', TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Bestellung::class,
                'csrf_protection' => false, // Ensure CSRF protection is enabled,
                'allow_extra_fields' => true,
            ]
        );
    }

    public function getBlockPrefix(): string
    {
        return 'bestellung';
    }
}
