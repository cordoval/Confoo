<?php

namespace Sensio\Bundle\HangmanBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Sensio\Bundle\HangmanBundle\Game\Game;

class GameControllerTest extends WebTestCase
{
    private $client;

    public function testResetGameAction()
    {
        //$this->client->followRedirects();

        $this->client->request('GET', '/game/hangman/');
        $crawler = $this->playLetter('H');

        $link = $crawler->selectLink('Reset the game')->link();
        $this->client->click($link);
        $crawler = $this->client->followRedirect();

        $this->assertEquals(0, $crawler->filter('#content .word_letters .guessed')->count());
        $this->assertEquals(3, $crawler->filter('#content .word_letters .hidden')->count());
    }

    public function testTryInvalidLetterAction()
    {
        $this->client->request('GET', '/game/hangman/');

        for ($i = 1; $i <= Game::MAX_ATTEMPTS; $i++) {
            $this->playLetter('X');
        }

        $response = $this->client->getResponse();
        $this->assertRegexp("#Oops, you're hanged#", $response->getContent());
    }

    public function testTryLetterAction()
    {
        $crawler = $this->client->request('GET', '/game/hangman/');
        $crawler = $this->playLetter('P');

        $this->assertEquals(2, $crawler->filter('#content .word_letters .guessed')->count());
        $this->assertEquals(1, $crawler->filter('#content .word_letters .hidden')->count());

        $crawler = $this->playLetter('H');
        $response = $this->client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertRegexp(
            '#You found the word <strong>php<\/strong>#',
            $response->getContent()
        );
    }

    private function playLetter($letter)
    {
        $crawler = $this->client->getCrawler();

        $link = $crawler->selectLink($letter)->link();

        $this->client->click($link);

        return $this->client->followRedirect();
    }

    public function testTryWrongWordAction()
    {
        $this->playWord('foo');

        $response = $this->client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertRegexp("#Oops, you're hanged#", $response->getContent());
    }

    public function testTryWordAction()
    {
        $this->playWord('php');

        $response = $this->client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertRegexp(
            '#You found the word <strong>php<\/strong>#',
            $response->getContent()
        );
    }

    private function playWord($word)
    {
        $crawler = $this->client->request('GET', '/game/hangman/');

        $form = $crawler->selectButton('Let me guess...')->form();
        $this->client->submit($form, array('word' => $word));

        $this->client->followRedirect();
    }

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function tearDown()
    {
        $this->client = null;
    }
}
