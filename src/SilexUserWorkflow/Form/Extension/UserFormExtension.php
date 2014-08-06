<?php

namespace SilexUserWorkflow\Form\Extension;

use Symfony\Component\Form\AbstractExtension;

class UserFormExtension extends AbstractExtension
{
    protected  $initialTypes;

    public function __construct($types = [])
    {
        $this->initialTypes = $types;
    }

    protected function loadTypes()
    {
        return $this->initialTypes;
    }
} 