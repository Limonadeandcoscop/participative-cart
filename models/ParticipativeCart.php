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

}
