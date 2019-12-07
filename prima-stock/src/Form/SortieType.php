<?php

namespace App\Form;

use App\Entity\Stocks;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Produits;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $daty   = new \DateTime(); //this returns the current date time
        $results = $daty->format('Y-m-d-H-i-s');
        $krr    = explode('-', $results);
        $results = implode("", $krr);
        $builder
            ->add('referencePanier', HiddenType::class, [
                'data' => $results,
            ])
            ->add('produit', EntityType::class, [
                'class' => Produits::class,
                'label' => 'Unité destinataire',
                'required' => true,
                'attr' => [
                    'class' => 'restante',
                    'multiple' => false,
                    'data-live-search' => true,
                ],
            ])
            ->add('reference')
            ->add('quantite', TextType::class)
            ->add('unite')
            ->add('projet')
            //->add('mouvement')
            ->add('Site')
            ->add('AutreSource', null, [
                'label' => 'Autre source'
            ])
            ->add('Remarque')
            ->add('piece', HiddenType::class, [
                'label' => 'Pièces-jointes',
                'required'   => false,
            ])
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
