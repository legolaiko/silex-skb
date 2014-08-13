<?php


namespace SilexUserWorkflow\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserAuthType extends AbstractType
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