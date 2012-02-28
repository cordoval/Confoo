<?php

namespace Sensio\Bundle\HangmanBundle\Tests;

use Sensio\Bundle\HangmanBundle\Game\Game;

class GameTest extends \PHPUnit_Framework_TestCase
{
    public function testTryWordWithExpectedWord()
    {
        $word = $this->getWordMock('php', 'php', true);
        $word
            ->expects($this->once())
            ->method('guessed')
        ;

        $game = new Game($word);
        $this->assertTrue($game->tryWord('php'));
        $this->assertTrue($game->isWon());
        $this->assertFalse($game->isHanged());
    }

    public function testTryWordWithInvalidWord()
    {
        $game = new Game($this->getWordMock('php', 'php', false));

        $this->assertFalse($game->tryWord('foo'));
        $this->assertFalse($game->isWon());
        $this->assertTrue($game->isHanged());
        $this->assertEquals(0, $game->getRemainingAttempts());
    }

    private function getWordMock($originalWord, $expectedWord, $isGuessed)
    {
        $word = $this->getMock(
            'Sensio\Bundle\HangmanBundle\Game\Word',
            array('getWord', 'isGuessed', 'guessed'),
            array($originalWord)
        );

        $word
            ->expects($this->once())
            ->method('getWord')
            ->will($this->returnValue($expectedWord))
        ;

        $word
            ->expects($this->once())
            ->method('isGuessed')
            ->will($this->returnValue($isGuessed))
        ;

        return $word;
    }
}