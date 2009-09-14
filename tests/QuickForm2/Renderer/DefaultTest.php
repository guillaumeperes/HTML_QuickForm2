<?php
/**
 * Unit tests for HTML_QuickForm2 package
 *
 * PHP version 5
 *
 * LICENSE:
 * 
 * Copyright (c) 2006-2009, Alexey Borzov <avb@php.net>,
 *                          Bertrand Mansion <golgote@mamasam.com>
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
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/HTML_QuickForm2
 */

/**
 * PHPUnit Test Case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Renderer base class
 */
require_once 'HTML/QuickForm2/Renderer.php';

/**
 * Class representing a HTML form
 */
require_once 'HTML/QuickForm2.php';

/**
 * Unit test for HTML_QuickForm2_Renderer_Default class
 */
class HTML_QuickForm2_Renderer_DefaultTest extends PHPUnit_Framework_TestCase
{
    public function testRenderElementUsingMostAppropriateTemplate()
    {
        $element = HTML_QuickForm2_Factory::createElement(
            'text', 'foo', array('id' => 'testRenderElement')
        );
        $renderer = HTML_Quickform2_Renderer::getInstance('default')
            ->setTemplateByClass(
                'HTML_QuickForm2_Element_InputText', 'InputText;id={id},html={element}'
            )->setTemplateByClass(
                'HTML_QuickForm2_Element_Input', 'Input;id={id},html={element}'
            )->setTemplateById(
                'testRenderElement', 'testRenderElement;id={id},html={element}'
            );

        $this->assertEquals(
            'testRenderElement;id=' . $element->getId() . ',html=' . $element->__toString(),
            $element->render($renderer->reset())->__toString()
        );

        $renderer->setTemplateById('testRenderElement', null);
        $this->assertEquals(
            'InputText;id=' . $element->getId() . ',html=' . $element->__toString(),
            $element->render($renderer->reset())->__toString()
        );

        $renderer->setTemplateByClass('HTML_QuickForm2_Element_InputText', null);
        $this->assertEquals(
            'Input;id=' . $element->getId() . ',html=' . $element->__toString(),
            $element->render($renderer->reset())->__toString()
        );
    }

    public function testRenderRequiredElement()
    {
        $element = HTML_QuickForm2_Factory::createElement(
            'text', 'foo', array('id' => 'testRenderRequiredElement')
        );

        $renderer = HTML_Quickform2_Renderer::getInstance('default')
            ->setTemplateById(
                'testRenderRequiredElement',
                '<qf:required>required!</qf:required>{element}<qf:required><em>*</em></qf:required>'
            );
        $this->assertEquals(
            $element->__toString(),
            $element->render($renderer->reset())->__toString()
        );

        $element->addRule('required', 'error message');
        $this->assertEquals(
            'required!' . $element->__toString() . '<em>*</em>',
            $element->render($renderer->reset())->__toString()
        );
    }

    public function testRenderElementWithValidationError()
    {
        $element = HTML_QuickForm2_Factory::createElement(
            'text', 'foo', array('id' => 'testElementWithError')
        );
        $renderer = HTML_Quickform2_Renderer::getInstance('default')
            ->setTemplateById(
                'testElementWithError',
                '<qf:error>an error!</qf:error>{element}<qf:error>{error}</qf:error>'
            );

        $this->assertEquals(
            $element->__toString(),
            $element->render($renderer->reset())->__toString()
        );

        $element->setError('some message');
        $this->assertEquals(
            'an error!' . $element->__toString() . 'some message',
            $element->render(
                $renderer->reset()->setOption('group_errors', false)
            )->__toString()
        );

        $this->assertEquals(
            $element->__toString(),
            $element->render(
                $renderer->reset()->setOption('group_errors', true)
            )->__toString()
        );
    }

    public function testRenderElementWithSingleLabel()
    {
        $element = HTML_QuickForm2_Factory::createElement(
            'text', 'foo', array('id' => 'testSingleLabel')
        );
        $renderer = HTML_Quickform2_Renderer::getInstance('default')
            ->setTemplateById(
                'testSingleLabel',
                '<qf:label>A label: </qf:label>{element}{label}'
            );

        $this->assertEquals(
            $element->__toString(),
            $element->render($renderer->reset())->__toString()
        );
        $element->setLabel('the label!');
        $this->assertEquals(
            'A label: ' . $element->__toString() . 'the label!',
            $element->render($renderer->reset())->__toString()
        );
    }

    public function testRenderElementWithMultipleLabels()
    {
        $element = HTML_QuickForm2_Factory::createElement(
            'text', 'foo', array('id' => 'testMultipleLabels')
        )->setLabel(array('first', 'second'));
        $renderer = HTML_Quickform2_Renderer::getInstance('default')
            ->setTemplateById(
                'testMultipleLabels',
                '<qf:label>First label: {label}</qf:label>{element}<qf:label_2>Second label: {label_2}</qf:label_2>' .
                '<qf:label_foo>Named label: {label_foo}</qf:label_foo>'
            );

        $this->assertEquals(
            'First label: first' . $element->__toString() . 'Second label: second',
            $element->render($renderer->reset())->__toString()
        );

        $element->setLabel(array('another', 'foo' => 'foo'));
        $this->assertEquals(
            'First label: another' . $element->__toString() . 'Named label: foo',
            $element->render($renderer->reset())->__toString()
        );
    }

    public function testRenderRequiredNote()
    {
        $form = new HTML_QuickForm2('reqnote');
        $element = $form->addText('testReqnote');

        $renderer = HTML_Quickform2_Renderer::getInstance('default')
            ->setOption('required_note', 'This is requi-i-i-ired!');

        $this->assertNotContains('<div class="reqnote">', $form->render($renderer)->__toString());

        $element->addRule('required', 'error message');
        $this->assertContains('<div class="reqnote">This is requi-i-i-ired!</div>', $form->render($renderer)->__toString());
    }

    public function testRenderGroupedErrors()
    {
        $form     = new HTML_QuickForm2('groupedErrors');
        $element  = $form->addText('testGroupedErrors')->setError('Some error');
        $renderer = HTML_Quickform2_Renderer::getInstance('default')
            ->setOption(array(
                'group_errors'  => true,
                'errors_prefix' => 'Your errors:',
                'errors_suffix' => ''
            ));

        $this->assertContains(
            '<div class="errors"><p>Your errors:</p><ul><li>Some error</li></ul></div>',
            $form->render($renderer)->__toString()
        );
    }

    public function testRenderGroupedHiddens()
    {
        $form     = new HTML_QuickForm2('groupedHiddens');
        $hidden1  = $form->addHidden('hidden1');
        $hidden2  = $form->addHidden('hidden2');
        $renderer = HTML_Quickform2_Renderer::getInstance('default')
            ->setOption('group_hiddens', false);

        $html = $form->render($renderer)->__toString();
        $this->assertContains('<div style="display: none;">' . $hidden1->__toString() . '</div>', $html);
        $this->assertContains('<div style="display: none;">' . $hidden2->__toString() . '</div>', $html);

        $renderer->setOption('group_hiddens', true);
        $html = $form->render($renderer)->__toString();
        $this->assertNotContains('<div style="display: none;">', $html);
        $this->assertContains($hidden1->__toString() . $hidden2->__toString(), $html);
    }
}
?>