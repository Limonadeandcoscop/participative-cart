<?php
/**
 * ParticipativeCartPlugin
 *
 * Enable advanced cart functionality for Omeka items
 *
 * @copyright Copyright 2017-2020 Limonade and Co
 * @author Franck Dupont <technique@limonadeandco.fr>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package ParticipativeCartPlugin
 */


class ParticipativeCart_ParticipativeCartController extends Omeka_Controller_AbstractActionController {

    public function init() {

        $this->_helper->db->setDefaultModelName('ParticipativeCart');

        // Disable view rendering
        $this->_helper->viewRenderer->setNoRender(true);

        // Redirect to homepage if the user is not logged in
        if(!current_user()) {
            $this->redirect($_SERVER['HTTP_REFERER']);
        }
    }


    /**
     * Show all carts
     *
     * @return HTML
     */
    public function indexAction() {

        echo "View call carts page";
    }


    /**
     * Show a cart
     *
     * @param Integer $cart-id The ID of the cart
     * @return HTML
     */
    public function viewCartAction() {

        echo "View cart #".$this->getParam('cart-id');
    }


    /**
     * Add a cart
     *
     * @return JSON
     */
    public function addCartAction() {

        echo "Add a cart page";
    }


    /**
     * Delete a cart
     *
     * @param Integer $cart-id The ID of the cart
     * @return JSON
     */
    public function deleteCartAction() {

        echo "Delete the cart #".$this->getParam('cart-id');
    }


    /**
     * Add an item to a cart
     *
     * @param Integer $cart-id The ID of the cart
     * @param Integer $item-id The ID of the item
     * @return JSON
     */
    public function addItemAction() {

        echo "Add item #".$this->getParam('item-id')." to cart #".$this->getParam('cart-id');
    }


    /**
     * Delete an item from a cart
     *
     * @param Integer $cart-id The ID of the cart
     * @param Integer $item-id The ID of the item
     * @return JSON
     */
    public function deleteItemAction() {

        echo "Delete item #".$this->getParam('item-id')." from cart #".$this->getParam('cart-id');
    }
}

