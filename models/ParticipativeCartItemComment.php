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
 * A ParticipativeCartItemComment row.
 *
 * @package Omeka\Plugins\ParticipativeCart
 */
class ParticipativeCartItemComment extends Omeka_Record_AbstractRecord
{
	public $cart_item_note_id;
	public $comment_id;
	public $user_id;
	public $level;
    public $comment;
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
     * Before delete a comment, deletes all childs
     */
    protected function beforeDelete() {

        // Get and check note
        if (!($note = get_record_by_id("ParticipativeCartItemNote", $this->cart_item_note_id))) {
            throw new Exception("Invalid note");
        }
        $children = $note->getComments($this->id);

        foreach($children as $child) {
            $child->delete();
        }
    }
}
