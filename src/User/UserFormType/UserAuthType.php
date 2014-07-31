<?php


namespace User\UserFormType;


use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserAuthType extends UserAbstractType
{
    public function getName()
    {
        return 'user_form_auth';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'email', [
                'label' => 'Email',
                'data'  => $options['username']
            ])
            ->add('password', 'password')
            ->add('rememberMe', 'checkbox', ['required' => false])
            ->add('signIn', 'submit');
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(['username' => '']);
    }


} 