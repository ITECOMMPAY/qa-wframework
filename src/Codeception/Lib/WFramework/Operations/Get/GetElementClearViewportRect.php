<?php


namespace Codeception\Lib\WFramework\Operations\Get;


use Codeception\Lib\WFramework\Helpers\Rect;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\Operations\Execute\ExecuteScriptOnThis;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Ds\Sequence;

class GetElementClearViewportRect extends AbstractOperation
{
    public function getName() : string
    {
        return "получаем размер viewport'а за вычетом плавающих панелек";
    }

    /**
     * Возвращает размер viewport'а для элемента с вычетом плавающих панелек
     */
    public function __construct() {}

    public function acceptWBlock($block) : Rect
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : Rect
    {
        return $this->apply($element);
    }

    /**
     * @param WCollection $collection
     * @return \Ds\Sequence - массив результатов применения операции для каждого элемента коллекции
     */
    public function acceptWCollection($collection) : Sequence
    {
        return $collection->getElementsArray()->map([$this, 'apply']);
    }

    protected function apply(WPageObject $pageObject) : Rect
    {
        return Rect::fromDOMRect($pageObject->accept(new ExecuteScriptOnThis(static::SCRIPT_GET_ELEMENT_CLEAR_VIEWPORT_SIZE)));
    }

