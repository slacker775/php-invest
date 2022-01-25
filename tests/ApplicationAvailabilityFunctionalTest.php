<?php
namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApplicationAvailabilityFunctionalTest extends WebTestCase
{
    /**
     * @dataProvider publicUrlProvider
     */
    public function testPublicPageIsSuccessful($url)
    {
        $client = self::createClient();
        $crawler = $client->request('GET', $url);

        $this->assertResponseIsSuccessful();
        $this->assertSame('Login', $crawler->filter('#mobile-nav')->siblings()->last()->text());
    }

    public function publicUrlProvider()
    {
        //yield ['/'];
        yield ['/assets'];
        yield ['/instruments'];
        yield ['/country'];
        yield ['/currency'];
        yield ['/register'];
    }

    /**
     * @dataProvider publicLoginUrlProvider
     */
    public function testPublicPageRequestsLogin($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertResponseRedirects("http://localhost/login");
    }

    public function publicLoginUrlProvider()
    {
        yield ['/accounts'];
        yield ['/account/new'];
        yield ['/asset/new'];
        yield ['/asset/edit/1'];
        //yield ['/asset/1'];
        yield ['/instrument/new'];
        yield ['/instrument/edit/1'];
        yield ['/country/new'];
        yield ['/currency/new'];
    }

    /**
     * @dataProvider userUrlProvider
     */
    public function testUserPageIsSuccessful($url)
    {
        $client = self::createClient();
        $container = static::getContainer();
        $userRepository = $container->get(UserRepository::class);

        $demo_user = $userRepository->findOneByEmail('demo@mail.com');
        $this->assertIsObject($demo_user);
        $this->assertEquals($demo_user->getName(), "Demo User");
        
        $client->loginUser($demo_user);
        $crawler = $client->request('GET', $url);

        $this->assertResponseIsSuccessful();
        $this->assertSame('Demo User Logout', $crawler->filter('#mobile-nav')->siblings()->last()->text());
    }

    public function userUrlProvider()
    {
        yield ['/'];
        yield ['/accounts'];
        yield ['/account/new'];
        yield ['/assets'];
        yield ['/asset/new'];
        yield ['/asset/1'];
        yield ['/asset/edit/1'];
        yield ['/execution/new?instrument=1'];
        yield ['/instruments'];
        yield ['/instrument/new'];
        yield ['/instrument/1'];
        yield ['/instrument/edit/1'];
        yield ['/country'];
        yield ['/currency'];
    }
}
