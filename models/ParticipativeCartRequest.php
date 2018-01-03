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
 * A ParticipativeCartRequest row.
 *
 * @package Omeka\Plugins\ParticipativeCart
 */
class ParticipativeCartRequest extends Omeka_Record_AbstractRecord
{
    public $cart_id;
    public $user_id;
    public $status;
    public $rights;
    public $requested;
    public $accepted;

    // Constants for rights
    const VIEW_ITEMS                    = 'view_items';
    const VIEW_ITEMS_NOTES              = 'view_items_notes';
    const VIEW_ITEMS_NOTES_COMMENTS     = 'view_itemsçcomments';
    const ADD_ITEMS_NOTES               = 'add_items_notes';
    const ADD_ITEMS_NOTES_COMMENTS      = 'add_items_notes_comments';
    const DELETE_ITEMS_NOTES            = 'delete_items_notes';
    const DELETE_ITEMS_NOTES_COMMENTS   = 'delete_items_notes_comments';


    /**
     * In order to validate a request, ensure that a request a the user doesn't exists for the cart
     */
    protected function _validate()
    {
        if ($this->id) return true;

        $res = $this->getTable()->findBy(array('user_id' => $this->user_id, 'cart_id' => $this->cart_id));
        if (count($res)) {
        	 throw new Exception("This user has already sended a request for this cart");
        }
    }


    /**
     * Returns the "User" object for this request
     *
     * @return User object
     */
    public function getUser() {

        $user = get_record_by_id('User', $this->user_id);
        if (get_class($user) != 'User')
            throw new Exception("Invalid user ID");

        // Retrieve additionnal infos from GuestUser plugin
        if (plugin_is_active("GuestUser") && class_exists('GuestUserInfo')) {
            $userInfos = get_db()->getTable("GuestUserInfo")->findBy(array('user_id' => $user->id));
            if ($userInfos) {
                $user->gender       = $userInfos[0]->gender;
                $user->profession   = $userInfos[0]->profession;
                $user->institution  = $userInfos[0]->institution;
            }
        }
        return $user;
    }


    /**
     * Returns the "Cart" object for this request
     *
     * @return ParticipativeCart object
     */
    public function getCart() {

        $cart = get_record_by_id('ParticipativeCart', $this->cart_id);
        if (get_class($cart) != 'ParticipativeCart')
            throw new Exception("Invalid cart ID");
        return $cart;
    }


    /**
     * Returns rights level of the request
     *
     * @return Integer The level
     */
    public function getRightsLevel()
    {
        if (!$this->rights) return;

        $level[self::VIEW_ITEMS]                  = 1;
        $level[self::VIEW_ITEMS_NOTES]            = 2;
        $level[self::VIEW_ITEMS_NOTES_COMMENTS]   = 3;
        $level[self::ADD_ITEMS_NOTES]             = 4;
        $level[self::ADD_ITEMS_NOTES_COMMENTS]    = 5;
        $level[self::DELETE_ITEMS_NOTES]          = 6;
        $level[self::DELETE_ITEMS_NOTES_COMMENTS] = 7;
        return $level[$this->rights];
    }


    /**
     * Check if the user can see the notes
     *
     * @return Bolaean
     */
    public function userCanViewNotes() {

        if($this->getRightsLevel() >= 2)
            return true;
        return false;
    }


    /**
     * Check if the user can see the comment
     *
     * @return Bolaean
     */
    public function userCanViewComments() {

        if($this->getRightsLevel() >= 3)
            return true;
        return false;
    }


    /**
     * Check if the user can add item to cart
     *
     * @return Bolaean
     */
    public function userCanAddItemToCart() {

        if($this->getRightsLevel() >= 4)
            return true;
        return false;
    }


    /**
     * Check if the user can add item to cart
     *
     * @return Bolaean
     */
    public function userCanAddCommentsToCart() {

        if($this->getRightsLevel() >= 5)
            return true;
        return false;
    }

}

