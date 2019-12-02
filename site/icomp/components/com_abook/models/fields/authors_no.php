<?php
/**
 * @version     1.0.0
 * @package     com_phpmysport
 * @copyright   Copyright (C) 2011. All rights reserved.
 * @license     
 * @author      Ugolotti Federica http://www.informatizzati.org
 */
// no direct access
defined('_JEXEC') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldAbauthorslist extends JFormFieldList
{
        /**
         * The form field type.
         *
         * @var         string
         * @since       1.6
         */
        protected $type = 'abauthorslist';

        /**
         * Method to get the field options.
         *
         * @return      array   The field option objects.
         * @since       1.6
         */
        public function getOptions()
        {
                // Initialize variables.
                $options = array();

              	$db = JFactory::getDBO(); 
                $query  = $db->getQuery(true);

		$query->select('CONCAT(a.lastname, " ", a.name) AS text, a.id AS value');
                $query->from('#__abauthor AS a');
		$query->join('#__abbookauth AS ba ON ba.idauth=b.id');
		$query->join('#__abbook AS b ON b.id=ba.idbook');
		$query->join('#__abcategories AS c ON c.id=b.catid');
		$query->where();
                $query->order('weather_name');

                // Get the options.
                $db->setQuery($query);

                $options = $db->loadObjectList();

                // Check for a database error.
                if ($db->getErrorNum()) {
                        JError::raiseWarning(500, $db->getErrorMsg());
                }

                array_unshift($options, JHtml::_('select.option', '0', JText::_('COM_PHPMYSPORT_SELECT')));


                return $options;
        }
}
