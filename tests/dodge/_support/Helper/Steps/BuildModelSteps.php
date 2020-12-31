<?php


namespace dodge\Helper\Steps;


use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Properties\TestProperties;
use Codeception\Lib\WFramework\Steps\StepsGroup;
use dodge\DodgeTester;
use dodge\Helper\AliasMaps\ChallengerDecalsMap;
use dodge\Helper\Blocks\BuildModelPage\ExteriorColorBlock;
use dodge\Helper\Blocks\BuildModelPage\StripesAndDecalsBlock;
use dodge\Helper\Blocks\BuildModelPage\TitleBlock;
use dodge\Helper\Elements\DodgeOption\DodgeOption;

class BuildModelSteps extends StepsGroup
{
    /** @var DodgeTester */
    protected $I;

    /** @var TitleBlock */
    public $titleBlock;

    /** @var ExteriorColorBlock */
    public $exteriorColorBlock;

    /** @var StripesAndDecalsBlock */
    public $stripesAndDecalsBlock;

    /** @var ChallengerDecalsMap */
    public $challengerDecalsMap;

    public function __construct(
        DodgeTester $I,
        TitleBlock $titleBlock,
        ExteriorColorBlock $exteriorColorBlock,
        StripesAndDecalsBlock $stripesAndDecalsBlock,

        ChallengerDecalsMap $challengerDecalsMap
    )
    {
        $this->I = $I;
        $this->titleBlock = $titleBlock;
        $this->exteriorColorBlock = $exteriorColorBlock;
        $this->stripesAndDecalsBlock = $stripesAndDecalsBlock;
        $this->challengerDecalsMap = $challengerDecalsMap;
    }

    public function shouldBeDisplayed() : BuildModelSteps
    {
        $this->I->logNotice('Проверяем, что страница настройки модели авто - отобразилась');

        $this->titleBlock->shouldBeDisplayed();
        $this->exteriorColorBlock->shouldBeDisplayed();
        $this->stripesAndDecalsBlock->shouldBeDisplayed();

        return $this;
    }

    public function checkModelName() : BuildModelSteps
    {
        $expectedModel = TestProperties::mustGetValue('currentModel');

        $this->I->logNotice("Проверяем, что отображается модель: $expectedModel");

        $actualModel = $this
                            ->titleBlock
                            ->getTitleLabel()
                            ->getCurrentValueString()
                            ;

        $this->I->assertContains($expectedModel, $actualModel);

        return $this;
    }

    public function setModelColor(string $alias) : BuildModelSteps
    {
        $this->I->logNotice("Выбираем цвет: $alias");

        $this
            ->exteriorColorBlock
            ->getColorPicker()
            ->selectColor($alias)
            ;

        return $this;
    }

    public function selectDecal(string $alias) : BuildModelSteps
    {
        $decal = $this->challengerDecalsMap->getValue($alias);

        $this->I->logNotice("Выбираем винил: $decal");

        $decals = $this
                    ->stripesAndDecalsBlock
                    ->getDecalsOptions()
                    ->shouldBeGreaterThanOrEqual(count($this->challengerDecalsMap->getAliasesList()))
                    ->getElementsMap('getOptionName')
                    ;

        if (!isset($decals[$decal]))
        {
            throw new UsageException("Винила: $decal - нет среди доступных: " . implode(', ', array_keys($decals)));
        }

        /** @var DodgeOption $decalOption */
        $decalOption = $decals[$decal];
        $decalOption->select();

        return $this;
    }
}
