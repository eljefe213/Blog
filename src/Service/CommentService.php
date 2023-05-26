<?php

namespace App\Service;

use App\Entity\Article;
use App\Repository\CommentRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CommentService
{
    public function __construct(
        private RequestStack $requestStack,
        private CommentRepository $commentRepo,
        private PaginatorInterface $paginator
    ) {
    }
    public function getPaginatedComments(?Article $article = null): PaginationInterface
    {
        $request = $this->requestStack->getMainRequest();
        $page = $request->query->getInt('page', 1);
        $limit = 5;


        $commentsQuery = $this->commentRepo->findForPagination($article);

        return $this->paginator->paginate($commentsQuery, $page, $limit);
    }
}
