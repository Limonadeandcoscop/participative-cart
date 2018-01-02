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
     * Get comments of the note
     *
     * @return Array of ParticipativeCartItemComments objects
     */
    public function getComments() {

		$params['cart_item_note_id']  = $this->id;
        $params['sort_field']   = 'inserted';
        $params['sort_dir']     = 'a';

        $table 		= get_db()->getTable('ParticipativeCartItemComment');
        $comments 	= $table->findBy($params);

        return $comments;
    }
}
