<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Entity\Comments;
use App\Form\PostsType;
use App\Repository\PostsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
/**
 * @Route("/posts")
 */
class PostsController extends AbstractController
{
    /**
     * @Route("/", name="posts_index", methods="GET")
     */
    public function index(PostsRepository $postsRepository): Response
    {
        return $this->render('posts/index.html.twig', ['posts' => $postsRepository->findAll()]);
    }

    /**
     * @Route("/new", name="posts_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $post = new Posts();
        $form = $this->createFormBuilder($post)
            ->add('title', TextType::class)
            ->add('author', TextType::class)
            ->add('content', TextType::class)
            ->add('save', SubmitType::class, array('label'=>'create new post'))
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setCreatedAt(new \DateTime('now'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('posts_index');
        }

        return $this->render('posts/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="posts_show", methods="GET")
     */
    public function show(Posts $post): Response
    {
        $repository = $this->getDoctrine()->getRepository(Comments::class); 
        $comments = $repository->findBy(
            ['post' => $post->getId()]
        );

        return $this->render('posts/show.html.twig', ['post' => $post, 'comments' => $comments]);
    }

    /**
     * @Route("/{id}/edit", name="posts_edit", methods="GET|POST")
     */
    public function edit(Request $request, Posts $post): Response
    {
        $form = $this->createFormBuilder($post)
        ->add('title', TextType::class)
        ->add('author', TextType::class)
        ->add('content', TextType::class)
        ->add('save', SubmitType::class, array('label'=>'edit'))
        ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setUpdatedAt(new \DateTime('now'));
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('posts_index');
        }

        return $this->render('posts/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="posts_delete", methods="DELETE")
     */
    public function delete(Request $request, Posts $post): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($post);
            $em->flush();
        }

        return $this->redirectToRoute('posts_index');
    }
}
