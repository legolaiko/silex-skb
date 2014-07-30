<?php


namespace User\ViewRenderer;


class TwigRenderer implements RendererInterface
{
    protected $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Renders specified view with given context
     *
     * @param string $viewName View name
     * @param array $context View context
     * @return mixed
     */
    public function render($viewName, $context = [])
    {
        return $this->twig->render($viewName . '.twig', $context);
    }


} 