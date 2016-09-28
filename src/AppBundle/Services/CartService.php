<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Cart;

/**
 * Class CartService
 * @package AppBundle\Services
 */
class CartService
{
    /**
     * @var EntityManager
     */
    protected $em;
    /**
     * @var
     */
    protected $utils;


    /**
     * @param EntityManager $entityManager
     * @param $utils
     */
    public function __construct(EntityManager $entityManager, $utils){
        $this->em = $entityManager;
        $this->utils = $utils;
    }

    /**
     * getItems will return the list of elements in the cart for the existing user
     * @return mixed
     */
    public function getItems(){
        return $this->em->getRepository('AppBundle:Cart')->findByUser($this->utils['user_id']);
    }

    /**
     * cartCount will return the quantity of elements in the cart
     * @return int
     */
    public function cartCount(){
        $total_count = 0;

        $items = $this->getItems();
        foreach($items as $item){
            $total_count = $total_count + $item->getQty();
        }

        return $total_count;
    }

    /**
     * addItem will ad a new quantity or a new item to the cart
     * @param $id
     * @return bool
     */
    public function addItem($id){
        $existing_item = null;

        $product = $this->em->getRepository('AppBundle:Product')->findOneById($id);

        $qb = $this->em->getRepository('AppBundle:Cart')->createQueryBuilder('cart');
        $qb -> select('cart')
            ->where($qb->expr()->andX(
                $qb->expr()->eq('cart.user', ':user'),
                $qb->expr()->eq('cart.product', ':product')
            ))
            ->setParameter(':user', $this->utils['user_id'])
            ->setParameter(':product', $id);
        $existing_item = $qb->getQuery()->getResult();
        $item = new Cart();
        $item->setProduct($product);
        $item->setUser($this->utils['user_id']);

        if($existing_item){
            $item->setId($existing_item[0]->getId());
            $item->setQty($existing_item[0]->getQty()+1);
            $this->em->merge($item);
        }
        else{
            $item->setQty(1);
            $this->em->persist($item);
        }
        $this->em->flush();

        return true;
    }

    /**
     * removeItem will remove all the quantity of the specified element from the cart
     * @param $id
     * @return bool
     */
    public function removeItem($id){
        $item = $this->em->getRepository('AppBundle:Cart')->findOneById($id);

        $this->em->remove($item);
        $this->em->flush();

        return true;

    }

    /**
     * changeQty will change the quantity ordered for the particular element
     * @param $id
     * @param $qty
     * @return bool
     */
    public function changeQty($id, $qty){
        $item = $this->em->getRepository('AppBundle:Cart')->findOneById($id);

        $item->setQty($qty);
        $this->em->persist($item);
        $this->em->flush();

        return true;
    }

    /**
     * calculatePrices will update the taxes and the totals for the cart
     * @return array
     */
    public function calculatePrices(){
        $totals = [];

        $totals['subtotal'] = 0;
        $totals['total_gst'] = 0;
        $totals['total_qst'] = 0;
        $totals['total'] = 0;

        $items = $this->getItems();
        //calculate prices
        foreach($items as $item){
            $totals['subtotal'] += $item->getQty()*$item->getProduct()->getPrice();
            $item->setTotal($item->getQty()*$item->getProduct()->getPrice());
        }

        $totals['total_gst'] = $totals['subtotal']*$this->utils['gst']/100;
        $totals['total_qst'] = $totals['subtotal']*$this->utils['qst']/100;

        $totals['total'] = $totals['subtotal'] + $totals['total_gst'] + $totals['total_qst'];

        return $totals;
    }

}