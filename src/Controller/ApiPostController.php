<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiPostController extends AbstractController
{
    /**
     * @Route("/api/post", name="api_post_index", methods={"GET"})
     */
    public function index(PostRepository $postRepository) //SerializerInterface $serializer
    {
        //$posts = $postRepository->findAll(); //On recupère tous nos article dans la BD

        // Le processus de SERIALISATION comprendre la Normalisation et l'encodage.On peux le faire par un SERVICE qu'on appelle le SerializerInterface

        //$postsNormalises = $normalizer->normalize($posts, null, ['groups'=>'post:read']); //NORMALISATION => On transforme ces Objets conplexes en tableaux associatifs, via le SERVICE NormalizeInterface de symfony.

        //$json = json_encode($postsNormalises); // ENCODAGA => Ensuite on transforme les tableaux associatifs en du JSON, via la fonction json_encode()

       // $json = $serializer->serialize($posts, 'json', ['groups' => 'post:read']);

       // $response = new Response($json, 200, [   // On le retourne ensuite via la classe Response.
       //     "Content-Type" => "application/json"
       // ]);

       // $response = new JsonResponse($json, 200, [], true);

       // return $response;

        //On peux reduir tout ça dans une ligne de code qui est :
        return $this->json($postRepository->findAll(), 200, [], ['groups' => 'post:read']);
    }

    /**
     * @Route("/api/post", name="api_post_store", methods={"POST"})
     */
    public function store(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator){
        
        $jsonRecu = $request->getContent();

        try{

            $post = $serializer->deserialize($jsonRecu, Post::class, 'json'); 

            $post->setCreatedAt(new \DateTime()); 
            // Quand je fini de créer mon poste je vais demandé de le valider.
            $errors = $validator->validate($post);
            // Et s'il y a des erreurs, on ne va pas au persiste , il va falloir arreter et envoyer un message d'erreur en json.
            if(count($errors) >0){
               return $this->json($errors, 400);
            }

            $em->persist($post);
            $em->flush();

            return $this->json($post, 201, [], ['groups' => 'post:read']);

        }catch(NotEncodableValueException $e){
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);

        }

        
    }
}
