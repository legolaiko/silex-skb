<?php


class UserLoginTest extends \Silex\WebTestCase {

    /**
     * @before
     */
    public function initFixtures()
    {
        $this->app['db']->insert('user', [
            'username' => 'test@test.com',
            // encrypted 1234 pwd (with salt 1234)
            'password' => 'IjybRvUBCI+XMSvsPF+j851uQHigchzHwQ2AEFfWXBqrOoHGID9NkcprpDw61xDL5ejA0ALqzURYyfl95nRN8w=='
        ]);
    }

    public function testValidLogin()
    {
        $client  = $this->createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/user/login');
        $form    = $crawler->selectButton('user_form_auth_signIn')->form();
        $crawler = $client->submit($form, [
            'user_form_auth[username]' => 'test@test.com',
            'user_form_auth[password]' => '1234'
        ]);

        $this->assertEquals(
            0,
            $crawler->filter('ul.form-errors')->count()
        );
    }

    public function testRememberMeLogin()
    {
        $client  = $this->createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/user/login');
        $form    = $crawler->selectButton('user_form_auth_signIn')->form();
        $crawler = $client->submit($form, [
            'user_form_auth[username]'   => 'test@test.com',
            'user_form_auth[password]'   => '1234',
            'user_form_auth[rememberMe]' => true
        ]);

        $cookie = $client->getCookieJar()->get('REMEMBERME');
        $this->assertNotNull($cookie);
    }

    /**
     * @param $username
     * @param $password
     *
     * @dataProvider invalidLoginData
     */
    public function testInvalidLogin($username, $password)
    {
        $client  = $this->createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/user/login');
        $form    = $crawler->selectButton('user_form_auth_signIn')->form();
        $crawler = $client->submit($form, [
            'user_form_auth[username]' => $username,
            'user_form_auth[password]' => $password
        ]);

        $this->assertGreaterThan(
                0,
            $crawler->filter('ul.form-errors:contains("Bad credentials")')->count()
        );
    }

    public function invalidLoginData()
    {
        return [
            ['',               ''],
            ['test@test.com',  ''],
            ['test@test.com',  '12345'],
            ['test1@test.com', '1234']
        ];
    }

    public function createApplication()
    {
        $app = require __DIR__ . '/app.php';
        return $app;
    }

} 