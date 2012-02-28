<?php

namespace Sensio\Bundle\HangmanBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\HangmanBundle\Game\Game;
use Sensio\Bundle\HangmanBundle\Game\GameContext;
use Sensio\Bundle\HangmanBundle\Game\Word;
use Sensio\Bundle\HangmanBundle\Game\WordList;

/**
 * @Route("/hangman")
 *
 */
class GameController extends Controller
{
    /**
     * This action handles the homepage of the Hangman game.
     *
     * @Route("/", name="hangman_game")
     * @Template()
     *
     * @param Request $request The request object
     * @return array Template variables
     */
    public function indexAction(Request $request)
    {
        $context = $this->get('hangman.game_context');
        $length  = $request->query->get('length', $this->container->getParameter('hangman.word_length'));

        if (!$game = $context->loadGame()) {
            $game = $context->newGame($length);
            $context->save($game);
        }

        return array('game' => $game);
    }

    /**
     * This action allows the player to try to guess a letter.
     *
     * @Route("/letter/{letter}", name="play_letter", requirements={ "letter"="[A-Z]" })
     *
     * @param string $letter The letter the user wants to try
     * @return RedirectResponse
     */
    public function letterAction($letter)
    {
        $context = $this->get('hangman.game_context');

        if (!$game = $context->loadGame()) {
            throw $this->createNotFoundException('Unable to load the previous game context.');
        }

        $game->tryLetter($letter);
        $context->save($game);

        if ($game->isWon()) {
            return $this->redirect($this->generateUrl('game_won'));
        }

        if ($game->isHanged()) {
            return $this->redirect($this->generateUrl('game_hanged'));
        }

        return $this->redirect($this->generateUrl('hangman_game'));
    }

    /**
     * This action allows the player to try to guess the word.
     *
     * @Route("/word", name="play_word")
     * @Method("POST")
     *
     * @param Request $request The Request object
     * @return RedirectResponse
     */
    public function wordAction(Request $request)
    {
        $context = $this->get('hangman.game_context');

        if (!$game = $context->loadGame()) {
            throw $this->createNotFoundException('Unable to load the previous game context.');
        }

        $game->tryWord($request->request->get('word'));
        $context->save($game);

        if ($game->isWon()) {
            return $this->redirect($this->generateUrl('game_won'));
        }

        return $this->redirect($this->generateUrl('game_hanged'));
    }

    /**
     * This action displays the hanged page.
     *
     * @Route("/hanged", name="game_hanged")
     * @Template()
     *
     * @return array Template variables
     * @throws NotFoundHttpException
     */
    public function hangedAction()
    {
        $context = $this->get('hangman.game_context');

        if (!$game = $context->loadGame()) {
            throw $this->createNotFoundException('Unable to load the previous game context.');
        }

        if (!$game->isHanged()) {
            throw $this->createNotFoundException('User is not yet hanged.');
        }

        return array('word' => $game->getWord());
    }

    /**
     * This action displays the winning page.
     *
     * @Route("/won", name="game_won")
     * @Template()
     *
     * @return array Template variables
     * @throws NotFoundHttpException
     */
    public function wonAction()
    {
        $context = $this->get('hangman.game_context');

        if (!$game = $context->loadGame()) {
            throw $this->createNotFoundException('Unable to load the previous game context.');
        }

        if (!$game->isWon()) {
            throw $this->createNotFoundException('Game is not yet won.');
        }

        return array('word' => $game->getWord());
    }

    /**
     * This action allows the user to reset the hangman game.
     *
     * @Route("/reset", name="game_reset")
     *
     * @return RedirectResponse
     */
    public function resetAction()
    {
        $context = $this->get('hangman.game_context');
        $context->reset();

        return $this->redirect($this->generateUrl('hangman_game'));
    }
}
