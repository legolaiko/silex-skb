<?php


class UserRegisterTest extends \Silex\WebTestCase
{

    public function testEntryPage()
    {
        $client  = $this->createClient();
        $crawler = $client->request('GET', '/user/register');
        $this->assertGreaterThan(
            0,
            $crawler->filter('form[name=form_register]')->count()
        );

        return $crawler;
    }


    /**
     * @param $username
     * @param $nickname
     * @param $password
     * @param $repeatPassword
     *
     * @dataProvider invalidRegistrationData
     */
    public function testInvalidRegistration($username, $nickname, $password, $repeatPassword)
    {
        $client  = $this->createClient();
        $crawler = $client->request('GET', '/user/register');
        $form    = $crawler->selectButton('form_register_signUp')->form();
        $crawler = $client->submit($form, [
            'form_register[username]'         => $username,
            'form_register[nickname]'         => $nickname,
            'form_register[password][first]'  => $password,
            'form_register[password][second]' => $repeatPassword
        ]);
        $this->assertGreaterThan(
            0,
            $crawler->filter('ul.form-errors')->count()
        );
    }

    public function invalidRegistrationData()
    {
        return [
            ['',                '',     '',     ''],     // all empty
            ['none-email',      'Nick', '1234', '1234'], // username is not email
            ['email@email.com', 'Nick', '12',   '12'],   // short password
            ['email@email.com', 'Nick', '1234', '2345'], // different passwords
        ];
    }

    public function testValidRegistration()
    {
        $user = [
            'username' => 'test@test.com',
            'nickname' => 'Test',
            'password' => '1234'
        ];

        $client  = $this->createClient();
        $crawler = $client->request('GET', '/user/register');
        $form    = $crawler->selectButton('form_register_signUp')->form();
        $crawler = $client->submit($form, [
            'form_register[username]'         => $user['username'],
            'form_register[nickname]'         => $user['nickname'],
            'form_register[password][first]'  => $user['password'],
            'form_register[password][second]' => $user['password']
        ]);

        $this->assertEquals(
            0,
            $crawler->filter('ul.form-errors')->count()
        );

        return $user;
    }

    public function createApplication()
    {
        $app = require __DIR__ . '/app.php';
        return $app;
    }


} 