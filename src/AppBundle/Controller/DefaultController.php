<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use AppBundle\Form\CodeBarreType;
use AppBundle\Form\RegistrationType;
use AppBundle\Entity\Product;
use AppBundle\Entity\Evaluation;

use AppBundle\Form\EvaluationType;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template("index.html.twig")
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm(CodeBarreType::class);
        $last_prod = array();
        
        $em = $this->getDoctrine()->getManager();
        $all_last_prod = $em->getRepository('AppBundle:Product')->findBy(array(), array('lastView' => 'DESC'), 8);
        for($i=0; $i<sizeof($all_last_prod) && $i<8; $i++){
            $json_prod[$i] = 'https://fr.openfoodfacts.org/api/v0/produit/'.$all_last_prod[$i]->getBarCode().'.json';
                
            $last_prod[$i] = json_decode(file_get_contents($json_prod[$i]), true);
        }

        $all_products_average = $this->getDoctrine()
            ->getRepository(Evaluation::class)
            ->findByAllAverageProd();

        for($i=0; $i<sizeof($all_products_average) && $i<8; $i++){
            $all_rate_prod = $em->getRepository('AppBundle:Product')->findBy(array('id' => $all_products_average[$i]), array(), 8);
            $json_prod[$i] = 'https://fr.openfoodfacts.org/api/v0/produit/'.$all_rate_prod[0]->getBarCode().'.json';
                
            $rate_prod[$i] = json_decode(file_get_contents($json_prod[$i]), true);
        }

        
        return [
            'form' => $form->createView(), 'last_prod' => $last_prod, 'rate_prod' => $rate_prod
        ];
    }

    /**
     * @Route("/search", name="search")
     * @Template("product.html.twig")
     */
    public function searchAction(Request $request)
    {
        $form_eval = $this->createForm(EvaluationType::class);
        $form = $this->createForm(CodeBarreType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $code_barre = $data['code_barre'];
            $url = 'https://fr.openfoodfacts.org/api/v0/produit/'.$code_barre.'.json';
            $data = json_decode(file_get_contents($url), true);
            if($data['status'] == 1 ){
                $em = $this->getDoctrine()->getManager();

                $exist = $em->getRepository('AppBundle:Product')->findBy(array('barCode' => $data['code']));
                if($exist == null){
                    $new_prod = new Product();
                    $new_prod->setBarCode($data['code']);
                    $new_prod->setNbConsult(1);
                    $new_prod->setLastView(new \DateTime("now"));
                    
                    $em->persist($new_prod);


                    $em->flush();

                    array_push($data, ["nbView" => $new_prod->getNbConsult()]);

                }
                else{
                    $exist = $em->getRepository('AppBundle:Product')->find($exist[0]);
                    $exist->setNbConsult($exist->getNbConsult()+1);
                    $exist->setLastView(new \DateTime("now"));

                    $em->persist($exist);
                    $em->flush();
                    
                    array_push($data, ["nbView" => $exist->getNbConsult()]);

                    $all_product = $em->getRepository('AppBundle:Evaluation')->findBy(array('prod' => $exist->getId()));
        
                    array_push($data, ["allProd" => $all_product]);

                    $products_average = $this->getDoctrine()
                    ->getRepository(Evaluation::class)
                    ->findByAverageProd($exist->getId());

                    if($products_average != null){
                        array_push($data, ["averageProd" => $products_average[0][1]]);
                    }

                    $user = $this->getUser();
                    if(!empty($user) && $user->getId() != null){
                        $database_user = $em->getRepository('AppBundle:User')->findBy(array('id' => $user->getId()));
                        $database_user = $database_user[0];
                        $database_eval = $em->getRepository('AppBundle:Evaluation')->findBy(array('user' => $database_user->getId(), 'prod' => $exist->getId() ));

                        if($database_eval == null){
                            $form = $this->createForm(EvaluationType::class);
                            return  ['form' => $form->createView(), 'product' => $data ];
                        }
                        else{
                            return  ['product' => $data ];
                        }
                    }
                    else{
                        return  ['product' => $data ];
                    }

                }
            }

            // XXX: A faire, chercher si le produit existe, le créer en
            // base et rediriger le visiteur vers la fiche produit 
            
            return  [
                'form' => $form_eval->createView(),
                "product" => $data
            ];

            
        } else {
            return $this->redirectToRoute('homepage');
        }
    }
}
