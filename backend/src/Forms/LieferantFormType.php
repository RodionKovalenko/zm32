<?php

namespace App\Forms;

use App\Entity\Material\Lieferant;
use App\Validator\Constraints\UniqueFieldValue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieferantFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', null, ['mapped' => false])
            ->add('name', TextType::class)
            ->add('lieferantStammdaten', CollectionType::class, [
                'entry_type' => LieferantStammdatenType::class,
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
                'data_class' => Lieferant::class,
                'csrf_protection' => false, // Ensure CSRF protection is enabled,
                'allow_extra_fields' => true
            ]
        );
    }

    public function getBlockPrefix(): string
    {
        return 'lieferant';
    }
}
