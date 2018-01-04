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
     * Show all carts of user
     *
     * @return HTML
     */
    public function indexAction() {

        $userCartsPrivate   = $this->_tableCart->getuserCarts('with_items', ParticipativeCart::CART_STATUS_PRIVATE);
        $userCartsPublic    = $this->_tableCart->getuserCarts('with_items', ParticipativeCart::CART_STATUS_PUBLIC);
        $sharedCarts        = $this->_tableCart->getSharedCarts('with_items');

        // Retrieve all tags (for 'create cart' modal)
        $participativeCartTagTable = get_db()->getTable('ParticipativeCartTag');
        $tags = $participativeCartTagTable->findAll();
        if (!$tags) $tags = array();
        $this->view->tags = $tags;

        $this->view->userCartsPrivate   = $userCartsPrivate;
        $this->view->userCartsPublic    = $userCartsPublic;
        $this->view->sharedCarts        = $sharedCarts;
    }


    /**
     * Show a cart
     *
     * @param Integer (Ajax) $cart-id The ID of the cart
     * @return HTML
     */
    public function viewCartAction() {

        // Check cart ID
        if (!($cart_id = $this->getParam('cart-id'))) {
            throw new Exception("The cart ID is required");
        }

        // Check cart
        if (!($cart = get_record_by_id("ParticipativeCart", $cart_id))) {
            throw new Exception("Invalid cart");
        }

        // Check access rights
        if ($cart->getUser()->id != current_user()->id && !$cart->userCanWiewCart()) {
            throw new Exception("You don't have any rights to access this cart");
        }


        $cart = get_record_by_id("ParticipativeCart", $cart_id);
        $cart->notes = $cart->getCartNotes();
        $cart->tags  = $cart->getCartTags();

        // Retrieve items in cart and prepare paginatation
        $this->_helper->db->setDefaultModelName('ParticipativeCartItem');
        $pluralName = $this->view->pluralize($this->_helper->db->getDefaultModelName());
        $params['cart_id'] = $cart->id;
        $recordsPerPage = 2;
        $currentPage    = $this->getParam('page', 1);
        $items_in_cart  = $this->_helper->db->findBy($params, $recordsPerPage, $currentPage);
        $totalRecords   = $this->_helper->db->count($params);

        if ($recordsPerPage) {
            Zend_Registry::set('pagination', array(
                'page' => $currentPage,
                'per_page' => $recordsPerPage,
                'total_results' => $totalRecords,
            ));
        }

        $this->view->items_in_cart  = $items_in_cart;
        $this->view->total_results  = $totalRecords;
        $this->view->cart           = $cart;
        $this->view->count          = count($cart->getItems());
        $this->view->s              = $this->view->count>1 ? 's' : '';
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
        $cart->tags     = $this->getParam('tags');
        $cart->status   = $cart::CART_STATUS_PRIVATE;
        $cart->save();

        $json['status']  = 'ok';
        $json['cart_id'] = $cart->id;

        echo json_encode($json); // Returns JSON like {"status":"ok","cart_id":"15"}
    }


    /**
     * Edit a cart
     *
     * @param Integer $cart-id The ID of the cart
     * @return void
     */
    public function editCartAction() {

        // Check param
        if (!($cart_id = $this->getParam('cart-id'))) {
            throw new Exception("Invalid cart ID");
        }

        // Check cart
        if (!($cart = get_record_by_id("ParticipativeCart", $cart_id))) {
            throw new Exception("Invalid cart");
        }

        if ($this->getRequest()->isPost()) {

            if (!($name = $this->getParam('name'))) {
                throw new Exception("The name of the cart is required");
            }

            $status = ($this->getParam('status') == ParticipativeCart::CART_STATUS_PRIVATE) ? ParticipativeCart::CART_STATUS_PRIVATE : ParticipativeCart::CART_STATUS_PUBLIC;

            $cart->name         = $name;
            $cart->description  = $this->getParam('description');
            $cart->tags         = $this->getParam('tags'); // Tags handling in ParticipativeCart::beforeSave();
            $cart->status       = $status;
            $cart->save();

            if ($notes = $this->getParam('note'))
                $cart->saveCartNotes($notes);

            $this->_helper->redirector->gotoRoute(array('cart-id' => $cart->id), 'pc_view_cart');
        }

        $cart->tags = $cart->getCartTags();
        $this->view->cart       = $cart;
        $this->view->nb_items   = count($cart->getItems());
        $this->view->tags       = $this->_tableCartTag->findAll();

   }


    /**
     * Empty a cart
     *
     * @param Integer $cart-id The ID of the cart
     */
    public function emptyCartAction() {

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

        $cart->empty();

        $this->_helper->redirector->gotoRoute(array('cart-id' => $cart->id), 'pc_view_cart');
    }

    /**
     * Delete a cart
     * - Send an email to users if there's requests on the cart
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

        // TODO : Check that the cart has no notes

        // Send an email to current requests users
        $cartRequests = $cart->getRequests();
        if (count($cartRequests)) {
            foreach ($cartRequests as $request) {
                if (!($user = get_record_by_id("User", $request->user_id))) {
                    throw new Exception("Invalid item ID");
                }
                $this->_deleteCartEmail($cart, $user);
            }
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
        $cartItem->user_id = current_user()->id;
        $cartItem->save();

        $json['status']  = 'ok';
        echo json_encode($json); // Returns JSON like {"status":"ok"}
    }


    /**
     * Print an item of cart
     *
     * @param Integer $cart-id The ID of the cart
     * @param Integer $item-id The ID of the item
     * @return JSON
     */
    public function printItemAction() {

        // Disable view rendering
        $this->_helper->viewRenderer->setNoRender(true);

        echo "Comming soon";
        echo '<br /><br><a href="javascript:history.back();">Back</a>';
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

        // Define cart a private if it containes no items
        if (count($cart->getItems()) == 0) {
            $cart->status = ParticipativeCart::CART_STATUS_PRIVATE;
            $cart->save();
        }

        $this->_helper->redirector->gotoRoute(array('cart-id' => $cart_id), 'pc_view_cart');
    }


    /**
     * Send an email to requester when the user deletes a cart
     *
     * @param ParticipativeCart $cart The cart
     * @param User $requester The requester user
     * @return void
     */
    private function _deleteCartEmail($cart, $requester) {

        $body  = "A user has deleted a cart.<br/><br />";
        $body .= "User : \"".$cart->getUser()->name."\"<br/>";
        $body .= "Cart name : \"".$cart->name."\"<br/><br />";

        $params['to']           = $requester->email;
        $params['recipient']    = $requester->name;
        $params['subject']      = __("A cart has been deleted");
        $params['body']         = $body;
        send_mail($params);
    }


    /**
     * Display an item of cart
     *
     * @return HTML
     */
    public function viewItemAction() {

        // Check cart ID
        if (!($cart_id = $this->getParam('cart-id'))) {
            throw new Exception("The cart ID is required");
        }

        // Check cart
        if (!($cart = get_record_by_id("ParticipativeCart", $cart_id))) {
            throw new Exception("Invalid cart");
        }

        // Check item ID
        if (!($item_id = $this->getParam('item-id'))) {
            throw new Exception("The item ID is required");
        }

        // Check item
        if (!($item = get_record_by_id("Item", $item_id))) {
            throw new Exception("Invalid item");
        }

        // Check access rights
        if ($cart->getUser()->id != current_user()->id && !$cart->userCanWiewCart()) {
            throw new Exception("You don't have any rights to access this cart");
        }

        // Get record
        $cartItem = $this->_tableCartItem->findBy(array('cart_id' => $cart_id, 'item_id' => $item_id));
        if (!($cartItem = $cartItem[0])) {
            throw new Exception("Invalid cart item");
        }

        // Get request
        if ($cart->user_id != current_user()->id) {
            $request = $cart->haveRequestFromUser();
            if (!$request) {
                throw new Exception("User is not allowed to access this item");
            }
        }


        if ($this->getRequest()->isPost()) {

            if (strlen(trim($this->getParam('save-note')))) { // The user has clicked "save the note" button

                if ($note = $this->getParam('note')) {
                    $itemNote = new ParticipativeCartItemNote;
                    $itemNote->cart_item_id = $cartItem->id;
                    $itemNote->user_id = current_user()->id;
                    $itemNote->order = $cartItem->getNextOrder();
                    $itemNote->note = $note;
                    $itemNote->save();
                    $this->_helper->redirector->gotoRoute(array('cart-id' => $cart->id, 'item-id' => $item->id), 'pc_view_item');
                }

            } else { // The user has clicked "save the reply" button

                if ($comment = $this->getParam('comment')) {

                    $level = $this->getParam('level') + 1;

                    $itemComment = new ParticipativeCartItemComment;
                    $itemComment->cart_item_note_id = $this->getParam('note-id');
                    $itemComment->comment_id = $this->getParam('comment-id');
                    $itemComment->user_id = current_user()->id;
                    $itemComment->level = $level;
                    $itemComment->comment = $comment;
                    $itemComment->save();
                    $this->_helper->redirector->gotoRoute(array('cart-id' => $cart->id, 'item-id' => $item->id), 'pc_view_item');
                }
            }

        }

        $this->view->cart       = $cart;
        $this->view->item       = $item;
        $this->view->request    = @$request;
        $this->view->notes      = $cartItem->getNotes();
        $this->view->count      = count($cart->getItems());
        $this->view->s          = $this->view->count>1 ? 's' : '';
    }


    /**
     * Delete a note
     *
     * @param Integer $note-id The ID of the note
     * @return JSON
     */
    public function deleteNoteAction() {

        // Disable view rendering
        $this->_helper->viewRenderer->setNoRender(true);

        // Check params
        if (!($note_id = $this->getParam('note-id'))) {
            throw new Exception("Invalid note ID");
        }

        // Check note
        if (!($note = get_record_by_id("ParticipativeCartItemNote", $note_id))) {
            throw new Exception("Invalid note");
        }

        $cartItem = $note->getCartItem();

        $note->delete();

        $this->_helper->redirector->gotoRoute(array('cart-id' => $cartItem->cart_id, 'item-id' => $cartItem->item_id), 'pc_view_item');
    }


    /**
     * Delete a comment
     *
     * @param Integer $comment-id The ID of the comment
     * @return JSON
     */
    public function deleteCommentAction() {

        // Disable view rendering
        $this->_helper->viewRenderer->setNoRender(true);

        // Check params
        if (!($comment_id = $this->getParam('comment-id'))) {
            throw new Exception("Invalid comment ID");
        }

        // Check comment
        if (!($comment = get_record_by_id("ParticipativeCartItemComment", $comment_id))) {
            throw new Exception("Invalid comment");
        }

        // Check note
        if (!($note = get_record_by_id("ParticipativeCartItemNote", $comment->cart_item_note_id))) {
            throw new Exception("Invalid note");
        }

        $cartItem = $note->getCartItem();

        $comment->delete();

        $this->_helper->redirector->gotoRoute(array('cart-id' => $cartItem->cart_id, 'item-id' => $cartItem->item_id), 'pc_view_item');
    }

}

