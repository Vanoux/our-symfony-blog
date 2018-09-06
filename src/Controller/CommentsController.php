<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Posts;
use App\Form\CommentsType;
use App\Repository\CommentsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * @Route("/comments")
 */
class CommentsController extends AbstractController
{
    /**
     * @Route("/", name="comments_index", methods="GET")
     */
    public function index(CommentsRepository $commentsRepository): Response
    {
        return $this->render('comments/index.html.twig', ['comments' => $commentsRepository->findAll()]);
    }

    /**
     * @Route("/new", name="comments_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $comment = new Comments();
        $form = $this->createFormBuilder($comment)
            ->add('author',TextType::class)
            ->add('content',TextType::class)
            ->add('save', SubmitType::class , array('label'=>'create new comment'))
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTime('now'));
            $repository = $this->getDoctrine()->getRepository(Posts::class);
            $post = $repository->find($_GET['id']);
            $comment->setPost($post);

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('posts_show', ['id' => $post->getId()]);
        }

        return $this->render('comments/new.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="comments_show", methods="GET")
     */
    public function show(Comments $comment): Response
    {
        return $this->render('comments/show.html.twig', ['comment' => $comment]);
    }

    /**
     * @Route("/{id}/edit", name="comments_edit", methods="GET|POST")
     */
    public function edit(Request $request, Comments $comment): Response
    {
        $form = $this->createFormBuilder($comment)
        ->add('author', TextType::class)
        ->add('content', TextType::class)
        ->add('submit', SubmitType::class, array('label'=>'Edit comment'))
        ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('posts_show', ['id' => $comment->getPost()->getId()]);
        }

        return $this->render('comments/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="comments_delete", methods="DELETE")
     */
    public function delete(Request $request, Comments $comment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($comment);
            $em->flush();
        }

        return $this->redirectToRoute('comments_index');
    }
}
