<?php
/**
 * ParticipativeCart
 *
 * @copyright Copyright 2017-2020 Limonade and Co
 * @author Franck Dupont <technique@limonadeandco.fr>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package ParticipativeCartPlugin
 */


/**
 * A ParticipativeCart row.
 *
 * @package Omeka\Plugins\ParticipativeCart
 */
class ParticipativeCartItem extends Omeka_Record_AbstractRecord
{
    public $cart_id;
    public $item_id;
    public $inserted;

}
