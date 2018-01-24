<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\EvaluationType;
use AppBundle\Entity\Product;
use AppBundle\Entity\Evaluation;

class EvaluationController extends Controller
{
	/**
     * @Route("/Evaluation", name="evaluation")
     * @Template("evaluation.html.twig")
     */
    public function EvaluationAction(Request $request)
    {        
    	$form = $this->createForm(EvaluationType::class);
        $form->handleRequest($request);
        $product = $request->query->get('code_barre');
        $em = $this->getDoctrine()->getManager();
        $database_prod = $em->getRepository('AppBundle:Product')->findBy(array('barCode' => $product));

        if ($form->isSubmitted() && $form->isValid()) {
        	$data = $form->getData();
        	$em = $this->getDoctrine()->getManager();
        	$new_eval = new Evaluation();
            $new_eval->setComment($data['comment']);
            $new_eval->setRate($data['rate']);
            $database_prod = $database_prod[0];
            $new_eval->setProd($database_prod);
            $new_eval->setUser($this->getUser());

            $em->persist($new_eval);
            $em->flush();
        }
    }
}
