<?php

namespace App\Controller;

use App\Entity\Question;
use App\Repository\QuestionRepository;
use App\Service\MarkdownHelper;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
    private $logger;
    private $isDebug;

    public function __construct(LoggerInterface $logger/*, bool $isDebug*/)
    {
        $this->logger = $logger;
    //    $this->isDebug = $isDebug;
    }


    /**
     * @Route("/", name="app_homepage")
     */
    public function homepage(QuestionRepository $repository)
    {
       // $repository = $entityManager->getRepository(Question::class);
        $questions = $repository->findAllAskedOrderByNewest();

        return $this->render('question/homepage.html.twig', [
            'questions' => $questions,
        ]);
    }

    /**
     * @Route("/questions/new")
     */
    public function new(EntityManagerInterface $entityManager)
    {
        return new Response('hola manolaaaa');

        /*
        return new Response(sprintf(
            'question is id #%d, slug %s',
            $question->getId(),
            $question->getSlug()
        ));
        */
    }

    /**
     * @Route("/questions/{slug}", name="app_question_show")
     */
    public function show(Question $question/*$slug, MarkdownHelper $markdownHelper, EntityManagerInterface $entityManager*/)
    {
       /* if ($this->isDebug) {
            $this->logger->info('We are in debug mode!');
        }*/

//        $repository = $entityManager->getRepository(Question::class);
//        /** @var Question|null $question */
//        $question = $repository->findOneBy(['slug' => $slug]);

//        if(!$question){
//            throw $this->createNotFoundException(sprintf('no question found for slug "%s"', $slug));
//        }

        $answers = [
            'Make sure your cat is sitting `purrrfectly` still 🤣',
            'Honestly, I like furry shoes better than MY cat',
            'Maybe... try saying the spell backwards?',
        ];
        $questionText = 'I\'ve been turned into a cat, any *thoughts* on how to turn back? While I\'m **adorable**, I don\'t really care for cat food.';

//        $parsedQuestionText = $markdownHelper->parse($questionText);

        return $this->render('question/show.html.twig', [
            'question' => $question,
            'answers' => $answers,
        ]);
    }

    /**
     * @Route("/questions/{slug}/vote", name="app_question_vote", methods="POST")
     */
    public function questionVote(Question $question, Request $request, EntityManagerInterface $entityManager)
    {
        $direction = $request->request->get('direction');

        if($direction === 'up'){
            $question->upVote();
        }elseif($direction === 'down'){
            $question->downVote();
        }

        $entityManager->flush();

        return $this->redirectToRoute("app_question_show", [
            'slug' => $question->getSlug(),
        ]);
    }
}
