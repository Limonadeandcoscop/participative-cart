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
     * @param Boolean $with_items whether or not retrieve items of the cart
     * @param Boolean $status Returns only cart with a given status
     * @return Array of ParticipativeCart objects
     */
    public static function getUserCarts($with_items = false, $status = false) {

        $user = current_user();

        $params['user_id'] = $user->id;

        if ($status)
            $params['status'] = $status;

        $carts = get_db()->getTable('ParticipativeCart')->findBy($params);

        if (!$with_items)
            return $carts;

        foreach($carts as $cart) {
            $cart->items = $cart->getItems();
        }
        return $carts;
    }


    /**
     * Get the cart containing a given item
     *
     * @param Integer $item_id The item ID
     * @param Boolean $only_keys Returns only carts ID ?
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
     * Get carts viewable for current user
     *
     * @param Array $params Search params retrieve from the controller
     * @return Array of ParticipativeCart objects
     */
    public static function getViewableCartOfUser($params) {

        $recordsPerPage = ParticipativeCartPlugin::NB_CARTS_ON_LISTS;
        $currentPage    = @$params['page'];
        $sort_field     = @$params['sort_field'];
        $sort_dir       = @$params['sort_dir'];

        $table = get_db()->getTable('ParticipativeCart');

        // Get only public carts
        $params['status'] = ParticipativeCart::CART_STATUS_PUBLIC;

        $select = $table->getSelectForFindBy($params, $recordsPerPage, $currentPage);

        $user = current_user();
        $select->where('user_id <>'.$user->id);

        // Add specific sorting
        $select->join(array('users' => get_db()->Users), 'participative_carts.user_id = users.id', array());
        if (isset($sort_field) && strpos($sort_field, '.')) {
            $dir = 'ASC';
            if ($sort_dir == 'd')
                $dir = 'DESC';
            $select->order($sort_field.' '.$dir);
        }

        return $table->fetchObjects($select);
    }

}
