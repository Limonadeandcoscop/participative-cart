<?php
/**
 * ParticipativeCart
 *
 * @copyright Copyright 2017-2020 Limonade and Co
 * @author Franck Dupont <technique@limonadeandco.fr>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package ParticipativeCartPlugin
 */


/**
 * A ParticipativeCart row.
 *
 * @package Omeka\Plugins\ParticipativeCart
 */
class ParticipativeCart extends Omeka_Record_AbstractRecord
{
    public $user_id;
    public $order;
    public $name;
    public $description;
    public $status;
    public $inserted;


    // Status constants for a cart
    const CART_STATUS_WAITING   = 'waiting';
    const CART_STATUS_PUBLIC    = 'public';
    const CART_STATUS_PRIVATE   = 'private';


    /**
     * Get the next cart order of current user
     *
     * @return Integer The next order
     */
    public static function getNextOrder() {

        $user = current_user();

        $params['user_id']      = $user->id;
        $params['sort_field']   = 'order';
        $params['sort_dir']     = 'd';

        $carts = get_db()->getTable('ParticipativeCart')->findBy($params);

        if (!$carts) return 1;

        $lastOrder = $carts[0]->order;

        return $lastOrder + 1;
    }


    /**
     * Get items in the current cart
     *
     * @return Item objects
     */
    public function getItems() {

        $itemsOfCart = get_db()->getTable('ParticipativeCartItem')->findBy(array('cart_id' => $this->id));

        $items = array();
        foreach($itemsOfCart as $itemOfCart) {
            $item = get_record_by_id('Item', $itemOfCart->item_id);

            if (get_class($item) == "Item")
                $items[] = get_record_by_id('Item', $itemOfCart->item_id);
        }
        return $items;
    }

    /**
     * Check if an item is in the current cart
     * If the item is in the cart, returns the ParticipativeCart object
     *
     * @param Integer $item_id The item ID
     * @return ParticipativeCart object | false
     */
    public function itemIsInCart($item_id) {

        $table = get_db()->getTable('ParticipativeCart');
        $carts = $table::getCartsOfItem($item_id);
        foreach ($carts as $cart) {
            if ($cart->cart_id == $this->id)
                return $cart;
        }
        return false;
    }

}
