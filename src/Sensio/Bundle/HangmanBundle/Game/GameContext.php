<?php

namespace Sensio\Bundle\HangmanBundle\Game;

use Symfony\Component\HttpFoundation\SessionStorage\SessionStorageInterface;

class GameContext
{
    private $storage;

    private $wordList;

    public function __construct(SessionStorageInterface $storage, WordList $list)
    {
        $this->storage  = $storage;
        $this->wordList = $list;
    }

    public function getWordList()
    {
        return $this->wordList;
    }

    public function reset()
    {
        $this->storage->write('hangman', array());
    }

    public function newGame($length)
    {
        return new Game($this->getRandomWord($length));
    }

    public function getRandomWord($length)
    {
        return new Word($this->wordList->getRandomWord($length));
    }

    public function loadGame()
    {
        $data = $this->storage->read('hangman');

        if (!count($data)) {
            return false;
        }

        $word = new Word($data['word'], $data['found_letters'], $data['tried_letters']);

        return new Game($word, $data['attempts']);
    }

    public function save(Game $game)
    {
        $this->storage->write('hangman', $game->getContext());
    }
}