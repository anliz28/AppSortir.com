<?php

namespace App\Form;

use App\Entity\Etats;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('dateDebut')
            ->add('duree')
            ->add('dateCloture')
            ->add('nbInscriptionsMax')
            ->add('infosSortie')
            ->add('etatSortie')
            ->add('organisateur')
            ->add('lieu')
            ->add('etat', EntityType::class,
                ['class'=> Etats::class,
                    'choice_label' => function ($etat) {
                        return $etat->getLibelle();
                    }
                ]);
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
