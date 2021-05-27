<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\Operations\Execute\ExecuteScriptOnThis;
use Codeception\Lib\WFramework\Properties\TestProperties;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class AnimationStopped extends AbstractCondition
{
    public function getName() : string
    {
        return "анимация закончилась?";
    }

    protected $timeout = null;

    public function acceptWBlock($block) : bool
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : bool
    {
        return $this->apply($element);
    }

    protected function apply(WPageObject $pageObject) : bool
    {
        if ($this->timeout === null)
        {
            $this->timeout = $this->getExpirationTime($pageObject);
        }

        return time() >= $this->timeout;
    }

    protected function getExpirationTime(WPageObject $pageObject) : int
    {
        $timeout = time();

        $animationTimeMs = (int) $pageObject->accept(new ExecuteScriptOnThis(static::SCRIPT_GET_TIMEOUT_MS));

        WLogger::logDebug($this, "получили время анимации: $animationTimeMs мс");

        $animationTime = $animationTimeMs / 1000;

        $elementTimeout = (int) TestProperties::mustGetValue('elementTimeout');

        if ($animationTime < 0)
        {
            $animationTime = 0;
        }
        elseif ($animationTime > $elementTimeout)
        {
            $animationTime = $elementTimeout;
        }

        return $timeout + $animationTime;
    }

    protected const SCRIPT_GET_TIMEOUT_MS = <<<EOF
function* getSoleChildren(element) {
    yield element;

    while (element.children.length === 1) {
        element = element.children[0];

        yield element;
    }
}

function findAnimatedElement(element) {
    for (const childOrSelf of getSoleChildren(element)) {
        if (childOrSelf.style.animationName.trim() !== '') {
            return childOrSelf;
        }
    }

    return null;
}

function getTimeInMs(str) {
    if (typeof str !== 'string') {
        return 0;
    }

    const regex = /(?<time>\d+)(?<units>s|ms)/;

    let matches = regex.exec(str);

    if (matches === null) {
        return 0;
    }

    if (matches.groups.units === 's') {
        return matches.groups.time * 1000;
    }

    return matches.groups.time;
}

let animatedElement = findAnimatedElement(arguments[0]);

if (animatedElement === null) {
    return 0;
}

return getTimeInMs(animatedElement.style.animation-delay) + getTimeInMs(animatedElement.style.animation-duration);
EOF;

}