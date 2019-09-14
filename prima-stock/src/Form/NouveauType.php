<?php

namespace App\Form;

use App\Entity\Stocks;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class NouveauType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            
            #->add('causeAnnulation')
            #->add('dateSaisie')
            #->add('dateValidation')
            ->add('referencePanier', HiddenType::class)
            ->add('produit')
            ->add('quantite')
            ->add('unite')
            ->add('projet')
            ->add('mouvement')
            ->add('client')
            #->add('operateur')
            #->add('piece')
            #->add('validation')
            #->add('validateur')
            #->add('stock')
            #->add('etat')
            ->add('Ajouter', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Stocks::class,
        ]);
    }
}
