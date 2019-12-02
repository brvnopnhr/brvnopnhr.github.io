<?php
/**
 * @package Joomla
 * @subpackage Abook
 * @copyright (C) 2010 Ugolotti Federica
 * @license GNU/GPL, see LICENSE.php
 * Abook is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License 2
 * as published by the Free Software Foundation.

 * Abook is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with Abook; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */
defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldAuthors extends JFormFieldList
{
        /**
         * The form field type.
         *
         * @var         string
         * @since       1.6
         */
        protected $type = 'Authors';

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

                $db             = JFactory::getDbo();
                $query  = $db->getQuery(true);
		$params = JComponentHelper::getParams( 'com_abook' );

		$query->select('id As value');
		if ($params->get('name_display_admin', 0)==1){
                	$query->select('CONCAT(name, " ", lastname) AS text');
                }else{
                	$query->select('CONCAT(lastname, " ", name) AS text');
                }

                $query->from('#__abauthor');
                $query->order('text');

                // Get the options.
                $db->setQuery($query);

                $options = $db->loadObjectList();

                // Check for a database error.
                if ($db->getErrorNum()) {
                        JError::raiseWarning(500, $db->getErrorMsg());
                }

                // Merge any additional options in the XML definition.
                //$options = array_merge(parent::getOptions(), $options);
		if (isset($this->element['show_root'])) {
                	array_unshift($options, JHtml::_('select.option', '0', JText::_('COM_ABOOK_SELECT')));
		}
		// Merge any additional options in the XML definition.
                $options = array_merge(parent::getOptions(), $options);
                return $options;
        }
}
