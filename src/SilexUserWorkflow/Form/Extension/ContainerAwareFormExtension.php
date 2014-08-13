<?php

namespace SilexUserWorkflow\Form\Extension;

use Symfony\Component\Form\AbstractExtension;

class ContainerAwareFormExtension extends AbstractExtension
{
    protected $typeServiceIds;
    protected $container;

    public function __construct($typeServiceIds, \ArrayAccess $container)
    {
        $this->typeServiceIds = $typeServiceIds;
        $this->container      = $container;
    }

    protected function loadTypes()
    {
        $types = [];
        foreach($this->typeServiceIds as $serviceId) {
            $types[] = $this->container[$serviceId];
        }
        return $types;
    }
} 