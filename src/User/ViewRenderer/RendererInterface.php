<?php

namespace User\ViewRenderer;


interface RendererInterface
{
    /**
     * Renders specified view with given context
     *
     * @param string $viewName View name
     * @param array $context View context
     * @return mixed
     */
    public function render($viewName, $context = []);
} 