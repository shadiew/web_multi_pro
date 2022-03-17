<?php

namespace crawlomatic_andreskrey\Readability\Nodes\DOM;

use crawlomatic_andreskrey\Readability\Nodes\NodeTrait;

/**
 * @method getAttribute($attribute)
 * @method hasAttribute($attribute)
 */
class DOMNode extends \DOMNode
{
    use NodeTrait;
}
