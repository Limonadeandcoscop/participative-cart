<?php
/**
 * ParticipativeCartItemNote
 *
 * @copyright Copyright 2017-2020 Limonade and Co
 * @author Franck Dupont <technique@limonadeandco.fr>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package ParticipativeCartPlugin
 */


/**
 * A ParticipativeCartItemNote row.
 *
 * @package Omeka\Plugins\ParticipativeCart
 */
class ParticipativeCartItemNote extends Omeka_Record_AbstractRecord
{
	public $cart_item_id;
	public $user_id;
	public $order;
    public $note;
    public $inserted;
    public $updated;
}
