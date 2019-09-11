<?php

namespace App\Form;

use App\Entity\Stocks;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StocksType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantite')
            ->add('causeAnnulation')
            ->add('dateSaisie')
            ->add('dateValidation')
            ->add('referencePanier')
            ->add('produit')
            ->add('projet')
            ->add('mouvement')
            ->add('client')
            ->add('unite')
            ->add('operateur')
            ->add('piece')
            ->add('validation')
            ->add('validateur')
            ->add('stock')
            ->add('etat')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Stocks::class,
        ]);
    }
}
