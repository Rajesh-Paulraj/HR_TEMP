<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GeminiLabs\Symfony\Component\CssSelector\Tests\Node;

use GeminiLabs\Symfony\Component\CssSelector\Node\ClassNode;
use GeminiLabs\Symfony\Component\CssSelector\Node\ElementNode;
use GeminiLabs\Symfony\Component\CssSelector\Node\NegationNode;

class NegationNodeTest extends AbstractNodeTest
{
    public function getToStringConversionTestData()
    {
        return [
            [new NegationNode(new ElementNode(), new ClassNode(new ElementNode(), 'class')), 'Negation[Element[*]:not(Class[Element[*].class])]'],
        ];
    }

    public function getSpecificityValueTestData()
    {
        return [
            [new NegationNode(new ElementNode(), new ClassNode(new ElementNode(), 'class')), 10],
        ];
    }
}
