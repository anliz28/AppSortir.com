<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participants;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModifPaticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo')
            ->add('nom')
            ->add('prenom')
            ->add('telephone')
            ->add('mail')
            ->add('plainPassword',RepeatedType::class,
                array('type'=> PasswordType::class,
                    'required'=> false,
                    'first_options'=> array('label'=> 'Mot de passe'),
                    'second_options'=> array('label'=> 'Répéter mot de passe')
                ))
            ->add('campus', EntityType::class,
                ['class'=> Campus::class,
                    'choice_label' => function ($campus) {
                        return $campus->getNomCampus();
                    }
                ]);


        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participants::class,
        ]);
    }
}