    protected const SCRIPT_GET_ELEMENT_CLEAR_VIEWPORT_SIZE = <<<EOF
return getElementViewportSize(arguments[0]);

function getElementViewportSize(element) {
    let scrollParent = getScrollParent(element, false);
    
    return getClearViewport(scrollParent);
}

function getScrollParent(element, includeHidden) {
    var style = getComputedStyle(element);
    var excludeStaticParent = style.position === "absolute";
    var overflowRegex = includeHidden ? /(auto|scroll|hidden)/ : /(auto|scroll)/;

    if (overflowRegex.test(style.overflow + style.overflowY + style.overflowX)) return element;

    if (style.position === "fixed") return document.documentElement;
    for (var parent = element; (parent = parent.parentElement);) {
        style = getComputedStyle(parent);
        if (excludeStaticParent && style.position === "static") {
            continue;
        }
        if (overflowRegex.test(style.overflow + style.overflowY + style.overflowX)) return parent;
    }

    return document.documentElement;
}

function getViewportStickies(viewport) {
    let result = [];

    let elements = viewport.getElementsByTagName("*");

    for (const element of elements) {
        let cs = getComputedStyle(element);
        let position = cs['position'];

        if (position !== 'sticky') {
            continue;
        }

        result.push(getStickyPosition(element, cs));
    }

    return result;
}

function getStickyPosition(sticky, computedStyle) {
    let positions = ['top', 'bottom', 'left', 'right'];

    for (const pos of positions) {
        let value = computedStyle[pos] ?? 'auto';

        if (isNaN(parseInt(value, 10))) {
            continue;
        }

        let pixels = toPixels(sticky, value, pos);

        return { element: sticky, position: pos, value: pixels };
    }

    return { element: sticky, position: "top", value: 0 };
}

function getCutValues(stickies) {
    var cutValues = { top: [0], bottom: [0], left: [0], right: [0] };

    for (sticky of stickies) {
        let { element, position, value } = sticky;

        let rect = element.getBoundingClientRect();

        var cutValue = 0;

        if (["top", "bottom"].indexOf(position) > -1) {
            cutValue = rect.height + value;
        } else {
            cutValue = rect.width + value;
        }

        cutValues[position].push(cutValue);
    }

    return {
        top: Math.max(...cutValues.top),
        bottom: Math.max(...cutValues.bottom),
        left: Math.max(...cutValues.left),
        right: Math.max(...cutValues.right)
    };
}

function getClearViewport(viewport) {
    let stickies = getViewportStickies(viewport);

    var viewportRect = viewport.getBoundingClientRect();

    if (!Array.isArray(stickies) || !stickies.length) {
        return viewportRect;
    }

    let cutValues = getCutValues(stickies);

    viewportRect = new DOMRect(viewportRect.x, viewportRect.y + cutValues.top, viewportRect.width, viewportRect.height - cutValues.top - cutValues.bottom);
    viewportRect = new DOMRect(viewportRect.x + cutValues.left, viewportRect.y, viewportRect.width - cutValues.left - cutValues.right, viewportRect.height);

    return viewportRect;
}


//https://github.com/heygrady/Units

function toPixels(elem, value, prop) {
    // create a test element
    var testElem = document.createElement('test'),
        docElement = document.documentElement,
        defaultView = document.defaultView,
        getComputedStyle = defaultView && defaultView.getComputedStyle,
        runit = /^(-?[\d+\.\-]+)([a-z]+|%)$/i,
        convert = {},
        conversions = [1 / 25.4, 1 / 2.54, 1 / 72, 1 / 6],
        units = ['mm', 'cm', 'pt', 'pc', 'in', 'mozmm'],
        i = 6; // units.length

    // add the test element to the dom
    docElement.appendChild(testElem);

    // pre-calculate absolute unit conversions
    while (i--) {
        convert[units[i] + "toPx"] = conversions[i] ? conversions[i] * convert.inToPx : toPx(testElem, '1' + units[i]);
    }

    // remove the test element from the DOM and delete it
    docElement.removeChild(testElem);
    testElem = undefined;

    // convert a value to pixels
    function toPx(elem, value, prop, force) {
        // use width as the default property, or specify your own
        prop = prop || 'width';

        var style,
            inlineValue,
            ret,
            unit = (value.match(runit) || [])[2],
            conversion = unit === 'px' ? 1 : convert[unit + 'toPx'],
            rem = /r?em/i;

        if (conversion || rem.test(unit) && !force) {
            // calculate known conversions immediately
            // find the correct element for absolute units or rem or fontSize + em or em
            elem = conversion ? elem : unit === 'rem' ? docElement : prop === 'fontSize' ? elem.parentNode || elem : elem;

            // use the pre-calculated conversion or fontSize of the element for rem and em
            conversion = conversion || parseFloat(curCSS(elem, 'fontSize'));

            // multiply the value by the conversion
            ret = parseFloat(value) * conversion;
        } else {
            // begin "the awesome hack by Dean Edwards"
            // @see http://erik.eae.net/archives/2007/07/27/18.54.15/#comment-102291

            // remember the current style
            style = elem.style;
            inlineValue = style[prop];

            // set the style on the target element
            try {
                style[prop] = value;
            } catch (e) {
                // IE 8 and below throw an exception when setting unsupported units
                return 0;
            }

            // read the computed value
            // if style is nothing we probably set an unsupported unit
            ret = !style[prop] ? 0 : parseFloat(curCSS(elem, prop));

            // reset the style back to what it was or blank it out
            style[prop] = inlineValue !== undefined ? inlineValue : null;
        }

        // return a number
        return ret;
    }

    // return the computed value of a CSS property
    function curCSS(elem, prop) {
        var value,
            pixel,
            unit,
            rvpos = /^top|bottom/,
            outerProp = ["paddingTop", "paddingBottom", "borderTop", "borderBottom"],
            innerHeight,
            parent,
            i = 4; // outerProp.length

        if (getComputedStyle) {
            // FireFox, Chrome/Safari, Opera and IE9+
            value = getComputedStyle(elem)[prop];
        } else if (pixel = elem.style['pixel' + prop.charAt(0).toUpperCase() + prop.slice(1)]) {
            // IE and Opera support pixel shortcuts for top, bottom, left, right, height, width
            // WebKit supports pixel shortcuts only when an absolute unit is used
            value = pixel + 'px';
        } else if (prop === 'fontSize') {
            // correct IE issues with font-size
            // @see http://bugs.jquery.com/ticket/760
            value = toPx(elem, '1em', 'left', 1) + 'px';
        } else {
            // IE 8 and below return the specified style
            value = elem.currentStyle[prop];
        }

        // check the unit
        unit = (value.match(runit) || [])[2];
        if ((value === 'auto' || (unit && unit !== 'px')) && getComputedStyle) {
            // WebKit and Opera will return auto in some cases
            // Firefox will pass back an unaltered value when it can't be set, like top on a static element
            value = 0;
        } else if (unit && unit !== 'px' && !getComputedStyle) {
            // IE 8 and below won't convert units for us
            // try to convert using a prop that will return pixels
            // this will be accurate for everything (except font-size and some percentages)
            value = toPx(elem, value) + 'px';
        }
        return value;
    }

    return toPx(elem, value, prop);
}
EOF;

}
