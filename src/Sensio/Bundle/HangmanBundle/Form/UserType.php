<?php

namespace Sensio\Bundle\HangmanBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class UserType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('password', 'repeated', array(
                'type' => 'password',
                'first_name' => 'password',
                'second_name' => 'confirmation'
            ))
            ->add('email', 'email')
        ;
    }

    public function getName()
    {
        return 'user';
    }
}
