<?php
/**
 * ParticipativeCartNote
 *
 * @copyright Copyright 2017-2020 Limonade and Co
 * @author Franck Dupont <technique@limonadeandco.fr>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package ParticipativeCartPlugin
 */


/**
 * A ParticipativeCartNote row.
 *
 * @package Omeka\Plugins\ParticipativeCart
 */
class ParticipativeCartNote extends Omeka_Record_AbstractRecord
{
	public $cart_id;
    public $note;
}
