<?php


namespace User;


use Pimple\Container;
use Pimple\ServiceProviderInterface;

class UserServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple An Container instance
     */
    public function register(Container $pimple)
    {
        $pimple['user.manager'] = function() use ($pimple) {

            // TODO implement 'assertRegistered' method for dependent services
            if (!isset($pimple['form.factory'])) {
                throw new \RuntimeException('Can\'t resolve form factory from the container');
            }
            if (!isset($pimple['security.encoder_factory'])) {
                throw new \RuntimeException('Can\'t security.encoder_factory from the container');
            }
            if (!isset($pimple['db'])) {
                throw new \RuntimeException('Can\'t db from the container');
            }
            return new UserManager($pimple['form.factory'], $pimple['security.encoder_factory'], $pimple['db']);
        };
    }

} 