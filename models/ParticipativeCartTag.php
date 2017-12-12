<?php
/**
 * ParticipativeCartTag
 *
 * @copyright Copyright 2017-2020 Limonade and Co
 * @author Franck Dupont <technique@limonadeandco.fr>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package ParticipativeCartPlugin
 */


/**
 * A ParticipativeCartTag row.
 *
 * @package Omeka\Plugins\ParticipativeCart
 */
class ParticipativeCartTag extends Omeka_Record_AbstractRecord
{
    public $name;

	/**
     * Before save a tag, ensure that it doesn't exists
     *
     * @return Array $args
     */
    protected function beforeSave($args) {

    	$row = $this->getTable()->findBy(array('name' => $this->name));

    	if(count($row)) {
    		throw new Exception("Tag new already exists");
    	}
    }

}
