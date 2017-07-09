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
        return new JsonResponse($mostSpokenThemeOfTheMonth);

    }

    /**
     * @Route("/mostRatedTopicsOfTheMonth", name="mostRatedTopicsOfTheMonth")
     */
    public function mostInfluencedTopicOfTheMonthAction(Request $request)
    {
        $mostInfluencedThemeOfTheMonth = $this->get('most_influenced_of_the_month')->search();
        return new JsonResponse($mostInfluencedThemeOfTheMonth);

    }

    /**
     * @Route("/evolutionMostSpokenTopic", name="evolutionMostSpokenTopic")
     */
    public function evolutionMostSpokenTopic(Request $request)
    {
        //$evolutionResultOfATopic = $this->get('evolution_of_the_most_spoken_topics')->setEvolutionChartOfTheMostSpoken($topic);
        $evolutionResultOfATopic = $this->get('evolution_of_the_most_spoken_topics')->setEvolutionChartOfTheMostSpoken('london');
        return new JsonResponse($evolutionResultOfATopic);

    }

    /**
     * @Route("/lastPost", name="lastPost")
     */
    public function lastPostAction(Request $request)
    {
        $lastPost = $this->get('last_post')->getLatestPost();
        return new JsonResponse($lastPost);

    }

    /**
     * @Route("/searchInPosts", name="searchInPosts")
     */
    public function searchInPostsAction(Request $request/*,string $searchedWord*/)
    {
        $foundInPosts = $this->get('search_in_posts')->getResultSearch('london');
        //$foundInPosts = $this->get('search_in_posts')->getResultSearch($searchedWord);
        return new JsonResponse($foundInPosts);

    }
}