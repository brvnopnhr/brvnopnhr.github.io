<?php
/**
 * @version             $Id: category.php 16235 2010-04-20 04:13:25Z pasamio $
 * @package             Joomla
 * @subpackage  com_content
 * @copyright   Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license             GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Component Helper
jimport('joomla.application.component.helper');
jimport('joomla.application.categories');

/**
 * Contact Component Category Tree
 *
 * @static
 * @package             Joomla
 * @subpackage  com_contact
 * @since 1.6
 */
class AbookCategories extends JCategories
{
        public function __construct($options = array())
        {
                $options['table'] = '#__abbook';
                $options['extension'] = 'com_abook';
                $options['statefield'] = 'state';
                parent::__construct($options);
        }

        protected function _load($id)
        {
                $db = JFactory::getDbo();
                $app = JFactory::getApplication();
                $user = JFactory::getUser();
                $extension = $this->_extension;
                // Record that has this $id has been checked
                $this->_checkedCategories[$id] = true;

                $query = $db->getQuery(true);

                // Right join with c for category
                $query->select('c.*');
                $case_when = ' CASE WHEN ';
                $case_when .= $query->charLength('c.alias');
                $case_when .= ' THEN ';
                $c_id = $query->castAsChar('c.id');
                $case_when .= $query->concatenate(array($c_id, 'c.alias'), ':');
                $case_when .= ' ELSE ';
                $case_when .= $c_id . ' END as slug';
                $query->select($case_when);

                $query->from('#__abcategories as c');
                $query->where('(c.extension=' . $db->Quote($extension) . ' OR c.extension=' . $db->Quote('system') . ')');

                if ($this->_options['access'])
                {
                        $query->where('c.access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')');
                }

                if ($this->_options['published'] == 1)
                {
                        $query->where('c.published = 1');
                }

                $query->order('c.lft');

                // s for selected id
                if ($id != 'root')
                {
                        // Get the selected category
                        $query->where('s.id=' . (int) $id);
                        if ($app->isSite() && $app->getLanguageFilter())
                        {
                                $query->leftJoin('#__abcategories AS s ON (s.lft < c.lft AND s.rgt > c.rgt AND c.language in (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')) OR (s.lft >= c.lft AND s.rgt <= c.rgt)');
                        }
                        else
                        {
                                $query->leftJoin('#__abcategories AS s ON (s.lft <= c.lft AND s.rgt >= c.rgt) OR (s.lft > c.lft AND s.rgt < c.rgt)');
                        }
                }
                else
                {
                        if ($app->isSite() && $app->getLanguageFilter())
                        {
                                $query->where('c.language in (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')');
				//$query->where('i.language in (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')');
                        }
                }
                $subQuery = ' (SELECT cat.id as id FROM #__abcategories AS cat JOIN #__abcategories AS parent ' .
                        'ON cat.lft BETWEEN parent.lft AND parent.rgt WHERE parent.extension = ' . $db->quote($extension) .
                        ' AND parent.published != 1 GROUP BY cat.id) ';
                $query->leftJoin($subQuery . 'AS badcats ON badcats.id = c.id');
                $query->where('badcats.id is null');

                // i for item
                if (isset($this->_options['countItems']) && $this->_options['countItems'] == 1)
                {
                        if ($this->_options['published'] == 1)
                        {
                                $query->leftJoin(
                                        $db->quoteName($this->_table) . ' AS i ON i.' . $db->quoteName($this->_field) . ' = c.id AND i.' . $this->_statefield . ' = 1'. ' AND i.language in (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')'
                                );
                        }
			elseif ($this->_options['published'] == 2)
                        {
                                $query->leftJoin($db->quoteName($this->_table) . ' AS i ON i.' . $db->quoteName($this->_field) . ' = c.id AND i.' . $this->_statefield . ' = 2'. ' AND i.language in (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')'
                                );
                        }
                        else {
                                $query->leftJoin($db->quoteName($this->_table) . ' AS i ON i.' . $db->quoteName($this->_field) . ' = c.id AND i.' . $this->_statefield . ' != "-1"'. ' AND i.language in (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')'
                                );
                        }

                        $query->select('COUNT(i.' . $db->quoteName($this->_key) . ') AS numitems');
                }

                // Group by
                $query->group('c.id, c.asset_id, c.access, c.alias, c.checked_out, c.checked_out_time,
                        c.created_time, c.created_user_id, c.description, c.extension, c.hits, c.language, c.level,
                        c.lft, c.metadata, c.metadesc, c.metakey, c.modified_time, c.note, c.params, c.parent_id,
                        c.path, c.published, c.rgt, c.title, c.modified_user_id');
                // Get the results
                $db->setQuery($query);
                $results = $db->loadObjectList('id');

                $childrenLoaded = false;
                if (count($results))
                {
                        // Foreach categories
                        foreach ($results as $result)
                        {
                                // Deal with root category
                                if ($result->id == 1)
                                {
                                        $result->id = 'root';
                                }

                                // Deal with parent_id
                                if ($result->parent_id == 1)
                                {
                                        $result->parent_id = 'root';
                                }

                                // Create the node
                                if (!isset($this->_nodes[$result->id]))
                                {
                                        // Create the JCategoryNode and add to _nodes
                                        $this->_nodes[$result->id] = new JCategoryNode($result, $this);

                                        // If this is not root and if the current node's parent is in the list or the current node parent is 0
                                        if ($result->id != 'root' && (isset($this->_nodes[$result->parent_id]) || $result->parent_id == 1))
                                        {
                                                // Compute relationship between node and its parent - set the parent in the _nodes field
                                                $this->_nodes[$result->id]->setParent($this->_nodes[$result->parent_id]);
                                        }

                                        // If the node's parent id is not in the _nodes list and the node is not root (doesn't have parent_id == 0),
                                        // then remove the node from the list
                                        if (!(isset($this->_nodes[$result->parent_id]) || $result->parent_id == 0))
                                        {
                                                unset($this->_nodes[$result->id]);
                                                continue;
                                        }

                                        if ($result->id == $id || $childrenLoaded)
                                        {
                                                $this->_nodes[$result->id]->setAllLoaded();
                                                $childrenLoaded = true;
                                        }
                                }
                                elseif ($result->id == $id || $childrenLoaded)
                                {
                                        // Create the JCategoryNode
                                        $this->_nodes[$result->id] = new JCategoryNode($result, $this);

                                        if ($result->id != 'root' && (isset($this->_nodes[$result->parent_id]) || $result->parent_id))
                                        {
                                                // Compute relationship between node and its parent
                                                $this->_nodes[$result->id]->setParent($this->_nodes[$result->parent_id]);
                                        }

                                        if (!isset($this->_nodes[$result->parent_id]))
                                        {
                                                unset($this->_nodes[$result->id]);
                                                continue;
                                        }

                                        if ($result->id == $id || $childrenLoaded)
                                        {
                                                $this->_nodes[$result->id]->setAllLoaded();
                                                $childrenLoaded = true;
                                        }

                                }
                        }
                }
                else
                {
                        $this->_nodes[$id] = null;
                }
        }
}
