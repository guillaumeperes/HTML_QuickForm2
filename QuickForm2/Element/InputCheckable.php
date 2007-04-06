<?php
/**
 * Base class for checkboxes and radios
 *
 * PHP version 5
 *
 * LICENSE:
 * 
 * Copyright (c) 2006, 2007, Alexey Borzov <avb@php.net>,
 *                           Bertrand Mansion <golgote@mamasam.com>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *    * Redistributions of source code must retain the above copyright
 *      notice, this list of conditions and the following disclaimer.
 *    * Redistributions in binary form must reproduce the above copyright
 *      notice, this list of conditions and the following disclaimer in the 
 *      documentation and/or other materials provided with the distribution.
 *    * The names of the authors may not be used to endorse or promote products 
 *      derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
 * IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   HTML
 * @package    HTML_QuickForm2
 * @author     Alexey Borzov <avb@php.net>
 * @author     Bertrand Mansion <golgote@mamasam.com>
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/HTML_QuickForm2
 */

/**
 * Base class for <input> elements
 */
require_once 'HTML/QuickForm2/Element/Input.php';

/**
 * Base class for <input> elements having 'checked' attribute (checkboxes and radios)   
 *
 * @category   HTML
 * @package    HTML_QuickForm2
 * @author     Alexey Borzov <avb@php.net>
 * @author     Bertrand Mansion <golgote@mamasam.com>
 * @version    Release: @package_version@
 */
class HTML_QuickForm2_Element_InputCheckable extends HTML_QuickForm2_Element_Input
{
    protected $persistent = true;

   /**
    * HTML to represent the element in "frozen" state 
    * 
    * Array index "checked" contains HTML for element's "checked" state, 
    * "unchecked" for not checked  
    * @var  array
    */
    protected $frozenHtml = array(
        'checked'   => 'On',
        'unchecked' => 'Off'
    );

   /**
    * Label to render glued to a checkbox or a radio  
    * @var  string
    */
    protected $gluedLabel;

   /**
    * Class constructor
    *
    * @param    string  Element name
    * @param    string  Label "glued" to a checkbox or radio 
    * @param    mixed   Label for the element (may be an array of labels)
    * @param    mixed   Attributes (either a string or an array)
    */
    public function __construct($name = null, $options = null, $label = null, $attributes = null)
    {
        parent::__construct($name, $options, $label, $attributes);
        $this->setGluedLabel($options);
    }

   /**
    * Sets the label to be rendered glued to the element
    *
    * This label is returned by {@link __toString()} method with the element's 
    * HTML. It is automatically wrapped into the <label> tag. 
    *
    * @param    string
    */
    public function setGluedLabel($label)
    {
        $this->gluedLabel = $label;
    }

   /**
    * Returns the label that will be "glued" to element's HTML
    *
    * @return   string
    */
    public function getGluedLabel()
    {
        return $this->gluedLabel;
    }


    public function setValue($value)
    {
        if ((string)$value == $this->getAttribute('value')) {
            $this->setAttribute('checked');
        } else {
            $this->removeAttribute('checked');
        }
    }

    public function getValue()
    {
        if (!empty($this->attributes['checked']) && empty($this->attributes['disabled'])) {
            return $this->getAttribute('value');
        } else {
            return null;
        }
    }

    public function __toString()
    {
        if (0 == strlen($this->gluedLabel)) {
            $label = '';
        } elseif ($this->frozen) {
            $label = $this->gluedLabel;
        } else {
            $label = '<label for="' . htmlspecialchars(
                         $this->getId(), ENT_QUOTES, self::getOption('charset')
                     ) . '">' . $this->gluedLabel . '</label>';
        }
        return parent::__toString() . $label;
    }

    public function getFrozenHtml()
    {
        if ($this->getAttribute('checked')) {
            return $this->frozenHtml['checked'] . $this->getPersistentData();
        } else {
            return $this->frozenHtml['unchecked'];
        }
    }
}
?>