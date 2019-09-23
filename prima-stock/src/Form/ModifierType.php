<?php

namespace App\Form;

use App\Entity\Stocks;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModifierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            #->add('quantite')
            #->add('causeAnnulation')
            #->add('dateSaisie')
            #->add('dateValidation')
            #->add('referencePanier', HiddenType::class, [
            #    'data' => $results,
            #])
            ->add('produit')
            ->add('quantite')
            ->add('unite')
            ->add('projet')
            //->add('mouvement', HiddenType::class)
            ->add('client')
            #->add('operateur')
            #->add('piece')
            #->add('validation')
            #->add('validateur')
            #->add('stock')
            #->add('etat')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Stocks::class,
        ]);
    }
}
