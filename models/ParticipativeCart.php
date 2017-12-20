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
    public $tags;
    public $status;
    public $inserted;


    // Status constants for a cart
    const CART_STATUS_PRIVATE   = 'private';
    const CART_STATUS_PUBLIC    = 'public';

    // Status constants for a request
    const REQUEST_STATUS_WAITING   = 'waiting';
    const REQUEST_STATUS_ACCEPTED  = 'accepted';

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
     * Returns the "User" object for this cart
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
     * Get items in the current cart
     *
     * @param $return_items_objects If true return "Item" objects, otherwise return "ParticipativeCartItem" objects
     * @return Item|ParticipativeCartItem objects
     */
    public function getItems($return_items_objects = true) {

        $itemsOfCart = get_db()->getTable('ParticipativeCartItem')->findBy(array('cart_id' => $this->id));

        if (!$return_items_objects)
            return $itemsOfCart;

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


    /**
     * Before delete a cart, delete items in the cart
     */
    public function empty() {
        $this->beforeDelete();
    }


    /**
     * Before delete a cart, delete items in the cart
     */
    protected function beforeDelete() {

        $items = $this->getItems(false);
        foreach ($items as $item) {
            $item->delete();
        }
    }


    /**
     * Check if a cart name already exists for a user
     *
     * @return Boolean
     */
    public static function cartNameExistForUser($name) {

        if (!strlen(trim($name))) return false;

        $user = current_user();

        $table = get_db()->getTable('ParticipativeCart');
        $results = $table->findBy(array('user_id' => $user->id, 'name' => $name));

        if ($results) return true;
    }

    /**
     * Quote a cart value, for example returns '&quot;My Cart&quot;' for 'My Cart'
     *
     * @return String
     */
    public function quote($value) {

        if (!isset($this->$value))
            throw new Exception("Invalid value to quote");

        return '&quot;'.$this->$value.'&quot;';
    }


    /**
     * Before save the cart, insert new tags (added from the combobox)
     * and replace the results of the combo by tags IDs
     *
     * @return Array $args
     */
    protected function beforeSave($args) {

        $tagsArray  = array_unique(explode(',', $this->tags));

        $tagsIds = '';

        foreach($tagsArray as $tag) {
            if (strlen(trim($tag)) && !is_numeric($tag)) {
                $tagObject = new ParticipativeCartTag;
                $tagObject->name = $tag;
                $tagObject->save();
                $tag = $tagObject->id;
            }
            $tagsIds .= $tag . ',';
        }
        $this->tags = $tagsIds;
    }


    /**
     * Get tags for current cart
     *
     * @return Array of ParticipativeCartTag objects
     */
    public function getCartTags() {

        if (!strlen(trim($this->tags))) return;

        $tagsArray  = explode(',', $this->tags);
        $table      = get_db()->getTable('ParticipativeCartTag');
        $results    = $table->findBy(array('id' => $tagsArray));
        return $results;
    }


    /**
     * Check if a cart has a given tag
     * If an array is given, returns TRUE if the cart contains all the values $tags_id
     *
     * @param Integer|Array $tags_id The tag ID or an array of tags IDs
     * @return Boolean
     */
    public function hasTag($tags_id) {

        if (!($cartTags = $this->getCartTags())) return false;

        $ids = array_map(function($r) {
            return $r['id'];
        }, $cartTags);


        if (!is_array($tags_id))
            return in_array($tags_id, $ids);

        $intersect = array_intersect($ids, $tags_id);

        if (count($intersect) == count($tags_id))
        // if (count($intersect)>0)
            return true;

        return false;
    }


    /**
     * Display tags in HTML
     *
     * @param String $html_tag The tag for surround tag values
     * @param String $class The class for each tag value
     * @return HTML
     */
    public function displayTags($html_tag = 'span', $class = 'tag') {

        if (!($tags = $this->getCartTags())) return;

        $html = '';
        foreach ($tags as $tag) {
            $html .= '<'.$html_tag.' class="'.$class.'">' . $tag->name . '</'.$html_tag.'>';
        }
        return $html;
    }


    /**
     * Save the notes of a cart
     *
     * @param Array $notes array of notes (strings)
     */
    public function saveCartNotes($notes) {

        if (!is_array($notes))
            throw new Exception("Invalid notes array");

        $table      = get_db()->getTable('ParticipativeCartNote');
        $results    = $table->findBy(array('cart_id' => $this->id));

        // Deleting old notes
        foreach($results as $result) {
            $result->delete();
        }

        // Adding notes
        foreach($notes as $note) {
            if (strlen(trim($note))) {
                $noteObject = new ParticipativeCartNote;
                $noteObject->cart_id = $this->id;
                $noteObject->note    = $note;
                $noteObject->save();
            }
        }
    }


    /**
     * Get notes for current cart
     *
     * @return Array of ParticipativeCartNote objects
     */
    public function getCartNotes() {

        $table      = get_db()->getTable('ParticipativeCartNote');
        $results    = $table->findBy(array('cart_id' => $this->id));
        return $results;
    }

}
