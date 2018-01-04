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
class ParticipativeCartItem extends Omeka_Record_AbstractRecord
{
    public $cart_id;
    public $item_id;
    public $user_id;
    public $inserted;

    /**
     * @todo Delete notes and comments
     */
	protected function beforeDelete() {
	}


    /**
     * Get notes of the item
     *
     * @return Array of ParticipativeCartItemNotes objects
     */
    public function getNotes() {

		$params['cart_item_id']  = $this->id;
        $params['sort_field']   = 'order';
        $params['sort_dir']     = 'a';

        $table    = get_db()->getTable('ParticipativeCartItemNote');
        $notes    = $table->findBy($params);

        if (count($notes)) {

            $table = get_db()->getTable('ParticipativeCartItemComment');
            foreach($notes as $note) {
                $note->comments = $note->getComments();
            }
        }
        return $notes;
    }


    /**
     * Returns the "Cart" object for this item
     *
     * @return Cart object
     */
    public function getCart() {

        $cart = get_record_by_id('ParticipativeCart', $this->cart_id);
        if (get_class($cart) != 'ParticipativeCart')
            throw new Exception("Invalid cart");
        return $cart;
    }


    /**
     * Returns the "User" object for this item (the user witch added the item in the cart)
     *
     * @return User object
     */
    public function getUser() {

        $user = get_record_by_id('User', $this->user_id);
        if (get_class($user) != 'User')
            throw new Exception("Invalid user");
        return $user;
    }


	/**
     * Get the next order of note for this item
     *
     * @return Integer The next order
     */
    public function getNextOrder() {

        $notes = $this->getNotes();

        if (!$notes) return 1;

        return end($notes)->order + 1;
    }


}
