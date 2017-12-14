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
    *
     * @return HTML
     */
    public function indexAction() {

        $params = $this->getAllParams();
        $table 	= $this->_helper->db->getTable('ParticipativeCart');
        $carts = $table::getViewableCartOfUser($params);

		$totalRecords = $table::getTotalCountViewableCartOfUser($params);

        Zend_Registry::set('pagination', array(
            'page' => $this->getParam('page', 1),
            'per_page' => ParticipativeCartPlugin::NB_CARTS_ON_LISTS,
            'total_results' => $totalRecords,
        ));

        $this->view->carts  = $carts;
        $this->view->total_results  = $totalRecords;
    }

}

