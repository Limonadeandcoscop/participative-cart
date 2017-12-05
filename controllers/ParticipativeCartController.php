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


class ParticipativeCart_CartController extends Omeka_Controller_AbstractActionController {

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
     * Index action
     *
     * @return HTML
     */
    public function indexAction() {

    }
}

