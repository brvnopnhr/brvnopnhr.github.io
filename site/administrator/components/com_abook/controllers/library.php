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
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controllerform');

class AbookControllerLibrary extends JControllerForm
{
	protected function allowAdd($data = array())
        {
                // Initialise variables.
                $user           = JFactory::getUser();
                $categoryId     = JArrayHelper::getValue($data, 'catid', JRequest::getInt('filter_category_id'), 'int');
                $allow          = null;

                if ($categoryId) {
                        // If the category has been passed in the URL check it.
                        $allow  = $user->authorise('core.create', $this->option.'.category.'.$categoryId);
                }

                if ($allow === null) {
                        // In the absense of better information, revert to the component permissions.
                        return parent::allowAdd($data);
                } else {
                        return $allow;
                }
        }
}
