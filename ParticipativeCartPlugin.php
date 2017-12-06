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
    protected $_hooks = array('install',
                              'uninstall',
                              'public_head',
                              'define_routes',
                              'public_items_show',
    );

    /**
     * @var array Filters for the plugin.
     */
    protected $_filters = array(
    );


    /**
     * The install process
     */
    public function hookInstall()
    {
        $sql  = "
        CREATE TABLE IF NOT EXISTS `{$this->_db->ParticipativeCart}` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `user_id` int(10) unsigned NOT NULL,
          `order` int(3) unsigned NOT NULL,
          `name` mediumtext NOT NULL,
          `description` text,
          `note` text,
          `public` varchar(20) NOT NULL DEFAULT 'waiting',
          `inserted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          UNIQUE KEY `id` (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
        $this->_db->query($sql);
    }


    /**
     * The uninstall process
     */
    public function hookUninstall()
    {
        $db = get_db();
        $sql = "DROP TABLE IF EXISTS `$db->ParticipativeCart` ";
        $db->query($sql);
    }

    /**
     * Simply include JS & CSS files
     * CSS elements of this can be overloaded by your own CSS
     */
    public function hookPublicHead($args) {

        $args['view']->headlink()->prependStylesheet("https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css"); // Boostrap 4
        echo '<script src="https://npmcdn.com/tether@1.2.4/dist/js/tether.min.js"></script>';
        echo '<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js"></script>';
        echo '<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js"></script>';
        queue_css_file('participative_cart');
        queue_js_file('participative_cart');
    }


    /**
     * Add the routes for accessing cart functionalities
     *
     * @param Zend_Controller_Router_Rewrite $router
     */
    public function hookDefineRoutes($args)
    {
        // Don't add these routes on the admin side to avoid conflicts.
        if (is_admin_theme()) return;

        // Include routes file
        $router = $args['router'];
        $router->addConfig(new Zend_Config_Ini(PARTICIPATIVE_CART_DIR .
        DIRECTORY_SEPARATOR . 'routes.ini', 'routes'));
    }


    /**
     * Hook for items/show page
     */
    public function hookPublicItemsShow()
    {
      echo get_view()->partial('modals/add-to-cart.php');
      echo get_view()->partial('modals/create-cart.php');
    }

}




