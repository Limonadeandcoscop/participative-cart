<?php
/**
 * ParticipativeCartPlugin
 *
 * @copyright Copyright 2017-2020 Limonade and Co
 * @author Franck Dupont <technique@limonadeandco.fr>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package ParticipativeCartPlugin
 */

/**
 * The ParticipativeCart table.
 *
 * @package Omeka\Plugins\ParticipativeCart
 */
class Table_ParticipativeCart extends Omeka_Db_Table
{

	/**
     * Get the cart of current user
     *
     * @return Array of ParticipativeCart objects
     */
    public static function getUserCarts() {

        $user = current_user();
        $carts = get_db()->getTable('ParticipativeCart')->findBy(array('user_id' => $user->id));
        return $carts;
    }


    /**
     * Get the cart containing a given item
     *
     * @param Integer $item_id The item ID
     * @param Boolean only_keys Returns only carts ID ?
     * @return Array of ParticipativeCart objects | array of integer (cart IDs)
     */
    public static function getCartsOfItem($item_id, $only_keys = false) {

        $carts = get_db()->getTable('ParticipativeCartItem')->findBy(array('item_id' => $item_id));

        if (!$only_keys)
            return $carts;

        $keys = array();
        foreach($carts as $cart) {
            $keys[] = $cart->cart_id;
        }
        return $keys;

    }

    /**
     * Get the cart containing a given item
     *
     * @param Integer $item_id The item ID
     * @param Integer $cart_id The cart ID
     * @return ParticipativeCart object | false
     */
    public static function itemIsInCart($item_id, $cart_id) {

        $carts = self::getCartsOfItem($item_id);
        foreach ($carts as $cart) {
            if ($cart->cart_id == $cart_id)
                return $cart;
        }
        return false;
    }


}
