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
 * A ParticipativeCartRequest row.
 *
 * @package Omeka\Plugins\ParticipativeCart
 */
class ParticipativeCartRequest extends Omeka_Record_AbstractRecord
{
    public $cart_id;
    public $user_id;
    public $status;
    public $rights;
    public $requested;
    public $accepted;


    /**
     * In order to validate a request, ensure that a request a the user doesn't exists for the cart
     */
    protected function _validate()
    {
        $res = $this->getTable()->findBy(array('user_id' => $this->user_id, 'cart_id' => $this->cart_id));
        if (count($res)) {
        	 throw new Exception("This user has already sended a request for this cart");
        }
    }

}
