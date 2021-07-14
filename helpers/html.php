<?php
namespace tradersoft\helpers;

use tradersoft\helpers\system\Translate;
use tradersoft\model\form\fields\DropDownList;
use tradersoft\model\Model;

/**
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Html
{
    const TAG_DIV = 'div';
    const TAG_SPAN = 'span';

    const OPTION_NAME = 'name';
    const OPTION_VALUE = 'value';
    const OPTION_LABEL = 'label';
    const OPTION_PLACEHOLDER = 'placeholder';
    const OPTION_ID = 'id';
    const OPTION_CLASS = 'class';
    const OPTION_DISABLED = 'disabled';
    const OPTION_SEPARATOR = 'separator';
    const OPTION_VISIBLE = 'visible';
    const OPTION_ON_CHANGE = 'onchange';
    const OPTION_ON_CLICK = 'onclick';

    const OPTION_RADIO_ITEM_RADIO_OPTIONS = 'radioOptions';
    const OPTION_RADIO_ITEM_COMMENT_OPTIONS = 'commentOptions';
    const OPTION_RCL_RADIO_NAME = 'radio';
    const OPTION_RCL_COMMENT_NAME = 'comment';

    public static $voidElements = [
        'area' => 1,
        'base' => 1,
        'br' => 1,
        'col' => 1,
        'command' => 1,
        'embed' => 1,
        'hr' => 1,
        'img' => 1,
        'input' => 1,
        'keygen' => 1,
        'link' => 1,
        'meta' => 1,
        'param' => 1,
        'source' => 1,
        'track' => 1,
        'wbr' => 1,
    ];

    public static $attributeOrder = [
        'type',
        'id',
        'class',
        'name',
        'value',

        'href',
        'src',
        'action',
        'method',

        'selected',
        'checked',
        'readonly',
        'disabled',
        'multiple',

        'size',
        'maxlength',
        'width',
        'height',
        'rows',
        'cols',

        'alt',
        'title',
        'rel',
        'media',
    ];

    public static $dataAttributes = ['data', 'data-ng', 'ng'];


    /**
     * @param array|string $action
     * @param string $method
     * @param array $options
     * @return string
     */
    public static function beginForm($action = '', $method = 'post', $options = [])
    {
        if ($action) {
            $options['action'] = $action;
        }
        if ($method) {
            $options['method'] = $method;
        }
        $form = static::beginTag('form', $options);

        return $form;
    }

    /**
     * @return string
     */
    public static function endForm()
    {
        return '</form>';
    }

    /**
     * @param string $content
     * @param array $options
     * @return string
     */
    public static function style($content, $options = [])
    {
        return static::tag('style', $content, $options);
    }

    /**
     * @param string $content
     * @param array $options
     * @return string
     */
    public static function script($content, $options = [])
    {
        return static::tag('script', $content, $options);
    }

    /**
     * @param array|string|null $url
     * @param array $options
     * @return string
     */
    public static function a($text, $url = null, $options = [])
    {
        if ($url !== null) {
            $options['href'] = $url;
        }
        return static::tag('a', $text, $options);
    }

    /**
     * @param array|string $src
     * @param array $options
     * @return string
     */
    public static function img($src, $options = [])
    {
        $options['src'] = $src;
        if (!isset($options['alt'])) {
            $options['alt'] = '';
        }
        return static::tag('img', '', $options);
    }

    /**
     * @param string $content
     * @param string $for
     * @param array $options
     * @return string label tag
     */
    public static function label($content, $for = null, $options = [])
    {
        $options['for'] = $for;
        return static::tag('label', Translate::__($content), $options);
    }

    /**
     * @param string $content
     * @param array $options
     * @return string button tag
     */
    public static function button($content = 'Button', $options = [])
    {
        if (!isset($options['type'])) {
            $options['type'] = 'button';
        }
        return static::tag('button', $content, $options);
    }

    /**
     * @param string $content
     * @param array $options
     * @return string submit button tag
     */
    public static function submitButton($content = 'Submit', $options = [])
    {
        $options['type'] = 'submit';
        return static::button($content, $options);
    }

    /**
     * @param string $content
     * @param array $options
     * @return string reset button tag
     */
    public static function resetButton($content = 'Reset', $options = [])
    {
        $options['type'] = 'reset';
        return static::button($content, $options);
    }

    /**
     * @param string $type
     * @param string $name
     * @param string $value
     * @param array $options
     * @return string input tag
     */
    public static function input($type, $name = null, $value = null, $options = [])
    {
        if (!isset($options['type'])) {
            $options['type'] = $type;
        }
        $options['name'] = $name;
        $options['value'] = $value === null ? null : (string) $value;
        return static::tag('input', '', $options);
    }

    /**
     * @param string $label
     * @param array $options
     * @return string button tag
     */
    public static function buttonInput($label = 'Button', $options = [])
    {
        $options['type'] = 'button';
        $options['value'] = $label;
        return static::tag('input', '', $options);
    }

    /**
     * @param string $label
     * @param array $options
     * @return string button tag
     */
    public static function submitInput($label = 'Submit', $options = [])
    {
        $options['type'] = 'submit';
        $options['value'] = Arr::get($options, 'value') ?: $label;
        return static::tag('input', '', $options);
    }

    /**
     * @param string $name
     * @param string $value
     * @param array $options
     * @return string input tag
     */
    public static function textInput($name, $value = null, $options = [])
    {
        return static::input('text', $name, $value, $options);
    }

    /**
     * @param string $name
     * @param string $value
     * @param array $options
     * @return string hidden input tag
     */
    public static function hiddenInput($name, $value = null, $options = [])
    {
        return static::input('hidden', $name, $value, $options);
    }

    /**
     * @param string $name
     * @param string $value
     * @param array $options
     * @return string password input tag
     */
    public static function passwordInput($name, $value = null, $options = [])
    {
        return static::input('password', $name, $value, $options);
    }

    /**
     * @param string $name
     * @param string $value
     * @param array $options
     * @return string file input tag
     */
    public static function fileInput($name, $value = null, $options = [])
    {
        return static::input('file', $name, $value, $options);
    }

    /**
     * @param string $name
     * @param string $value
     * @param array $options
     * @return string text area tag
     */
    public static function textarea($name, $value = '', $options = [])
    {
        $options['name'] = $name;
        $doubleEncode = Arr::remove($options, 'doubleEncode', true);
        return static::tag('textarea', static::encode($value, $doubleEncode), $options);
    }

    /**
     * @param string $name
     * @param bool $checked
     * @param array $options
     * @return string radio button tag
     */
    public static function radio($name, $checked = false, $options = [])
    {
        return static::booleanInput('radio', $name, $checked, $options);
    }

    /**
     * @param string $name
     * @param string|array|null $selection
     * @param array $items
     * @param array $options
     *
     * @return string
     */
    public static function radioList($name, $selection = null, $items = [], $options = [])
    {
        $itemOptions = Arr::remove($options, 'itemOptions', []);
        $encode = Arr::remove($options, 'encode', true);
        $separator = Arr::remove($options, 'separator', "\n");
        $tag = Arr::remove($options, 'tag', 'div');
        $hidden = isset($options['unselect']) ? static::hiddenInput($name, $options['unselect']) : '';
        unset($options['unselect']);

        $lines = [];
        $index = 0;
        foreach ($items as $value => $label) {
            $checked = $selection !== null &&
                (!Arr::isTraversable($selection) && !strcmp($value, $selection)
                    || Arr::isTraversable($selection) && Arr::isIn($value, $selection));

            $lines[] = static::radio($name, $checked, array_merge($itemOptions, [
                'value' => $value,
                'label' => $encode ? static::encode($label) : $label,
                'id' => $name . '_' . $index,
            ]));
            $index++;
        }
        $visibleContent = implode($separator, $lines);

        if ($tag === false) {
            return $hidden . $visibleContent;
        }

        return $hidden . static::tag($tag, $visibleContent, $options);
    }

    /**
     * @param string  $name
     * @param mixed  $selection
     * @param array $items
     * @param array $options
     *
     * @return string
     * @throws \Exception
     */
    public static function radioCommentList($name, $selection = null, $items = [], $options = [])
    {
        $separator = Arr::remove($options, static::OPTION_SEPARATOR, "\n");
        $tag = Arr::remove($options, 'tag', 'div');
        $unSelect = Arr::remove($options, 'unselect', '');

        $errorMsg = Arr::remove($options, 'error');
        $errorOptions = Arr::remove($options, 'errorOptions', []);
        $error = Html::tag(Arr::remove($errorOptions, 'tag'), Html::encode($errorMsg), $errorOptions);


        $radioName = $name . '[' . static::OPTION_RCL_RADIO_NAME . ']';
        $commentName = $name . '[' . static::OPTION_RCL_COMMENT_NAME . ']';
        $commentId = static::_cleanSpecificSymbol($commentName);

        $radioValue = Arr::get($selection, static::OPTION_RCL_RADIO_NAME);
        $commentValue = Arr::get($selection, static::OPTION_RCL_COMMENT_NAME , '');

        $hidden = static::hiddenInput($radioName, $unSelect);
        $hidden .= static::hiddenInput($commentName, $commentValue, [static::OPTION_ID => $commentId]);

        $lines = [];
        foreach ($items as $index => $itemOption) {
            $radioOptions = Arr::get($itemOption, static::OPTION_RADIO_ITEM_RADIO_OPTIONS, []);
            $commentOptions = Arr::get($itemOption, static::OPTION_RADIO_ITEM_COMMENT_OPTIONS, []);

            $radioOptions[static::OPTION_ID] = static::_cleanSpecificSymbol($radioName) . $index;
            $commentOptions[static::OPTION_ID] = static::_cleanSpecificSymbol($commentName) . $index;

            $commentOptions[static::OPTION_ON_CHANGE] = "document.getElementById('$commentId').value = this.value;";
            $commentOptions[static::OPTION_ON_CLICK] = "document.getElementById('{$radioOptions[static::OPTION_ID]}').checked=true;document.getElementById('{$radioOptions[static::OPTION_ID]}').dispatchEvent(new Event('change'));";
            $radioOptions[static::OPTION_ON_CHANGE] = "document.getElementById('$commentId').value = document.getElementById('{$commentOptions[static::OPTION_ID]}').value;";

            $value = $radioOptions[static::OPTION_VALUE];
            $checked = !is_null($selection) && $radioValue == $value;
            if ($checked) {
                $commentOptions[static::OPTION_VALUE] =  $commentValue;
            }

            $radioOptions['boxOptions'][static::OPTION_CLASS] = 'element-radio';

            $comment = static::_comment($commentOptions);
            $radio = static::radio($radioName, $checked, $radioOptions);

            $lines[] = static::tag(
                'div',
                $radio . $comment . ($checked ? $error : ''),
                [static::OPTION_CLASS=>'radio-item']
            );
            if ($checked) {
                $error = '';
            }
        }
        $visibleContent = implode($separator, $lines);
        $visibleContent .= $error;

        if ($tag === false) {
            return $hidden . $visibleContent;
        }

        return $hidden . static::tag($tag, $visibleContent, $options);
    }

    /**
     * @param string $name
     * @param bool $checked
     * @param array $options
     *
     * @return string checkbox tag
     */
    public static function checkbox($name, $checked = false, $options = [])
    {
        return static::booleanInput('checkbox', $name, $checked, $options);
    }

    /**
     * @param string $name
     * @param string|array|null $selection
     * @param array $items
     * @param array $options
     *
     * @return string
     */
    public static function dropDownList($name, $selection = null, $items = [], $options = [])
    {
        if (!empty($options['multiple'])) {
            return static::listBox($name, $selection, $items, $options);
        }
        $options['name'] = $name;
        unset($options['unselect']);
        $selectOptions = static::renderSelectOptions($selection, $items, $options);
        return static::tag('select', "\n" . $selectOptions . "\n", $options);
    }

    /**
     * @param string $name
     * @param string|array|null $selection
     * @param array $items
     * @param array $options
     *
     * @return string
     */
    public static function listBox($name, $selection = null, $items = [], $options = [])
    {
        if (!array_key_exists('size', $options)) {
            $options['size'] = 4;
        }
        if (!empty($options['multiple']) && !empty($name) && substr_compare($name, '[]', -2, 2)) {
            $name .= '[]';
        }
        $options['name'] = $name;
        if (isset($options['unselect'])) {
            if (!empty($name) && substr_compare($name, '[]', -2, 2) === 0) {
                $name = substr($name, 0, -2);
            }
            $hidden = static::hiddenInput($name, $options['unselect']);
            unset($options['unselect']);
        } else {
            $hidden = '';
        }
        $selectOptions = static::renderSelectOptions($selection, $items, $options);
        return $hidden . static::tag('select', "\n" . $selectOptions . "\n", $options);
    }

    /**
     * @param string|array|null $selection
     * @param array $items
     * @param array $tagOptions
     * @return string
     */
    public static function renderSelectOptions($selection, $items, &$tagOptions = [])
    {
        $lines = [];
        $encodeSpaces = Arr::remove($tagOptions, 'encodeSpaces', false);
        $encode = Arr::remove($tagOptions, 'encode', true);
        if (isset($tagOptions['prompt'])) {
            $promptOptions = ['value' => ''];
            if (is_string($tagOptions['prompt'])) {
                $promptText = $tagOptions['prompt'];
            } else {
                $promptText = $tagOptions['prompt']['text'];
                $promptOptions = array_merge($promptOptions, $tagOptions['prompt']['options']);
            }
            $promptText = $encode ? static::encode($promptText) : $promptText;
            if ($encodeSpaces) {
                $promptText = str_replace(' ', '&nbsp;', $promptText);
            }
            $lines[] = static::tag('option', Translate::__($promptText), $promptOptions);
        }

        $options = isset($tagOptions['options']) ? $tagOptions['options'] : [];
        $groups = isset($tagOptions['groups']) ? $tagOptions['groups'] : [];
        unset($tagOptions['prompt'], $tagOptions['options'], $tagOptions['groups']);
        $options['encodeSpaces'] = Arr::get($options, 'encodeSpaces', $encodeSpaces);
        $options['encode'] = Arr::get($options, 'encode', $encode);

        $isNeedTranslate = Arr::remove($tagOptions, DropDownList::KEY_IS_NEED_TRANSLATE, false);
        foreach ($items as $key => $value) {
            if (is_array($value)) {
                $groupAttrs = isset($groups[$key]) ? $groups[$key] : [];
                if (!isset($groupAttrs['label'])) {
                    $groupAttrs['label'] = $key;
                }
                $attrs = ['options' => $options, 'groups' => $groups, 'encodeSpaces' => $encodeSpaces, 'encode' => $encode];
                $content = static::renderSelectOptions($selection, $value, $attrs);
                $lines[] = static::tag('optgroup', "\n" . $content . "\n", $groupAttrs);
            } else {
                $attrs = isset($options[$key]) ? $options[$key] : [];
                $attrs['value'] = (string) $key;
                if (!array_key_exists('selected', $attrs)) {
                    $attrs['selected'] = $selection !== null &&
                        (!Arr::isTraversable($selection) && !strcmp($key, $selection)
                            || Arr::isTraversable($selection) && Arr::isIn($key, $selection));
                }
                $text = $encode ? static::encode($value) : $value;
                if ($isNeedTranslate) {
                    $text = Translate::__($text);
                }
                if ($encodeSpaces) {
                    $text = str_replace(' ', '&nbsp;', $text);
                }
                $lines[] = static::tag('option', $text, $attrs);
            }
        }

        return implode("\n", $lines);
    }

    /**
     * @param Model|Model[] $models
     * @param array $options
     *
     * @return string
     */
    public static function errorSummary($models, $options = [])
    {
        $header = isset($options['header']) ? $options['header'] : '<p>' . \TS_Functions::__('Please fix the following errors:') . '</p>';
        $footer = Arr::remove($options, 'footer', '');
        $encode = Arr::remove($options, 'encode', true);
        $showAllErrors = Arr::remove($options, 'showAllErrors', false);
        unset($options['header']);

        $lines = [];
        if (!is_array($models)) {
            $models = [$models];
        }
        foreach ($models as $model) {
            /* @var $model Model */
            foreach ($model->getErrors() as $errors) {
                foreach ($errors as $error) {
                    $line = $encode ? Html::encode($error) : $error;
                    if (array_search($line, $lines) === false) {
                        $lines[] = $line;
                    }
                    if (!$showAllErrors) {
                        break;
                    }
                }
            }
        }

        if (empty($lines)) {
            $content = '<ul></ul>';
            $options['style'] = isset($options['style']) ? rtrim($options['style'], ';') . '; display:none' : 'display:none';
        } else {
            $content = '<ul><li>' . implode("</li>\n<li>", $lines) . '</li></ul>';
        }
        return Html::tag('div', $header . $content . $footer, $options);
    }

    /**
     * @param Model $model
     * @param string $attribute
     * @param array $options
     *
     * @return string
     */
    public static function error($model, $attribute, $options = [])
    {
        $error = $model->getFirstError($attribute);
        $tag = Arr::remove($options, 'tag', 'div');
        $encode = Arr::remove($options, 'encode', true);
        return Html::tag($tag, $encode ? Html::encode($error) : $error, $options);
    }

    /**
     * @param string $content
     * @param bool $doubleEncode
     * @return string the encoded content
     */
    public static function encode($content='', $doubleEncode = true)
    {
        $charset = get_option('blog_charset');
        return htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE, $charset ? $charset : 'UTF-8', $doubleEncode);

    }

    /**
     * @param string $content
     * @return string
     */
    public static function decode($content)
    {
        return htmlspecialchars_decode($content, ENT_QUOTES);
    }

    /**
     * @param string|bool|null $name
     * @param string $content
     * @param array $options
     *
     * @return string HTML tag
     */
    public static function tag($name, $content = '', $options = [])
    {
        if ($name === null || $name === false) {
            return $content;
        }
        $html = "<$name" . static::renderTagAttributes($options) . '>';
        return isset(static::$voidElements[strtolower($name)]) ? $html : "$html$content</$name>";
    }

    /**
     * @param string|bool|null $name
     * @param array $options
     * @return string
     */
    public static function beginTag($name, $options = [])
    {
        if ($name === null || $name === false) {
            return '';
        }
        return "<$name" . static::renderTagAttributes($options) . '>';
    }

    /**
     * @param string|bool|null $name
     * @return string
     */
    public static function endTag($name)
    {
        if ($name === null || $name === false) {
            return '';
        }
        return "</$name>";
    }

    /**
     * @param array $attributes
     * @return string
     */
    public static function renderTagAttributes($attributes)
    {
        if (count($attributes) > 1) {
            $sorted = [];
            foreach (static::$attributeOrder as $name) {
                if (isset($attributes[$name])) {
                    $sorted[$name] = $attributes[$name];
                }
            }
            $attributes = array_merge($sorted, $attributes);
        }

        $html = '';
        foreach ($attributes as $name => $value) {
            if ($name === static::OPTION_PLACEHOLDER) {
                $value = Translate::__($value);
            }
            if (is_bool($value)) {
                if ($value) {
                    $html .= " $name";
                }
            } elseif (is_array($value)) {
                if (in_array($name, static::$dataAttributes)) {
                    foreach ($value as $n => $v) {
                        if (is_string($v)) {
                            $html .= " $name-$n=\"" . static::encode($v) . '"';
                        }
                    }
                } elseif ($name === 'class') {
                    if (empty($value)) {
                        continue;
                    }
                    $html .= " $name=\"" . static::encode(implode(' ', $value)) . '"';
                } elseif ($name === 'style') {
                    if (empty($value)) {
                        continue;
                    }
                    $html .= " $name=\"" . static::encode(static::cssStyleFromArray($value)) . '"';
                }
            } elseif ($value !== null) {
                $html .= " $name=\"" . static::encode($value) . '"';
            }
        }

        return $html;
    }

    /**
     * @param array $style
     * @return string
     */
    public static function cssStyleFromArray(array $style)
    {
        $result = '';
        foreach ($style as $name => $value) {
            $result .= "$name: $value; ";
        }
        return $result === '' ? null : rtrim($result);
    }

    /**
     * @param Model $model
     * @param string $attribute
     * @return string
     * @throws \Exception
     */
    public static function getInputId($model, $attribute)
    {
        $formName = $model->formName();
        $name = strtolower($formName . '-' . $attribute);
        return static::_cleanSpecificSymbol($name);
    }

    public static function getInputClass($attribute)
    {
        return static::_cleanSpecificSymbol($attribute);
    }

    /**
     * @param string $type
     * @param Model $model
     * @param string $attribute
     * @param array $options
     *
     * @return string input tag
     */
    public static function activeInput($type, $model, $attribute, $options = [])
    {
        $name = Arr::get($options, 'name', $attribute);
        $value = (isset($model->$attribute) && !is_null($model->$attribute))
            ? $model->$attribute
            : Arr::get($options, 'value');

        return static::input($type, $name, $value, $options);
    }

    /**
     * @param Model $model
     * @param string $attribute
     * @param array $options
     * @return string input tag
     */
    public static function activeTextarea($model, $attribute, $options = [])
    {
        $name = isset($options['name']) ? $options['name'] : $attribute;
        if (isset($options['value'])) {
            $value = $options['value'];
            unset($options['value']);
        } else {
            $value = $model->$attribute;
        }
        return static::textarea($name, $value, $options);
    }

    /**
     * @param Model $model
     * @param string $attribute
     * @param array $items
     * @param array $options
     * @return string
     */
    public static function activeDropDownList($model, $attribute, $items, $options = [])
    {
        if (empty($options['multiple'])) {
            return static::activeListInput('dropDownList', $model, $attribute, $items, $options);
        } else {
            return static::activeListInput('listBox',$model, $attribute, $items, $options);
        }
    }

    /**
     * @param string $type
     * @param Model $model
     * @param string $attribute
     * @param array $items
     * @param array $options
     * @return string
     */
    public static function activeListInput($type, $model, $attribute, $items, $options = [])
    {
        $name = Arr::remove($options, 'name', $attribute);
        $selection = isset($options['value']) ? $options['value'] : $model->getAttributeValue($attribute);
        if (!array_key_exists('unselect', $options)) {
            $options['unselect'] = '';
        }
        return static::$type($name, $selection, $items, $options);
    }

    /**
     * $options['value'] - checkbox value
     * $options['uncheck'] - checkbox default(uncheck) value
     * $options['checked'] - default param checked
     *
     * @param string $type
     * @param Model $model
     * @param string $attribute
     * @param array $options
     * @return string
     */
    public static function activeBooleanInput($type, $model, $attribute, $options = [])
    {
        $name = isset($options['name']) ? $options['name'] : $attribute;
        $value = $model->$attribute;

        if (!array_key_exists('value', $options)) {
            $options['value'] = '1';
        }
        if (!array_key_exists('uncheck', $options)) {
            $options['uncheck'] = '0';
        }

        if (array_key_exists('checked', $options) && is_null($value)) {
            $checked = $options['checked'];
        } else {
            $checked = $value == $options['value'];
        }

        return static::booleanInput($type, $name, $checked, $options);
    }

    /**
     * @param $data
     * @return string
     */
    public static function getFormLink($data)
    {
        if (empty($data['key'])) {
            return '';
        }

        $pageKey = '[' . $data['key'] . ']';
        $title = \TS_Functions::__(Arr::get($data, 'text'));
        unset($data['key'], $data['text']);

        $htmlOptions = [];
        $htmlAttrPrefix = 'html-';
        foreach ($data as $key => $value) {
            if (mb_strpos($key, $htmlAttrPrefix) === 0) {
                $htmlOptions[strtr($key, [$htmlAttrPrefix => ''])] = $value;
                unset($data[$key]);
            }
        }

        if ($pageKey == '[TS-REGISTRATION]') {
            $link = Link::getTraderRegistrationLink();
        } else {
            $link = Link::getForPageWithKey($pageKey, $data);
        }

        return static::a($title, $link, $htmlOptions);
    }

    public static function addClassToOptions($class, array $options)
    {
        $currentClass = trim(Arr::get($options, Html::OPTION_CLASS , ''));
        if(mb_strpos($currentClass, $class) !== false) {
            return $options;
        }

        $options[Html::OPTION_CLASS] =  "$currentClass $class";

        return $options;
    }

    /**
     * @param string $type
     * @param string $name
     * @param bool $checked
     * @param array $options
     * @return string checkbox tag
     */
    protected static function booleanInput($type, $name, $checked = false, $options = [])
    {
        $options['checked'] = (bool) $checked;
        $value = array_key_exists('value', $options) ? $options['value'] : '1';
        if (isset($options['uncheck'])) {
            $hidden = static::hiddenInput($name, $options['uncheck']);
            unset($options['uncheck']);
        } else {
            $hidden = '';
        }
        if (isset($options['label'])) {
            $label = $options['label'];
            $labelOptions = isset($options['labelOptions']) ? $options['labelOptions'] : [];
            $boxOptions = isset($options['boxOptions']) ? $options['boxOptions'] : [];
            $tag = Arr::remove($boxOptions, 'tag', 'div');
            unset($options['label'], $options['labelOptions']);
            $content = $hidden . static::input($type, $name, $value, $options) . static::label($label, $options['id'], $labelOptions);
            return static::tag($tag, $content, $boxOptions);
        } else {
            return $hidden . static::input($type, $name, $value, $options);
        }
    }

    protected static function _cleanSpecificSymbol($text)
    {
        return str_replace(['[]', '][', '[', ']', ' ', '.'], ['', '-', '-', '', '-', '-'], strtolower($text));
    }

    /**
     * @param array $options
     *
     * @return string
     */
    protected static function _comment(array $options)
    {
        $commentBlockOptions = [static::OPTION_CLASS=>'element-comment'];
        if (!Arr::remove($options, static::OPTION_VISIBLE , true)) {
            $commentBlockOptions['style'] = 'display:none;';
        }

        if ($commentLabel = Arr::remove($options, static::OPTION_LABEL)) {
            $commentLabel = static::label($commentLabel, $options[static::OPTION_ID]);
        }

        $commentValue = Arr::remove($options, static::OPTION_VALUE, '');
        $commentInput = static::textInput(null, $commentValue, $options);

        return static::tag('div', $commentLabel . $commentInput, $commentBlockOptions);
    }
}