<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use App\Service\CommentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method User getUser()
 */
class CommentController extends AbstractController
{
    public function __construct(
        private ArticleRepository $articleRepo,
        private CommentRepository $commentRepo,
        private CommentService   $commentService
    ) {
    }
    #[Route('/ajax/comments', name: 'comment_add', methods: ['POST'])]
    public function add(Request $request, ArticleRepository $articleRepo, CommentRepository $commentRepo, EntityManagerInterface $en, UserRepository $userRepo): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->json([
                'code' => 'NOT_AUTHENTICATED'
            ], Response::HTTP_UNAUTHORIZED);
        }
        $commentData = $request->request->all('comment');

        if ($this->isCsrfTokenValid('comment-add', $commentData['_token'])) {
            return $this->json([
                'code' => 'INVALID_CSRF_TOKEN'
            ], Response::HTTP_BAD_REQUEST);
        }

        $article = $this->$articleRepo->findOneBy(['id' => $commentData['article']]);


        if (!$article) {
            return $this->json([
                'code' => 'ARTICLE_NOT_FOUND'
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->getUser();

        if (!$user) {
            return $this->json([
                'code' => 'USER_NOT_AUTHENTICATED_FULLY'
            ], Response::HTTP_BAD_REQUEST);
        }

        $comment = new Comment($article);
        $comment->setContent($commentData['content']);
        $comment->setUser($user);
        $comment->setCreatedAt(new \DateTimeImmutable());

        $en->persist($comment);
        $en->flush();

        $html = $this->renderView('comment/index.html.twig', [
            'comment' => $comment
        ]);



        return $this->json([
            'code' => 'COMMENT_ADDED_SUCCESSFULLY',
            'message' => $html,
            'numberOfComments' => $commentRepo->count(['article' => $article])
        ]);
    }
}
