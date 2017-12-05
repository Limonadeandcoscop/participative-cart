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

define('PARTICIPATIVE_CART_DIR', dirname(__FILE__));

require_once PARTICIPATIVE_CART_DIR . '/controllers/ParticipativeCartController.php';

/**
 * The ParticipativeCart plugin.
 * @package Omeka\Plugins\ParticipativeCart
 */

/**
 * The ParticipativeCart plugin.
 * @package Omeka\Plugins\ParticipativeCart
 */
class ParticipativeCartPlugin extends Omeka_Plugin_AbstractPlugin
{
    /**
     * @var array Hooks for the plugin.
     */
    protected $_hooks = array(
    );

    /**
     * @var array Filters for the plugin.
     */
    protected $_filters = array(
    );

}




