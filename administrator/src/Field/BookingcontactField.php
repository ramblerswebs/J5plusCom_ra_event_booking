<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Ra_eventbooking
 * @author     Chris Vaughan  <ruby.tuesday@ramblers-webs.org.uk>
 * @copyright  2025 Ruby Tuesday
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Ramblers\Component\Ra_eventbooking\Administrator\Field;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use \Joomla\CMS\Form\FormField;
use Joomla\Utilities\ArrayHelper;

/**
 * Class SubmitField
 *
 * @since  1.0.0
 */
class BookingcontactField extends FormField {

    protected $type = 'Bookingcontact';
    protected $value;
    protected $for;
    private $configMode = false;

    /**
     * Get a form field markup for the input
     *
     * @return string
     */
    #[\Override]
    protected function getInput() {
        $context = (string) $this->element['context'];  // "config"

        if ($context === 'config') {
            // Config.xml specific logic
            $this->configMode = true;
        }
        $options = $this->getOptions();
        $html = '<select class="form-select" name="' . $this->name . '" value="' . $this->value . '" >';
        if (!$this->configMode) {
            $html .= '<option value="">Not specified [Use Group Booking Contact]</option>';
        }
        foreach ($options as $option) {
            if ($this->value === $option->value) {
                $html .= '<option value="' . $option->value . '" selected="selected">' . $option->text . '</option>';
            } else {
                $html .= '<option value="' . $option->value . '">' . $option->text . '</option>';
            }
        }
        $html .= '</select>';
        return $html;
    }

    #[\Override]
    protected function getLabel() {
        return parent::getLabel();
    }

    protected function getOptions() {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->select('*')
                ->from($db->quoteName('#__users'));
        $db->setQuery($query);
        $users = $db->loadObjectList();
        $options = [];
        foreach ($users as $user) {
            if ($this->canEdit($user)) {
                $options[] = (object) ['value' => $user->id, 'text' => $user->name . ' (' . $user->username . ')'];
            }
        }
        return $options;
    }

    protected function canEdit($user) {
        if ($user->id > 0) {
            $juser = Factory::getUser($user->id);
            return $juser->authorise('core.edit', 'com_ra_eventbooking');
        }
        return false;
    }
}
