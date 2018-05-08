<?php

namespace App\Form;

use App\Entity\Member;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MemberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
	        ->add('level', IntegerType::class, [
		        'data' => 110
	        ])
            ->add('class', ChoiceType::class, [
	            'choices' => Member::$classes,
            ])
            ->add('race', ChoiceType::class, [
	            'choices' => Member::$races,
            ])
            ->add('gender', ChoiceType::class, [
	            'choices' => Member::$genders,
            ])
            ->add('disponibilities', ChoiceType::class, [
	            'choices' => [
		            'Lundi' => 0,
		            'Mardi' => 1
	            ],
	            'multiple' => true,
	            'required' => false
            ])
        ;

	    if($options['admin'] == true){
		    $builder
			    ->add('user', EntityType::class, [
				    'class' => User::class,
				    'choice_label' => 'username',
				    'empty_data' => null,
				    'required' => false,
			    ]);
	    }

	    if($options['action'] == "new"){
		    $builder
			    ->add('create_account', CheckboxType::class, [
				    'mapped' => false,
				    'required' => false
			    ]);
	    }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Member::class,
	        'admin' => false,
	        'action' => null
        ]);
    }
}
