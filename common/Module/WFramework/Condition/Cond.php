<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 26.02.19
 * Time: 13:05
 */

namespace Common\Module\WFramework\Condition;


use Common\Module\WFramework\Condition\Operator\Attribute;
use Common\Module\WFramework\Condition\Operator\AttributeValue;
use Common\Module\WFramework\Condition\Operator\Conj;
use Common\Module\WFramework\Condition\Operator\CssClass;
use Common\Module\WFramework\Condition\Operator\CssValue;
use Common\Module\WFramework\Condition\Operator\Delegate;
use Common\Module\WFramework\Condition\Operator\Disabled;
use Common\Module\WFramework\Condition\Operator\Disj;
use Common\Module\WFramework\Condition\Operator\Enabled;
use Common\Module\WFramework\Condition\Operator\ExactText;
use Common\Module\WFramework\Condition\Operator\Exist;
use Common\Module\WFramework\Condition\Operator\Explain;
use Common\Module\WFramework\Condition\Operator\Focused;
use Common\Module\WFramework\Condition\Operator\Hidden;
use Common\Module\WFramework\Condition\Operator\InView;
use Common\Module\WFramework\Condition\Operator\Not;
use Common\Module\WFramework\Condition\Operator\PageLoaded;
use Common\Module\WFramework\Condition\Operator\Selected;
use Common\Module\WFramework\Condition\Operator\SelectedText;
use Common\Module\WFramework\Condition\Operator\Text;
use Common\Module\WFramework\Condition\Operator\TextCaseSensitive;
use Common\Module\WFramework\Condition\Operator\TextContains;
use Common\Module\WFramework\Condition\Operator\TextMatchesRegex;
use Common\Module\WFramework\Condition\Operator\ExactValue;
use Common\Module\WFramework\Condition\Operator\Value;
use Common\Module\WFramework\Condition\Operator\ValueCaseSensitive;
use Common\Module\WFramework\Condition\Operator\ValueContains;
use Common\Module\WFramework\Condition\Operator\Visible;
use Common\Module\WFramework\FacadeWebElement\FacadeWebElement;
use Common\Module\WFramework\Logger\WLogger;


/**
 * Данный класс реализует механизм создания решений для проверки состояния FacadeWebElement.
 *
 * Каждое решение может состоять из комбинации условий любой сложности.
 *
 * Все условия наследуют от данного класса. В классе прописаны статические методы для быстрого создания новых
 * экземпляров условий. На практике, это позволяет писать подобный код:
 *
 * $facadeWebElement
 *                 ->checkIt()
 *                 ->is(Cond::exist,
 *                      Cond::enabled,
 *                      Cond::or(Cond::exactText('Login'), Cond::exactText('Логин')));
 *
 * @package Common\Module\WFramework\Condition
 */
abstract class Cond
{
    /**
     * Название проверки.
     *
     * Одно и то же условие может быть объявлено в классе Cond под несколькими названиями.
     *
     * @var string
     */
    protected $name = 'undefined';

    /**
     * Ожидаемый результат.
     *
     * Это поле и поле $actualValue используются, только когда условие выходит за рамки простой проверки, что элемент
     * отображается/не отображается, имеет/не имеет заданный атрибут и т.п. и необходимо вывести более подробную
     * информацию для тестировщика.
     *
     * Например при проверке, того, что элемент содержит заданный текст, недостаточно просто сообщить тестировщику, что
     * проверка не прошла - нужно так же вывести актуальный текст элемента, чтобы он мог понять - что пошло не так.
     *
     * @var string
     */
    protected $expectedValue = 'undefined';

    /**
     * Актуальный результат.
     *
     * См. описание поля $expectedValue.
     *
     * @var string
     */
    protected $actualValue = 'undefined';

    /**
     * Результат выполнения проверки.
     * @var bool
     */
    protected $result = False;

    /**
     * Применяет условие к заданному FacadeWebElement.
     *
     * Все условия должны реализовывать данный метод.
     * Результат проверки условия должен сохраняться в переменную $this->result.
     *
     * @param FacadeWebElement $facadeWebElement
     * @return mixed
     */
    abstract protected function apply(FacadeWebElement $facadeWebElement);

    /**
     * Проверяет данное условие для заданного FacadeWebElement.
     *
     * @param FacadeWebElement $facadeWebElement
     * @return bool - результат проверки
     */
    public function check(FacadeWebElement $facadeWebElement) : bool
    {
        $this->apply($facadeWebElement);

        WLogger::logDebug($this->toString());

        return $this->result;
    }

