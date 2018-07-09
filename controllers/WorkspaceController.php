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


class ParticipativeCart_WorkspaceController extends Omeka_Controller_AbstractActionController {


    /**
     * Get carts viewable by the user
     * Activate pagination and sorts
     *
     * @return HTML
     */
    public function indexAction() {

        // Manage facets params
        $params = $this->getAllParams();

        // Pass user_id param for SQL query
        if ($users = @$params['users'])
            $params['user_id'] = explode(',', $users);

        // Get tags in $tags variable and Unset tags params
        if (isset($params['tags'])) {
            $tags = array_map('intval', explode(',', $params['tags']));
            $tags = array_filter($tags);
            unset($params['tags']);
        }

        // Retrieve viewable carts
        $table 	    = $this->_helper->db->getTable('ParticipativeCart');

        // If an item ID is provided, get only the carts with this item commented
        if (isset($params['item-id'])) {
            $cartWithItemAnnoted = ParticipativeCartItemNote::getCartsWithItemAnnoted($params['item-id']);
            $range = implode(',', array_column($cartWithItemAnnoted, 'cart_id'));
            $params['range'] = $range;
            $this->view->item = get_record_by_id('Item', $params['item-id']);
        }    

        $carts      = $table::getViewableCartOfUser($params);
        $allCarts   = $table::getViewableCartOfUser(array());


        // Manage search by tags
        if (isset($tags) && count($tags)) {
            foreach($carts as $key => $cart) {
                if (!$cart->hasTag($tags)) {
                    // echo "Cart #" . $cart->id.' ne contient pas '.print_r($tags,1).' <br>';
                    unset($carts[$key]); // Exclude the cart if it doesn't contains at least one value of $tags array
                }
            }
        }

        // Manage pagination params
        $perPage    = ParticipativeCartPlugin::NB_CARTS_ON_LISTS;
        @$start      = $params['page'] == 1 ? 0 : ($params['page']-1) * ParticipativeCartPlugin::NB_CARTS_ON_LISTS;
        $end        = $start + $perPage;
        $pageCarts  = array_slice($carts, $start, $perPage);

        // Enable pagination
        Zend_Registry::set('pagination', array(
            'page' => $this->getParam('page', 1),
            'per_page' => $perPage,
            'total_results' => count($carts),
        ));

        // Retrieve informations for facets
        foreach($carts as $key => $cart) {

            // Users facet
            $user = $cart->getUser();
            $refinements['users'][$user->id]['name'] = $user->name;
            @$refinements['users'][$user->id]['nb']++;

            // Tags facets
            $cartTags = $cart->getCartTags();
            if ($cartTags) {
                foreach($cartTags as $cartTag) {
                    $refinements['tags'][$cartTag->id]['name'] = $cartTag->name;
                    @$refinements['tags'][$cartTag->id]['nb']++;
                }
            }
        }

        // It's the first call, there's no refinements, store the URL in session
        if (!$this->getParam('refine')) {
            $_SESSION['orginal_uri'] = $_SERVER['REQUEST_URI'];
        }

        $this->view->carts          = $pageCarts;
        $this->view->total_results  = count($carts);
        $this->view->refine         = $this->getParam('users') || $this->getParam('tags') ? $this->getParam('refine') : false;
        $this->view->refinements    = @$refinements;
        $this->view->params         = $this->getAllParams();
        $this->view->original_uri   = $_SESSION['orginal_uri'];

    }


