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

    public function init() {

    }

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

        // Get tags in $tags varaible and Unset tags params
        if (isset($params['tags'])) {
            $tags = array_map('intval', explode(',', $params['tags']));
            $tags = array_filter($tags);
            unset($params['tags']);
        }

        // Retrieve viewable carts
        $table 	    = $this->_helper->db->getTable('ParticipativeCart');
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
        @$start      = $params['page'] == 1 ? 0 : $params['page'];
        $end        = $start + $perPage;
        $pageCarts  = array_slice($carts, $start, $perPage);

        // Enable pagination
        Zend_Registry::set('pagination', array(
            'page' => $this->getParam('page', 1),
            'per_page' => $perPage,
            'total_results' => count($carts),
        ));

        // Retrieve informations for facets
        foreach($allCarts as $key => $cart) {
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
        $this->view->refinements    = $refinements;
        $this->view->params         = $this->getAllParams();
        $this->view->original_uri   = $_SESSION['orginal_uri'];
    }

}

