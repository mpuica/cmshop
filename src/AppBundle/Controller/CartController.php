<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Services\CartService;

/**
 * Class CartController
 * @package AppBundle\Controller
 */
class CartController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $cartService = $this->get('cart_service');
        $utils = $this->container->getParameter('utils');

        return $this->render('AppBundle:cart:index.html.twig', array('items' => $cartService->getItems(), 'utils' => $utils, 'totals' => $cartService->calculatePrices()));
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function addAction(Request $request, $id)
    {
        $isAjax = $request->isXMLHttpRequest();
        if ($isAjax) {

            $cartService = $this->get('cart_service');
            $cartService->addItem($id);

            $response = array("count" => $cartService->cartCount());
            return new Response(json_encode($response));
        }
        return new Response('This is not ajax!', 400);
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function removeAction(Request $request, $id)
    {
        $isAjax = $request->isXMLHttpRequest();
        if ($isAjax) {

            $cartService = $this->get('cart_service');
            $cartService->removeItem($id);

            $response = array("count" => $cartService->cartCount());
            return new Response(json_encode($response));
        }
        return new Response('This is not ajax!', 400);
    }

    /**
     * @param Request $request
     * @param $id
     * @param $qty
     * @return Response
     */
    public function qtyAction(Request $request, $id, $qty){

        $isAjax = $request->isXMLHttpRequest();
        if ($isAjax) {

            $cartService = $this->get('cart_service');
            $cartService->changeQty($id, $qty);

            $response = array("count" => $cartService->cartCount());
            return new Response(json_encode($response));
        }
        return new Response('This is not ajax!', 400);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function countAction(Request $request)
    {
        $isAjax = $request->isXMLHttpRequest();
        if ($isAjax) {

            $cartService = $this->get('cart_service');

            $response = array("count" => $cartService->cartCount());
            return new Response(json_encode($response));
        }
        return new Response('This is not ajax!', 400);
    }
}
