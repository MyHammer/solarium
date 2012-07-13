<?php
/**
 * Copyright 2011 Bas de Nooijer. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this listof conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are
 * those of the authors and should not be interpreted as representing official
 * policies, either expressed or implied, of the copyright holder.
 */

namespace Solarium\Tests\QueryType\Select\Query\Component\Highlighting;
use Solarium\QueryType\Select\Query\Component\Highlighting\Highlighting;
use Solarium\QueryType\Select\Query\Component\Highlighting\Field;
use Solarium\QueryType\Select\Query\Query;

class HighlightingTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Highlighting
     */
    protected $hlt;

    public function setUp()
    {
        $this->hlt = new Highlighting;
    }

    public function testConfigMode()
    {
        $options = array(
            'field' => array(
                'fieldA' => array(
                    'snippets' => 3,
                    'fragsize' => 25,
                ),
                'fieldB'
            ),
            'snippets' => 2,
            'fragsize' => 20,
            'mergecontiguous' => true,
            'requirefieldmatch' => false,
            'maxanalyzedchars' => 40,
            'alternatefield' => 'text',
            'maxalternatefieldlength' => 50,
            'formatter' => 'myFormatter',
            'simpleprefix' => '<b>',
            'simplepostfix' => '</b>',
            'fragmenter' => 'myFragmenter',
            'fraglistbuilder' => 'regex',
            'fragmentsbuilder' => 'myBuilder',
            'usefastvectorhighlighter' => true,
            'usephrasehighlighter' => false,
            'highlightmultiterm' => true,
            'regexslop' => .8,
            'regexpattern' => 'myPattern',
            'regexmaxanalyzedchars' => 500,
            'query' => 'text:myvalue',
            'phraselimit' => 35,

        );

        $this->hlt->setOptions($options);

        $this->assertEquals(array('fieldA','fieldB'), array_keys($this->hlt->getFields()));
        $this->assertEquals($options['field']['fieldA']['snippets'], $this->hlt->getField('fieldA')->getSnippets());
        $this->assertEquals($options['field']['fieldA']['fragsize'], $this->hlt->getField('fieldA')->getFragSize());
        $this->assertEquals(null, $this->hlt->getField('FieldB')->getSnippets());
        $this->assertEquals($options['snippets'], $this->hlt->getSnippets());
        $this->assertEquals($options['fragsize'], $this->hlt->getFragSize());
        $this->assertEquals($options['mergecontiguous'], $this->hlt->getMergeContiguous());
        $this->assertEquals($options['maxanalyzedchars'], $this->hlt->getMaxAnalyzedChars());
        $this->assertEquals($options['alternatefield'], $this->hlt->getAlternateField());
        $this->assertEquals($options['maxalternatefieldlength'], $this->hlt->getMaxAlternateFieldLength());
        $this->assertEquals($options['formatter'], $this->hlt->getFormatter());
        $this->assertEquals($options['simpleprefix'], $this->hlt->getSimplePrefix());
        $this->assertEquals($options['simplepostfix'], $this->hlt->getSimplePostfix());
        $this->assertEquals($options['fragmenter'], $this->hlt->getFragmenter());
        $this->assertEquals($options['fraglistbuilder'], $this->hlt->getFragListBuilder());
        $this->assertEquals($options['fragmentsbuilder'], $this->hlt->getFragmentsBuilder());
        $this->assertEquals($options['usefastvectorhighlighter'], $this->hlt->getUseFastVectorHighlighter());
        $this->assertEquals($options['usephrasehighlighter'], $this->hlt->getUsePhraseHighlighter());
        $this->assertEquals($options['highlightmultiterm'], $this->hlt->getHighlightMultiTerm());
        $this->assertEquals($options['regexslop'], $this->hlt->getRegexSlop());
        $this->assertEquals($options['regexpattern'], $this->hlt->getRegexPattern());
        $this->assertEquals($options['regexmaxanalyzedchars'], $this->hlt->getRegexMaxAnalyzedChars());
        $this->assertEquals($options['query'], $this->hlt->getQuery());
        $this->assertEquals($options['phraselimit'], $this->hlt->getPhraseLimit());
    }

    public function testGetType()
    {
        $this->assertEquals(Query::COMPONENT_HIGHLIGHTING, $this->hlt->getType());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf('Solarium\QueryType\Select\ResponseParser\Component\Highlighting', $this->hlt->getResponseParser());
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf('Solarium\QueryType\Select\RequestBuilder\Component\Highlighting', $this->hlt->getRequestBuilder());
    }

    public function testGetFieldAutocreate()
    {
        $name = 'test';
        $field = $this->hlt->getField($name);

        $this->assertEquals($name, $field->getName());
    }

    public function testGetFieldNoAutocreate()
    {
        $name = 'test';
        $field = $this->hlt->getField($name, false);

        $this->assertEquals(null, $field);
    }

    public function testAddFieldWithObject()
    {
        $field = new Field;
        $field->setName('test');

        $this->hlt->addField($field);

        $this->assertEquals($field, $this->hlt->getField('test'));
    }

    public function testAddFieldWithString()
    {
        $name = 'test';
        $this->hlt->addField($name);

        $this->assertEquals(array($name), array_keys($this->hlt->getFields()));
    }

    public function testAddFieldWithArray()
    {
        $config = array(
            'name' => 'fieldA',
            'snippets' => 6
        );
        $this->hlt->addField($config);

        $this->assertEquals(6, $this->hlt->getField('fieldA')->getSnippets());
    }

    public function testAddFieldWithObjectWithoutName()
    {
        $field = new Field;
        $this->setExpectedException('Solarium\Exception\InvalidArgumentException');
        $this->hlt->addField($field);
    }

    public function testAddsFieldsWithString()
    {
        $fields = 'test1,test2';
        $this->hlt->addFields($fields);

        $this->assertEquals(array('test1','test2'), array_keys($this->hlt->getFields()));
    }

    public function testAddsFieldsWithArray()
    {
        $fields = array(
            'test1' => array('snippets' => 2),
            'test2' => array('snippets' => 5),
        );
        $this->hlt->addFields($fields);

        $this->assertEquals(2, $this->hlt->getField('test1')->getSnippets());
        $this->assertEquals(5, $this->hlt->getField('test2')->getSnippets());
    }

    public function testRemoveField()
    {
        $fields = array(
            'test1' => array('snippets' => 2),
            'test2' => array('snippets' => 5),
        );

        $this->hlt->addFields($fields);
        $this->assertEquals(array('test1','test2'), array_keys($this->hlt->getFields()));

        $this->hlt->removeField('test1');
        $this->assertEquals(array('test2'), array_keys($this->hlt->getFields()));
    }

    public function testRemoveFieldWithInvalidName()
    {
        $fields = array(
            'test1' => array('snippets' => 2),
            'test2' => array('snippets' => 5),
        );

        $this->hlt->addFields($fields);
        $this->assertEquals(array('test1','test2'), array_keys($this->hlt->getFields()));

        $this->hlt->removeField('test1=3'); // should fail silently and do nothing
        $this->assertEquals(array('test1','test2'), array_keys($this->hlt->getFields()));
    }

    public function testClearFields()
    {
        $fields = array(
            'test1' => array('snippets' => 2),
            'test2' => array('snippets' => 5),
        );

        $this->hlt->addFields($fields);
        $this->assertEquals(array('test1','test2'), array_keys($this->hlt->getFields()));

        $this->hlt->clearFields();
        $this->assertEquals(array(), array_keys($this->hlt->getFields()));
    }

    public function testSetFields()
    {
        $fields = array(
            'test1' => array('snippets' => 2),
            'test2' => array('snippets' => 5),
        );

        $this->hlt->addFields($fields);
        $this->assertEquals(array('test1','test2'), array_keys($this->hlt->getFields()));

        $newFields = array(
            'test3' => array('snippets' => 4),
            'test4' => array('snippets' => 6),
        );

        $this->hlt->setFields($newFields);
        $this->assertEquals(array('test3','test4'), array_keys($this->hlt->getFields()));
    }

    public function testSetAndGetSnippets()
    {
        $value = 2;
        $this->hlt->setSnippets($value);

        $this->assertEquals(
            $value,
            $this->hlt->getSnippets()
        );
    }

    public function testSetAndGetFragSize()
    {
        $value = 20;
        $this->hlt->setFragsize($value);

        $this->assertEquals(
            $value,
            $this->hlt->getFragSize()
        );
    }

    public function testSetAndGetMergeContiguous()
    {
        $value = true;
        $this->hlt->setMergeContiguous($value);

        $this->assertEquals(
            $value,
            $this->hlt->getMergeContiguous()
        );
    }

    public function testSetAndGetRequireFieldMatch()
    {
        $value = true;
        $this->hlt->setRequireFieldMatch($value);

        $this->assertEquals(
            $value,
            $this->hlt->getRequireFieldMatch()
        );
    }

    public function testSetAndGetMaxAnalyzedChars()
    {
        $value = 200;
        $this->hlt->setMaxAnalyzedChars($value);

        $this->assertEquals(
            $value,
            $this->hlt->getMaxAnalyzedChars()
        );
    }

    public function testSetAndGetAlternateField()
    {
        $value = 'description';
        $this->hlt->setAlternateField($value);

        $this->assertEquals(
            $value,
            $this->hlt->getAlternateField()
        );
    }

    public function testSetAndGetMaxAlternateFieldLength()
    {
        $value = 150;
        $this->hlt->setMaxAlternateFieldLength($value);

        $this->assertEquals(
            $value,
            $this->hlt->getMaxAlternateFieldLength()
        );
    }

    public function testSetAndGetFormatter()
    {
        $this->hlt->setFormatter();

        $this->assertEquals(
            'simple',
            $this->hlt->getFormatter()
        );
    }

    public function testSetAndGetSimplePrefix()
    {
        $value = '<em>';
        $this->hlt->setSimplePrefix($value);

        $this->assertEquals(
            $value,
            $this->hlt->getSimplePrefix()
        );
    }

    public function testSetAndGetSimplePostfix()
    {
        $value = '</em>';
        $this->hlt->setSimplePostfix($value);

        $this->assertEquals(
            $value,
            $this->hlt->getSimplePostfix()
        );
    }

    public function testSetAndGetFragmenter()
    {
        $value = Highlighting::FRAGMENTER_REGEX;
        $this->hlt->setFragmenter($value);

        $this->assertEquals(
            $value,
            $this->hlt->getFragmenter()
        );
    }

    public function testSetAndGetFragListBuilder()
    {
        $value = 'myBuilder';
        $this->hlt->setFragListBuilder($value);

        $this->assertEquals(
            $value,
            $this->hlt->getFragListBuilder()
        );
    }

    public function testSetAndGetFragmentsBuilder()
    {
        $value = 'myBuilder';
        $this->hlt->setFragmentsBuilder($value);

        $this->assertEquals(
            $value,
            $this->hlt->getFragmentsBuilder()
        );
    }

    public function testSetAndGetUseFastVectorHighlighter()
    {
        $value = true;
        $this->hlt->setUseFastVectorHighlighter($value);

        $this->assertEquals(
            $value,
            $this->hlt->getUseFastVectorHighlighter()
        );
    }

    public function testSetAndGetUsePhraseHighlighter()
    {
        $value = true;
        $this->hlt->setUsePhraseHighlighter($value);

        $this->assertEquals(
            $value,
            $this->hlt->getUsePhraseHighlighter()
        );
    }

    public function testSetAndGetHighlightMultiTerm()
    {
        $value = true;
        $this->hlt->setHighlightMultiTerm($value);

        $this->assertEquals(
            $value,
            $this->hlt->getHighlightMultiTerm()
        );
    }

    public function testSetAndGetRegexSlop()
    {
        $value = .8;
        $this->hlt->setRegexSlop($value);

        $this->assertEquals(
            $value,
            $this->hlt->getRegexSlop()
        );
    }

    public function testSetAndGetRegexPattern()
    {
        $value = 'myPattern';
        $this->hlt->setRegexPattern($value);

        $this->assertEquals(
            $value,
            $this->hlt->getRegexPattern()
        );
    }

    public function testSetAndGetRegexMaxAnalyzedChars()
    {
        $value = 500;
        $this->hlt->setRegexMaxAnalyzedChars($value);

        $this->assertEquals(
            $value,
            $this->hlt->getRegexMaxAnalyzedChars()
        );
    }

    public function testSetAndGetQuery()
    {
        $value = 'text:myvalue';
        $this->hlt->setQuery($value);

        $this->assertEquals(
            $value,
            $this->hlt->getQuery()
        );
    }

    public function testSetAndGetPhraseLimit()
    {
        $value = 20;
        $this->hlt->setPhraseLimit($value);

        $this->assertEquals(
            $value,
            $this->hlt->getPhraseLimit()
        );
    }

    public function testSetAndGetTagPrefix()
    {
        $value = '<i>';
        $this->_hlt->setTagPrefix($value);

        $this->assertEquals(
            $value,
            $this->_hlt->getTagPrefix()
        );
    }

    public function testSetAndGetTagPostfix()
    {
        $value = '</i>';
        $this->_hlt->setTagPostfix($value);

        $this->assertEquals(
            $value,
            $this->_hlt->getTagPostfix()
        );
    }

}
