<?php


class UserAuthTest extends \Silex\WebTestCase {

    /**
     * @before
     */
    public function initFixtures()
    {
        $this->app['db']->insert('user', [
            'username' => 'test@test.com',
            'password' => 'IjybRvUBCI+XMSvsPF+j851uQHigchzHwQ2AEFfWXBqrOoHGID9NkcprpDw61xDL5ejA0ALqzURYyfl95nRN8w=='
        ]);
    }

    public function testUserLogin($d)
    {
        $t = 1;
    }

    public function createApplication()
    {
        $app = require __DIR__ . '/app.php';
        return $app;
    }

} 