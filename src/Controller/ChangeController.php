<?php
// src/Controller/ChnageController.php
namespace App\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ChangeController extends AbstractController
{
		protected $acceptedValues = array(500, 100, 50, 20, 10, 5, 1, .25, .10, .05, .01);

    /**
     * @Route("/make_change", methods={"GET", "POST"})
     */
    public function makeChange(Request $request) {
    		if($request->isMethod('post')) {
		        $totalcost = $request->request->get('totalcost');
		        $amountprovided = $request->request->get('amountprovided');
		        $change = $this->computeChange($totalcost, $amountprovided);

		        return $this->render('user/change.html.twig', [
		        	'page_title' => 'Gimme change',
		        	'change' => $change,
		        	'totalcost' => $totalcost,
		        	'amountprovided' => $amountprovided,
		        ]);    			
    		} else {
		        return $this->render('user/change.html.twig', [
		        	'page_title' => 'Gimme change',
		        ]);    			
    		}
    }
     
		public function computeChange($totalCost, $amountProvided) {
			$response = array();
			$result = $amountProvided - $totalCost;
			if($result > 0) {
				// so we start at the top $100, because:
				// https://en.m.wikipedia.org/wiki/Large_denominations_of_United_States_currency
				// also this CAN be done as a callback function, but a loop for this will work just as well
				for($i = 0; $i < count($this->acceptedValues); $i++) {
					// so this goes through the array from the highest to the lowest and tries to divide the change amount by the value and then moves along the array
					$res = $result / $this->acceptedValues[$i];
					$res = floor ( $res );
					// so I was going to return an array of all of the possible values with 0 for the places that didn't have an amount, but the challenge says to return only the amounts to be returned.
					if($res > 0) {
						$result = $result - $res * $this->acceptedValues[$i];
						$response[] = array("amount"=>$res, "value"=>$this->acceptedValues[$i]);
					} else if($result == 0) {
						break;
					}
				}
			} else {
				// This will set response to 0 so that the function will return an error, so you know when dumb stuff was sent to it.
				$response = 0;
			}
			// This can a JSON out, but it wasn't asked for.
			return $response;
		}

}