    /**
     * Send request to a cart
     *
     * @return JSON
     */
    public function sendRequestAction() {

        // Disable view rendering
        $this->_helper->viewRenderer->setNoRender(true);

        //if (!$this->_request->isXmlHttpRequest()) return;

        if (!($cart_id = $this->getParam('cart-id'))) {
            throw new Exception("Invalid cart ID");
        }

        if (!($cart = get_record_by_id("ParticipativeCart", $cart_id))) {
            throw new Exception("Invalid cart");
        }

        if (!($user = current_user())) {
            throw new Exception("Invalid user ID");
        }

        if ($user->id == $cart->user_id) {
            throw new Exception("A user can't send a request to on of its own cart");
        }

        if (!($cartUser = get_record_by_id("User", $cart->user_id))) {
            throw new Exception("Invalid cart user");
        }

        if ($cartRequest = $cart->haveSuspendedRequestFromUser()) {
            $cartRequest->status = ParticipativeCart::REQUEST_STATUS_WAITING;
            $cartRequest->save();
        } else {
            $cartRequest = new ParticipativeCartRequest();
            $cartRequest->cart_id   = $cart_id;
            $cartRequest->user_id   = $user->id;
            $cartRequest->status    = ParticipativeCart::REQUEST_STATUS_WAITING;
            $cartRequest->save();
        }


        $url = absolute_url(array('cart-id' => $cart->id), 'pc_view_cart');

        // Send an email to cart owner
        $body  = "Another user would like to access your cart.<br/><br />";
        $body .= "User : \"".$user->name."\"<br/>";
        $body .= "Cart : \"".$cart->name."\"<br/><br />";
        $body .= "You can approuve this request: <br/>";
        $body .= "<a href='".$url."'>View cart</a>";

        $params['to']           = $cartUser->email;
        $params['recipient']    = $cartUser->name;
        $params['subject']      = __("New Request to Access Your Cart");
        $params['body']         = $body;
        send_mail($params);

        $this->getResponse()->setHeader('Content-Type', 'application/json');

        $json = array();

        $json['status']  = 'ok';

        echo json_encode($json); // Returns JSON like {"status":"ok"}
    }


    /**
     * Delete a request
     *
     * @return void
     */
    public function deleteRequestAction() {

        // Disable view rendering
        $this->_helper->viewRenderer->setNoRender(true);

        if (!($request_id = $this->getParam('request-id'))) {
            throw new Exception("Invalid request ID");
        }

        if (!($request = get_record_by_id("ParticipativeCartRequest", $request_id))) {
            throw new Exception("Invalid request");
        }

        $request->status = ParticipativeCart::REQUEST_STATUS_WAITING;
        $request->save();

        $this->_helper->redirector->gotoRoute(array('cart-id' => $request->cart_id), 'pc_members');
    }


    /**
     * Suspend a request
     *
     * @return void
     */
    public function suspendRequestAction() {

        // Disable view rendering
        $this->_helper->viewRenderer->setNoRender(true);

        if (!($request_id = $this->getParam('request-id'))) {
            throw new Exception("Invalid request ID");
        }

        if (!($request = get_record_by_id("ParticipativeCartRequest", $request_id))) {
            throw new Exception("Invalid request");
        }

        $request->status    = ParticipativeCart::REQUEST_STATUS_SUSPENDED;
        $request->save();

        $this->_helper->redirector->gotoRoute(array('cart-id' => $request->cart_id), 'pc_members');
    }


    /**
     * Manage request and rights
     */
    public function membersAction() {

        if (!($cart_id = $this->getParam('cart-id'))) {
            throw new Exception("Invalid cart ID");
        }

        if (!($cart = get_record_by_id("ParticipativeCart", $cart_id))) {
            throw new Exception("Invalid cart");
        }

        if (current_user()->id != $cart->user_id) {
            throw new Exception("The cart #".$cart->id." doesn't belongs to current user");
        }

        if ($this->getRequest()->isPost()) {

            $rights         = $this->getParam('rights');
            $request_id     = $this->getParam('request_id');
            $request_type   = $this->getParam('request_type');

            if (strlen(trim($rights))) {


                // Update request
                if (!($request = get_record_by_id("ParticipativeCartRequest", $request_id))) {
                    throw new Exception("Invalid request");
                }
                $request->status    = ParticipativeCart::REQUEST_STATUS_ACCEPTED;
                $request->rights    = $rights;
                $request->accepted  = date("Y-m-d H:i:s");
                $request->save();

                // Send a mail to requested
                $body  = "A user has accepted your request on a cart.<br/><br />";
                $body .= "User : \"".$cart->getUser()->name."\"<br/>";
                $body .= "Cart : \"".$cart->name."\"<br/><br />";
                $url = absolute_url(array('cart-id' => $cart->id), 'pc_view_cart');
                $body .= "<a href='".$url."'>View cart</a>";
                
                $params['to']           = $request->getUser()->email;
                $params['recipient']    = $request->getUser()->name;
                $params['subject']      = __("Your cart request has been accepted on OpenJerusalem");
                $params['body']         = $body;
                send_mail($params);
            }
        }

        // Check accepted requests without items
        $acceptedRequests = $cart->getAcceptedRequests();
        $table = $this->_helper->db->getTable('ParticipativeCart');

        $this->view->cart               = $cart;
        $this->view->waitingRequests    = $cart->getWaitingRequests();
        $this->view->acceptedRequests   = $acceptedRequests;
        $this->view->suspendedRequests  = $cart->getSuspendedRequests();
    }

}

