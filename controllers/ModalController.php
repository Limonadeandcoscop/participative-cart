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

class ParticipativeCart_ModalController extends Omeka_Controller_AbstractActionController {

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

    /**
     * @var User The ParticipativeCartTag model table
     */
    protected $_tableCartTag;

    public function init() {

        $this->_helper->db->setDefaultModelName('ParticipativeCart');

        // Redirect to homepage if the user is not logged in
        if(!$this->_user = current_user()) {
            $this->redirect($_SERVER['HTTP_REFERER']);
        }

        // Instanciate tables models
        $this->_tableCart       = $this->_helper->db->getTable('ParticipativeCart');
        $this->_tableCartItem   = $this->_helper->db->getTable('ParticipativeCartItem');
        $this->_tableCartTag    = $this->_helper->db->getTable('ParticipativeCartTag');
    }

    /**
     * Modal add item
     * 
     * @param Integer (Ajax) $item-id The ID of the item
     * 
     * @return HTML
     */
    public function addItemAction() {
        $itemId = $this->getParam('item-id');
        $userCarts = $this->_tableCart->getUserCarts();
        $sharedCarts = $this->_tableCart->getSharedCarts(null, 'only_editables');
        $carts = array_merge($userCarts, $sharedCarts);

        $this->view->carts = $carts;
        $this->view->item_id = $itemId;
        
        foreach ($carts as $cart) {
            if ($cart->itemIsInCart($itemId)) {
              $cart->contain_item = true;
            }
          }
    }

    /**
     * Modal add item confirmation
     * 
     * @return HTML
     */
    public function addItemConfirmationAction() {

    }

    /**
     * Modal delete item
     * 
     * @param Integer $cart-id The ID of the cart
     * @param Integer $item-id The ID of the item
     * 
     * @return HTML
     */
    public function deleteItemAction() {
        $this->view->cart_id = $this->getParam('cart-id');
        $this->view->item_id = $this->getParam('item-id');
    }

    /**
     * Modal delete item confirmation
     * 
     * @return HTML
     */
    public function deleteItemConfirmationAction() {

    }

    /**
     * Modal create cart
     * 
     * @param Integer $item-id The ID of the item
     * @return HTML
     */
    public function createCartAction() {
        $this->view->item_id = $this->getParam('item-id');
        $this->view->tags = $this->_tableCartTag->findAll();
    }

    /**
     * Modal create cart confirmation
     * 
     * @return HTML
     */
    public function createCartConfirmationAction() {

    }

    /**
     * Modal delete cart
     * 
     * @param Integer $cart-id The ID of the cart
     * @return HTML
     */
    public function deleteCartAction() {
        $this->view->cart_id = $this->getParam('cart-id');
    }

    /**
     * Modal delete cart confirmation
     * 
     * @return HTML
     */
    public function deleteCartConfirmationAction() {

    }

    /**
     * Modal delete comment
     * 
     * @param Integer $comment-id The ID of the cart
     * @return HTML
     */
    public function deleteCommentAction() {
        $this->view->comment_id = $this->getParam('comment-id');
    }

    /**
     * Modal delete note
     * 
     * @param Integer $note-id The ID of the cart
     * @return HTML
     */
    public function deleteNoteAction() {
        $this->view->note_id = $this->getParam('note-id');
    }

    /**
     * Modal delete request
     * 
     * @param Integer $request-id The ID of the request
     * @return HTML
     */
    public function deleteRequestAction() {
        $this->view->request_id = $this->getParam('request-id');
    }

    /**
     * Modal empty cart
     * 
     * @return HTML
     */
    public function emptyCartAction() {
        $this->view->cart_id = $this->getParam('cart-id');
    }

    /**
     * Modal empty cart confirmation
     * 
     * @param Integer $cart-id The ID of the cart
     * @return HTML
     */
    public function emptyCartConfirmationAction() {

    }

    /**
     * Modal request confirmation
     * 
     * @return HTML
     */
    public function requestConfirmationAction() {

    }

    /**
     * Modal suspend request
     * 
     * @param Integer $request-id The ID of the request
     * @return HTML
     */
    public function suspendRequestAction() {
        $this->view->request_id = $this->getParam('request-id');
    }

}

