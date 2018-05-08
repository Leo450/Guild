<?php

namespace App\Form;

use App\Entity\Member;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
        ;

	    if($options['admin'] == true){
		    $builder
			    ->add('roles', ChoiceType::class, [
				    'choices' => [
					    'Membre' => 'ROLE_MEMBER',
					    'Admin' => 'ROLE_ADMIN'
				    ],
				    'multiple' => true
			    ])
			    ->add('member', EntityType::class, [
				    'class' => Member::class,
				    'choice_label' => 'name',
				    'empty_data' => null,
				    'required' => false,
			    ]);
	    }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'admin' => false,
            'action' => null
        ]);
    }
}
