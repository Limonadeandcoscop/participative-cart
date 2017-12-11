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

    /**
     * @var User The ParticipativeCart model table
     */
    protected $_tableCart;

    /**
     * @var User The ParticipativeCartItem model table
     */
    protected $_tableCartItem;

    public function init() {

        $this->_helper->db->setDefaultModelName('ParticipativeCart');

        // Redirect to homepage if the user is not logged in
        if(!$this->_user = current_user()) {
            $this->redirect($_SERVER['HTTP_REFERER']);
        }

        // Instanciate tables models
        $this->_tableCart       = $this->_helper->db->getTable('ParticipativeCart');
        $this->_tableCartItem   = $this->_helper->db->getTable('ParticipativeCartItem');
    }


    /**
     * Show all carts of user
     *
     * @return HTML
     */
    public function indexAction() {

        $userCarts = $this->_tableCart->getuserCarts('with_items');
        $this->view->userCarts = $userCarts;
    }


    /**
     * Show a cart
     *
     * @param Integer (Ajax) $cart-id The ID of the cart
     * @return HTML
     */
    public function viewCartAction() {

        if (!($cart_id = $this->getParam('cart-id'))) {
            throw new Exception("The cart ID is required");
        }

        $cart = get_record_by_id("ParticipativeCart", $cart_id);

        $this->view->cart =  $cart;
        $this->view->items  = $cart->getItems();
        $this->view->count  = count($cart->getItems());
        $this->view->s      = $this->view->count>1 ? 's' : '';
    }


    /**
     * Add a cart
     *
     * @return JSON
     */
    public function addCartAction() {

        // Disable view rendering
        $this->_helper->viewRenderer->setNoRender(true);

        if (!$this->_request->isXmlHttpRequest()) return;

        $this->getResponse()->setHeader('Content-Type', 'application/json');

        $json = array();

        if (!($name = $this->getParam('name'))) {
            $json['error'] = "The name is required";
            die(json_encode($json));
        }

        $cart = new ParticipativeCart();

        if ($cart::cartNameExistForUser($name)) {
            $json['error'] = "The cart name already exists";
            die(json_encode($json));
        }

        $cart->user_id  = $this->_user->id;
        $cart->order    = $cart::getNextOrder();
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

        // Disable view rendering
        $this->_helper->viewRenderer->setNoRender(true);

        // Check param
        if (!($cart_id = $this->getParam('cart-id'))) {
            throw new Exception("Invalid cart ID");
        }

        // Check cart
        if (!($cart = get_record_by_id("ParticipativeCart", $cart_id))) {
            throw new Exception("Invalid cart");
        }

        $cart->delete();

        $this->_helper->redirector->gotoRoute(array(), 'pc_all_carts');
    }


    /**
     * Add an item to a cart
     *
     * @param Integer (Ajax) $cart-id The ID of the cart
     * @param Integer (Ajax) $item-id The ID of the item
     * @return JSON
     */
    public function addItemAction() {

        // Disable view rendering
        $this->_helper->viewRenderer->setNoRender(true);

        if (!$this->_request->isXmlHttpRequest()) return;

        $this->getResponse()->setHeader('Content-Type', 'application/json');

        $json = array();

        // Check params
        if (!($item_id = $this->getParam('item-id')) || !($cart_id = $this->getParam('cart-id'))) {
            $json['error'] = __("Too few parameters to add item ins the cart");
        }

        // Check item
        if (!($item = get_record_by_id("Item", $item_id))) {
            $json['error'] = __("Invalid item ID");
        }

        // Check cart
        if (!($cart = get_record_by_id("ParticipativeCart", $cart_id))) {
            $json['error'] = __("Invalid cart ID");
        }

        if ($cart->itemIsInCart($item_id)) {
            $json['error'] = __("The item is already in this cart");
        }

        if (@$json['error']) {
            die(json_encode($json));
        }

        $cartItem = new ParticipativeCartItem();
        $cartItem->item_id = $item_id;
        $cartItem->cart_id = $cart_id;
        $cartItem->save();

        $json['status']  = 'ok';
        echo json_encode($json); // Returns JSON like {"status":"ok"}
    }


    /**
     * Delete an item from a cart
     *
     * @param Integer $cart-id The ID of the cart
     * @param Integer $item-id The ID of the item
     * @return JSON
     */
    public function deleteItemAction() {

        // Disable view rendering
        $this->_helper->viewRenderer->setNoRender(true);

        // Check params
        if (!($item_id = $this->getParam('item-id')) || !($cart_id = $this->getParam('cart-id'))) {
            throw new Exception("Too few parameters to add item ins the cart");
        }

        // Check item
        if (!($item = get_record_by_id("Item", $item_id))) {
            throw new Exception("Invalid item ID");
        }

        // Check cart
        if (!($cart = get_record_by_id("ParticipativeCart", $cart_id))) {
            throw new Exception("Invalid cart ID");
        }

        // Check that item is in the cart
        $cartItem = $this->_tableCartItem->findBy(array('cart_id' => $cart_id, 'item_id' => $item_id));
        if (!$cartItem) {
            throw new Exception("This item isn't in the cart");
        }

        $cartItem[0]->delete();

        $this->_helper->redirector->gotoRoute(array('cart-id' => $cart_id), 'pc_view_cart');
    }
}

