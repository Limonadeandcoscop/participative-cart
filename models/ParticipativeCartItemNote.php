<?php
/**
 * ParticipativeCartItemNote
 *
 * @copyright Copyright 2017-2020 Limonade and Co
 * @author Franck Dupont <technique@limonadeandco.fr>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package ParticipativeCartPlugin
 */


/**
 * A ParticipativeCartItemNote row.
 *
 * @package Omeka\Plugins\ParticipativeCart
 */
class ParticipativeCartItemNote extends Omeka_Record_AbstractRecord
{
	public $cart_item_id;
	public $user_id;
	public $order;
    public $note;
    public $inserted;
    public $updated;

    private $_comments;
    private $_hierarchy;

    /**
     * Returns the "User" object for this comment
     *
     * @return User object
     */
    public function getUser() {

        $user = get_record_by_id('User', $this->user_id);
        if (get_class($user) != 'User')
            throw new Exception("Invalid user ID");
        return $user;
    }


    /**
     * Returns the "ParticipativeCartItem" object for this note
     *
     * @return ParticipativeCartItem object
     */
    public function getCartItem() {

        $cartItem = get_record_by_id('ParticipativeCartItem', $this->cart_item_id);

        if (get_class($cartItem) != 'ParticipativeCartItem')
            throw new Exception("Invalid cart item");

        return $cartItem;
    }

     /**
     * Get comments of the note sorted in a tree
     *
     * @return Array of ParticipativeCartItemComments objects
     */
    public function getComments() {

		$params['cart_item_note_id']= $this->id;
        $params['sort_field']       = 'inserted';
        $params['sort_dir']         = 'a';

        $table 		= get_db()->getTable('ParticipativeCartItemComment');
        $comments 	= $table->findBy($params);

        $this->_comments = $comments;

        $this->getChildComments();

        return $this->_hierarchy;
    }


     /**
     * Recursive function witch build the comments tree
     * Fill the $_hierarchy variable
     *
     * @return void
     */
    private function getChildComments($comment_id = false) {

        foreach ($this->_comments as $key => $comment) {

            if ($comment->comment_id == $comment_id) {
                $this->_hierarchy[] = $comment;
                unset($this->_comments[$key]);
                $this->getChildComments($comment->id);
            }
        }
    }


    /**
     * Before delete a note, delete comments of this note
     */
    protected function beforeDelete() {

        $comments = $this->getComments();
        foreach ($comments as $comment) {
            $comment->delete();
        }
    }
}
