<?php

namespace Sensio\Bundle\HangmanBundle\Tests;

use Sensio\Bundle\HangmanBundle\Game\Word;

class WordTest extends \PHPUnit_Framework_TestCase
{
    public function testIsGuessed()
    {
        $word = new Word('php');
        $word->tryLetter('h');
        $this->assertFalse($word->isGuessed());

        $word->tryLetter('p');
        $this->assertTrue($word->isGuessed());
    }

    public function testTryLetterTwiceInARow()
    {
        $word = new Word('php');
        $word->tryLetter('h');

        $this->setExpectedException('InvalidArgumentException');
        $word->tryLetter('h');
    }

    public function testTryLetterDoesNotAcceptNonAsciiLetter()
    {
        $this->setExpectedException('InvalidArgumentException');

        $word = new Word('php');
        $word->tryLetter('0');
    }

    public function testTryLetter()
    {
        $word = new Word('php');
        $this->assertTrue($word->tryLetter('h'));
        $this->assertContains('h', $word->getFoundLetters());
        $this->assertContains('h', $word->getTriedLetters());

        $this->assertFalse($word->tryLetter('x'));
        $this->assertContains('x', $word->getTriedLetters());
        $this->assertNotContains('x', $word->getFoundLetters());
    }
}