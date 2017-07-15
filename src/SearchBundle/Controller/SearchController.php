<?php
namespace SearchBundle\Controller;

use SearchBundle\Entity\NormalizedPost;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller
{
    /**
     *
     * @Route("/mostSpokenTopicsOfMonth", name="mostSpokenTopicsOfMonth")
     */
    public function mostSpokenTopicsOfTheMonthControllerAction(Request $request)
    {
        $mostSpokenThemeOfTheMonth = $this->get('most_spoken_of_the_month')->search();
        return new JsonResponse($mostSpokenThemeOfTheMonth, 200, ['Access-Control-Allow-Origin'=>'*']);

    }

    /**
     * @Route("/mostRatedTopicsOfTheMonth", name="mostRatedTopicsOfTheMonth")
     */
    public function mostInfluencedTopicOfTheMonthAction(Request $request)
    {
        $mostInfluencedThemeOfTheMonth = $this->get('most_influenced_of_the_month')->search();
        return new JsonResponse($mostInfluencedThemeOfTheMonth, 200, ['Access-Control-Allow-Origin'=>'*']);

    }

    /**
     * @Route("/evolutionMostSpokenTopic", name="evolutionMostSpokenTopic")
     */
    public function evolutionMostSpokenTopic(Request $request)
    {
        $topic = $request->get('topic_key');
        $evolutionResultOfATopic = $this->get('evolution_of_the_most_spoken_topics')->setEvolutionChartOfTheMostSpoken($topic);
        return new JsonResponse($evolutionResultOfATopic, 200, ['Access-Control-Allow-Origin'=>'*']);

    }

    /**
     * @Route("/lastPost", name="lastPost")
     */
    public function lastPostAction(Request $request)
    {
        $lastPost = $this->get('last_post')->getLatestPost();
        return new JsonResponse($lastPost, 200, ['Access-Control-Allow-Origin'=>'*']);

    }

    /**
     * @Route("/searchInPosts", name="searchInPosts")
     */
    public function searchInPostsAction(Request $request)
    {
        $searchedWord = $request->get('searched_word');
        $foundInPosts = $this->get('search_in_posts')->getResultSearch($searchedWord);
        if ($foundInPosts === null){
            return new JsonResponse('Request not found', 404, ['Access-Control-Allow-Origin'=>'*']);
        }
        return new JsonResponse($foundInPosts, 200, ['Access-Control-Allow-Origin'=>'*']);

    }
}