<?php


namespace dodge\Helper\Steps;


use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Properties\TestProperties;
use Codeception\Lib\WFramework\Steps\StepsGroup;
use dodge\DodgeTester;
use dodge\Helper\AliasMaps\ChallengerModelsMap;
use dodge\Helper\Blocks\SelectModelPage\SelectModelBlock;
use dodge\Helper\Elements\DodgeModelCard\DodgeModelCard;

class SelectModelSteps extends StepsGroup
{
    /** @var DodgeTester */
    protected $I;

    /** @var SelectModelBlock */
    public $selectModelBlock;

    /** @var ChallengerModelsMap */
    public $challengerModelsMap;



    public function __construct(
        DodgeTester $I,
        SelectModelBlock $selectModelBlock,
        ChallengerModelsMap $challengerModelsMap
    )
    {
        $this->I = $I;
        $this->selectModelBlock = $selectModelBlock;
        $this->challengerModelsMap = $challengerModelsMap;
    }

    public function shouldBeDisplayed() : SelectModelSteps
    {
        $this->I->logNotice($this, 'Проверяем, что страница выбора модели авто - отобразилась');

        $this->selectModelBlock->shouldBeDisplayed(true);

        return $this;
    }

    public function selectBuyOption() : SelectModelSteps
    {
        $this->I->logNotice($this, 'Выбираем опцию покупки авто (а не кредита)');

        $this
            ->selectModelBlock
            ->getBuyButton()
            ->click()
            ;

        return $this;
    }

    public function selectModel(string $alias) : BuildModelSteps
    {
        $name = $this->challengerModelsMap->getValue($alias);

        $this->I->logNotice($this, 'Выбираем модель: ' . $name);

        $models = $this
                        ->selectModelBlock
                        ->getModelsArray()
                        ->shouldBeGreaterThanOrEqual(12)
                        ->getElementsMap('getModelName');

        if (!isset($models[$name]))
        {
            throw new UsageException('Среди отображаемых моделей: ' . implode(', ', array_keys($models)) . ' - нет модели с названием: ' . $name);
        }

        /** @var DodgeModelCard $model */
        $model = $models[$name];
        $model->build();

        TestProperties::setValue('currentModel', $name);

        return DodgeSteps::$buildModelSteps->shouldBeDisplayed();
    }
}
