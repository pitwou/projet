<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use AppBundle\Form\EvaluationType;
use AppBundle\Entity\Product;
use AppBundle\Entity\Evaluation;

class ProductController extends Controller
{
	/**
     * @Route("/product", name="product")
     * @Template("product.html.twig")
     */
    public function productAction(Request $request)
    {        
    	


        $json_prod = 'https://fr.openfoodfacts.org/api/v0/produit/'.$request->query->get('code_barre').'.json';
                
        $prod = json_decode(file_get_contents($json_prod), true);

        $em = $this->getDoctrine()->getManager();

        $database_prod = $em->getRepository('AppBundle:Product')->findBy(array('barCode' => $prod['code']));
        $database_prod = $database_prod[0];
        $database_prod->setNbConsult($database_prod->getNbConsult()+1);
        $database_prod->setLastView(new \DateTime("now"));
        $em->persist($database_prod);
        $em->flush();
		array_push($prod, ["nbView" => $database_prod->getNbConsult()]);

		$all_product = $em->getRepository('AppBundle:Evaluation')->findBy(array('prod' => $database_prod->getId()));
        
        array_push($prod, ["allProd" => $all_product]);

        $products_average = $this->getDoctrine()
        ->getRepository(Evaluation::class)
        ->findByAverageProd($database_prod->getId());
        if($products_average != null){
        	array_push($prod, ["averageProd" => $products_average[0][1]]);
        }

        $user = $this->getUser();
        if(!empty($user) && $user->getId() != null){
	        $database_user = $em->getRepository('AppBundle:User')->findBy(array('id' => $user->getId()));
	        $database_user = $database_user[0];
	        $database_eval = $em->getRepository('AppBundle:Evaluation')->findBy(array('user' => $database_user->getId(), 'prod' => $database_prod->getId() ));

	        if($database_eval == null){
	        	$form = $this->createForm(EvaluationType::class);
	        	return  ['form' => $form->createView(), 'product' => $prod ];
	        }
	        else{
	        	return  ['product' => $prod ];
        	}
	    }
        else{
        	return  ['product' => $prod ];
        }
    }

}
