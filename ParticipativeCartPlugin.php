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
                              'before_delete_item',
    );

    /**
     * @var array Filters for the plugin.
     */
    protected $_filters = array(
                              'public_navigation_admin_bar',
                              'public_navigation_main',
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
          `tags` varchar(255),
          `status` varchar(20) NOT NULL DEFAULT '".ParticipativeCart::CART_STATUS_PRIVATE."',
          `inserted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          UNIQUE KEY `id` (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
        $this->_db->query($sql);

        $sql  = "
        CREATE TABLE IF NOT EXISTS `{$this->_db->ParticipativeCartItems}` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `cart_id` int(10) unsigned NOT NULL,
          `item_id` int(3) unsigned NOT NULL,
          `inserted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          UNIQUE KEY `id` (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
        $this->_db->query($sql);

        $sql  = "
        CREATE TABLE IF NOT EXISTS `{$this->_db->ParticipativeCartTags}` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `name` varchar(50) UNIQUE NOT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `id` (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
        $this->_db->query($sql);

        $sql  = "
        CREATE TABLE IF NOT EXISTS `{$this->_db->ParticipativeCartNotes}` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `cart_id` int(10) unsigned NOT NULL,
          `note` mediumtext NOT NULL,
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
        $sql = "DROP TABLE IF EXISTS `$db->ParticipativeCart`";
        $db->query($sql);
        $sql = "DROP TABLE IF EXISTS `$db->ParticipativeCartItems`";
        $db->query($sql);
        $sql = "DROP TABLE IF EXISTS `$db->ParticipativeCartTags`";
        $db->query($sql);
        $sql = "DROP TABLE IF EXISTS `$db->ParticipativeCartNotes`";
        $db->query($sql);
    }

    /**
     * Simply include JS & CSS files
     * CSS elements of this can be overloaded by your own CSS
     */
    public function hookPublicHead($args) {

        queue_css_file(array('bootstrap.min', 'participative_cart'));
        queue_js_file(array('participative_cart', 'validate.min', 'tether.min', 'transition.min', 'bootstrap.min', 'dropdown.min'));
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
     *
     * - Displays modal to create a new cart and the confirmation modal
     * - Displays modal to add an item in the cart and the confirmation modal
     */
    public function hookPublicItemsShow($args)
    {
      $item = $args['item'];

      $participativeCartTable = get_db()->getTable('ParticipativeCart');
      $carts = $participativeCartTable->getUserCarts();

      foreach ($carts as $cart) {
        if ($cart->itemIsInCart($item->id)) {
          $cart->contain_item = true;
        }
      }

      // Retrieve all tags
      $participativeCartTagTable = get_db()->getTable('ParticipativeCartTag');
      $tags = $participativeCartTagTable->findAll();

      echo get_view()->partial('modals/add-to-cart.php', null, array('carts' => $carts, 'item_id' => $item->id, 'table' => $participativeCartTable));
      echo get_view()->partial('modals/create-cart.php');
      echo get_view()->partial('modals/create-cart-confirmation.php', null, array('item_id' => $item->id, 'redirect_text' => __('Back to cart selection')));
      echo get_view()->partial('modals/add-to-cart-confirmation.php');
    }


    /**
     * Before delete an item, remove it from carts
     *
     */
    public function hookBeforeDeleteItem($args)
    {
      $writer = new Zend_Log_Writer_Stream(LOGS_DIR.'/errors.log');
      $logger = new Zend_Log($writer);

      $item = $args['record'];

      $participativeCartTable = get_db()->getTable('ParticipativeCart');
      $carts = $participativeCartTable::getCartsOfItem($item->id);

      if (count($carts)) {
        foreach ($carts as $cart) {
          $cart->delete();
        }
      }
    }


    /**
     * Add the cart link to the admin bar
     */
    public function filterPublicNavigationAdminBar($navLinks)
    {
        if(!current_user()) {
            return $navLinks;
        }
        $navLinks[1] = array(
            'label'=> __('Your carts'),
            'class' => 'your-carts-link',
            'uri' => url("cart")
        );
        ksort($navLinks);
        return $navLinks;
    }


    /**
     * Add the workspace link to main menu
     */
    public function filterPublicNavigationMain($navLinks)
    {
        $navLinks[] = array(
            'label'=> __('Workspace'),
            'class' => 'workspace-link',
            'uri' => url("workspace")
        );
        return $navLinks;
    }

}




