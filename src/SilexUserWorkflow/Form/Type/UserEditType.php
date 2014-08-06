<?php

namespace SilexUserWorkflow\Form\Type;


use Symfony\Component\Form\FormBuilderInterface;

class UserEditType extends UserAbstractType
{
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'user_form_edit';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nickname', 'text', $this->getNicknameOptions())
            ->add('Save', 'submit');
    }


} 