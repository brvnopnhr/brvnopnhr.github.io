<?php
/**
 * @version		$Id: article.php 18212 2010-07-22 06:02:54Z eddieajau $
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

/**
 * Supports a modal article picker.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @since		1.6
 */
class JFormFieldModal_Author extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Modal_Author';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		$allowEdit              = ((string) $this->element['edit'] == 'true') ? true : false;
                $allowClear             = ((string) $this->element['clear'] != 'false') ? true : false;

		// Load language
                JFactory::getLanguage()->load('com_content', JPATH_ADMINISTRATOR);

		// Load the modal behavior script.
		JHtml::_('behavior.modal', 'a.modal');

		// Build the script.
		$script = array();
		$script[] = '	function jSelectBook_'.$this->id.'(id, name) {';
		$script[] = '		document.getElementById("'.$this->id.'_id").value = id;';
		$script[] = '		document.getElementById("'.$this->id.'_name").value = name;';
		if ($allowEdit)
                {
                        $script[] = '           jQuery("#'.$this->id.'_edit").removeClass("hidden");';
                }

                if ($allowClear)
                {
                        $script[] = '           jQuery("#'.$this->id.'_clear").removeClass("hidden");';
                }

		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));


		// Setup variables for display.
		$html	= array();
		$link	= 'index.php?option=com_abook&amp;view=authors&amp;layout=modal&amp;tmpl=component&amp;function=jSelectBook_'.$this->id;

		$db	= JFactory::getDBO();
		$db->setQuery(
			'SELECT name' .
			' FROM #__abauthor' .
			' WHERE id = '.(int) $this->value
		);
		$title = $db->loadResult();

		if ($error = $db->getErrorMsg()) {
			JError::raiseWarning(500, $error);
		}

		if (empty($title)) {
			$title = JText::_('COM_ABOOK_SELECT_AN_AUTHOR');
		}
		$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

		// The active article id field.
		if (0 == (int)$this->value) {
			$value = '';
		} else {
			$value = (int)$this->value;
		}

		                // The current article display field.
                $html[] = '<span class="input-append">';
                $html[] = '<input type="text" class="input-medium" id="'.$this->id.'_name" value="'.$title.'" disabled="disabled" size="35" />';
                $html[] = '<a class="modal btn hasTooltip" title="'.JHtml::tooltipText('COM_CONTENT_CHANGE_ARTICLE').'"  href="'.$link.'&amp;'.JSession::getFormToken().'=1" rel="{handler: \'iframe\', size: {x: 800, y: 450}}"><i class="icon-user"></i> '.JText::_('JSELECT').'</a>';

                // Edit article button
                if ($allowEdit)
                {
                        $html[] = '<a class="btn hasTooltip'.($value ? '' : ' hidden').'" href="index.php?option=com_content&layout=modal&tmpl=component&task=article.edit&id=' . $value. '" target="_blank" title="'.JHtml::tooltipText('COM_CONTENT_EDIT_ARTICLE').'" ><span class="icon-edit"></span> ' . JText::_('JACTION_EDIT') . '</a>';
                }

                // Clear article button
                if ($allowClear)
                {
                        $html[] = '<button id="'.$this->id.'_clear" class="btn'.($value ? '' : ' hidden').'" onclick="return jClearArticle(\''.$this->id.'\')"><span class="icon-remove"></span> ' . JText::_('JCLEAR') . '</button>';
                }

                $html[] = '</span>';

		// class='required' for client side validation
		$class = '';
		if ($this->required) {
			$class = ' class="required modal-value"';
		}

		$html[] = '<input type="hidden" id="'.$this->id.'_id"'.$class.' name="'.$this->name.'" value="'.$value.'" />';

		return implode("\n", $html);
	}
}