    /**
     * Выводит актуальный результат в красиво-отформатированном виде.
     *
     * @return string - актуальный результат
     */
    public function printActualValue() : string
    {
        return (string) $this->actualValue;
    }

    /**
     * Выводит ожидаемый результат в красиво-отформатированном виде.
     *
     * @return string - ожидаемый результат
     */
    public function printExpectedValue() : string
    {
        return (string) $this->expectedValue;
    }

    /**
     * @return bool - результат проверки
     */
    public function getResult() : bool
    {
        return $this->result;
    }

    /**
     * @return string - название проверки
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Выводит проверку в красиво-отформатированном виде.
     *
     * @return string
     */
    public function toString() : string
    {
        return  'Условие: ' . $this->name . PHP_EOL . ' -> ' . json_encode($this->getResult()) . ' [ожидаемое: ' . $this->printExpectedValue() . ' | актуальное: ' . $this->printActualValue() . ']';
    }

    public function __construct(string $conditionName)
    {
        $this->name = $conditionName;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Отображается
     *
     * @return Cond
     */
    public static function visible() : Cond
    {
        return new Visible('visible');
    }

    /**
     * Отображается на экране (во viewport)
     *
     * @return Cond
     */
    public static function inView() : Cond
    {
        return new InView('inView');
    }

    /**
     * Существует
     *
     * @return Cond
     */
    public static function exist() : Cond
    {
        return new Exist('exist');
    }

    /**
     * Скрыт
     *
     * @return Cond
     */
    public static function hidden() : Cond
    {
        return new Hidden('hidden');
    }

    /**
     * Имеет атрибут
     *
     * @param string $attribute
     * @return Cond
     */
    public static function attribute(string $attribute) : Cond
    {
        return new Attribute('attribute', $attribute);
    }

    /**
     * Доступен только для чтения
     *
     * @return Cond
     */
    public static function readonly() : Cond
    {
        return new Attribute('readonly', 'readonly');
    }

    /**
     * Имеет атрибут со значением
     *
     * @param string $attribute
     * @param string $expectedValue
     * @return Cond
     */
    public static function attributeValue(string $attribute, string $expectedValue) : Cond
    {
        return new AttributeValue('attributeValue', $attribute, $expectedValue);
    }

    /**
     * Имеет значение (регистр и управляющие символы не учитываются, пробельные символы приводятся к одному пробелу)
     *
     * @param string $expectedValue
     * @return Cond
     */
    public static function value(string $expectedValue) : Cond
    {
        return new Value('value', $expectedValue);
    }

    /**
     * Имеет значение которое содержит подстроку (регистр и управляющие символы не учитываются, пробельные символы
     * приводятся к одному пробелу)
     *
     * @param string $expectedValue
     * @return Cond
     */
    public static function valueThatContains(string $expectedValue) : Cond
    {
        return new ValueContains('valueThatContains', $expectedValue);
    }

    /**
     * Имеет точное значение
     *
     * @param string $expectedValue
     * @return Cond
     */
    public static function exactValue(string $expectedValue) : Cond
    {
        return new ExactValue('exactValue', $expectedValue);
    }

    /**
     * Имеет значение c учётом регистра (управляющие символы не учитываются, пробельные символы приводятся к одному пробелу)
     *
     * @param string $expectedValue
     * @return Cond
     */
    public static function valueCaseSensitive(string $expectedValue) : Cond
    {
        return new ValueCaseSensitive('valueCaseSensitive', $expectedValue);
    }

    /**
     * Имеет атрибут 'name' с заданным значением
     *
     * @param string $name
     * @return Cond
     */
    public static function name(string $name) : Cond
    {
        return new AttributeValue('name','name', $name);
    }

    /**
     * Имеет атрибут 'type' с заданным значением
     *
     * @param string $type
     * @return Cond
     */
    public static function type(string $type) : Cond
    {
        return new AttributeValue('type','type', $type);
    }

    /**
     * Имеет атрибут 'id' с заданным значением
     *
     * @param string $id
     * @return Cond
     */
    public static function id(string $id) : Cond
    {
        return new AttributeValue('id','id', $id);
    }

    /**
     * Имеет видимый текст, который содержит подстроку (регистр и управляющие символы не учитываются, пробельные символы
     * приводятся к одному пробелу)
     *
     * @param string $expectedText
     * @return Cond
     */
    public static function textThatContains(string $expectedText) : Cond
    {
        return new TextContains('textThatContains', $expectedText);
    }

    /**
     * Имеет видимый текст, который соответствует регулярке
     *
     * @param string $regex
     * @return Cond
     */
    public static function textThatMatchesRegex(string $regex) : Cond
    {
        return new TextMatchesRegex('textThatMatchesRegex', $regex);
    }

    /**
     * Имеет видимый текст (регистр и управляющие символы не учитываются, пробельные символы приводятся к одному пробелу)
     *
     * @param string $expectedText
     * @return Cond
     */
    public static function text(string $expectedText) : Cond
    {
        return new Text('text', $expectedText);
    }

    /**
     * Имеет выделенный текст (регистр и управляющие символы не учитываются, пробельные символы приводятся к одному пробелу)
     *
     * @param string $expectedText
     * @return Cond
     */
    public static function selectedText(string $expectedText) : Cond
    {
        return new SelectedText('selectedText', $expectedText);
    }

    /**
     * Имеет видимый текст с учётом регистра (управляющие символы не учитываются, пробельные символы приводятся к одному пробелу)
     *
     * @param string $expectedText
     * @return Cond
     */
    public static function caseSensitiveText(string $expectedText) : Cond
    {
        return new TextCaseSensitive('caseSensitiveText', $expectedText);
    }

    /**
     * Имеет видимый текст, который точно совпадает с заданным
     *
     * @param string $expectedText
     * @return Cond
     */
    public static function exactText(string $expectedText) : Cond
    {
        return new ExactText('exactText', $expectedText);
    }

    /**
     * Имеет класс CSS с именем
     *
     * @param $className
     * @return Cond
     */
    public static function cssClass($className) : Cond
    {
        return new CssClass('cssClass', $className);
    }

    /**
     * Имеет свойство CSS с заданным значением
     *
     * @param string $property
     * @param string $expectedValue
     * @return Cond
     */
    public static function cssValue(string $property, string $expectedValue) : Cond
    {
        return new CssValue('cssValue', $property, $expectedValue);
    }

    /**
     * Находится в фокусе
     *
     * @return Cond
     */
    public static function focused() : Cond
    {
        return new Focused('focused');
    }

    /**
     * Доступен для взаимодействия
     *
     * @return Cond
     */
    public static function enabled() : Cond
    {
        return new Enabled('enabled');
    }

    /**
     * Не доступен для взаимодействия
     *
     * @return Cond
     */
    public static function disabled() : Cond
    {
        return new Disabled('disabled');
    }

    /**
     * Выбран (то же, что и checked())
     *
     * @return Cond
     */
    public static function selected() : Cond
    {
        return new Selected('selected');
    }

    /**
     * Выбран галочкой (то же, что и selected())
     *
     * @return Cond
     */
    public static function checked() : Cond
    {
        return new Selected('checked');
    }

    public static function pageLoaded() : Cond
    {
        return new PageLoaded('pageLoaded');
    }

    /**
     * Логическое И
     *
     * @param Cond ...$conditions
     * @return Cond
     */
    public static function and(Cond ...$conditions) : Cond
    {
        return new Conj('AND', ...$conditions);
    }

    /**
     * Логическое ИЛИ
     *
     * @param Cond ...$conditions
     * @return Cond
     */
    public static function or(Cond ...$conditions) : Cond
    {
        return new Disj('OR', ...$conditions);
    }

    /**
     * Логическое отрицание
     *
     * @param Cond $condition
     * @return Cond
     */
    public static function not(Cond $condition) : Cond
    {
        return new Not($condition);
    }

    /**
     * Пустой
     *
     * @return Cond
     */
    public static function empty() : Cond
    {
        return new Conj('пустой', static::value(''), static::text(''));
    }

    /**
     * Должен быть (то же, что и has())
     *
     * На самом деле, никак не влияет на результат проверки и сделан только для красоты.
     *
     * @param Cond $condition
     * @return Cond
     */
    public static function be(Cond $condition) : Cond
    {
        return new Delegate('должен быть', $condition);
    }

    /**
     * Должен иметь (то же, что и be())
     *
     * На самом деле, никак не влияет на результат проверки и сделан только для красоты.
     *
     * @param Cond $condition
     * @return Cond
     */
    public static function has(Cond $condition) : Cond
    {
        return new Delegate('должен иметь', $condition);
    }

    /**
     * Потому что
     *
     * Используется для пояснения в тестах в силу каких требований данное условие должно выполняться.
     *
     * @param string $message
     * @return Cond
     */
    public function because(string $message) : Cond
    {
        return new Explain($this, $message);
    }
}
