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

    /**
     * @var User The current user
     */
    protected $_user;

    public function init() {

        $this->_helper->db->setDefaultModelName('ParticipativeCart');

        // Disable view rendering
        $this->_helper->viewRenderer->setNoRender(true);

        // Redirect to homepage if the user is not logged in
        if(!$this->_user = current_user()) {
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

        if (!$this->_request->isXmlHttpRequest()) return;

        $this->getResponse()->setHeader('Content-Type', 'application/json');

        $json = array();

        if (!($name = $this->getParam('name'))) {
            $json['error'] = "The name is required";
            echo json_encode($json); // Returns JSON {"error":"The name is required"}
            return;
        }

        $cart = new ParticipativeCart();

        $cart->user_id  = $this->_user->id;
        $cart->order    = $cart::getNextOrder($this->_user);
        $cart->name     = $name;
        $cart->status   = $cart::CART_STATUS_WAITING;
        $cart->save();

        $json['status']  = 'ok';
        $json['cart_id'] = $cart->id;

        echo json_encode($json); // Returns JSON like {"status":"ok","cart_id":"15"}
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

