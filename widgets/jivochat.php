<?php

namespace tradersoft\widgets;

use tradersoft\widgets\base\Widget, TS_Functions, TSInit,
    tradersoft\helpers\multi_language\Multi_Language;

/**
 * Class js list widget
 */
class Jivochat extends Widget
{

    const ALL_LANGUAGES = 'all';
    const ALL_LANGUAGES_NAME = 'All';

    /**
     * @param array $args
     * @param array $instance
     */
    protected function _widget($args, $instance)
    {
        $text = $instance['text'];

        if (self::ALL_LANGUAGES == $instance['language'] || TS_Functions::getCurrentLanguage() == $instance['language']) {

            echo $this->_render(
                'jivochat',
                ['text' => $text,]
            );
        }
    }


    // Widget Backend
    public function form($instance)
    {
        $language = $instance['language'];
        $text = esc_textarea($instance['text']);

        $languages = [
                self::ALL_LANGUAGES => [
                    'code' => self::ALL_LANGUAGES,
                    'display_name' => self::ALL_LANGUAGES_NAME,
                ]
            ] + Multi_Language::getInstance()->getActiveLanguages();
        echo '<p><select name="' . $this->get_field_name('language') . '">';
        $languageName = '';
        foreach ($languages as $lang) {
            $selected = '';
            if ($language == $lang['code']) {
                $languageName = $lang['display_name'];
                $selected = ' selected="selected"';
            }
            echo '<option value="' . $lang['code'] . '"' . $selected . '>' . $lang['display_name'] . '</option>';
        }
        echo '</select></p>';

        echo '<input class="widefat" id="' . $this->get_field_id('title') . '" type="hidden" value="' . $languageName . '" disabled="disabled" />';
        echo '<p><textarea class="widefat" rows="16" cols="20" id="' . $this->get_field_id('text') . '" name="' . $this->get_field_name('text') . '">' . $text . '</textarea></p>';
    }

    public function update($new_instance, $old_instance)
    {
        $instance = $new_instance;
        return $instance;
    }

    /**
     * Return ID base for widget
     * @return string
     */
    protected function _getId()
    {
        return str_replace('\\', '_', mb_strtolower(get_class($this), 'UTF-8'));
    }

    /**
     * @return string
     */
    protected function _getName()
    {
        return \TS_Functions::__('TraderSoft Jivochat');
    }

    protected function _loadInlineScripts()
    {

//        if (self::ALL_LANGUAGES == $instance['language'] || TS_Functions::getCurrentLanguage() == $instance['language']) {

            $trader = TSInit::$app->trader;
            if (!$trader->getIsGuest()) {

                $this->_mediaFiles->addScriptInline(
                        $this->_render(
                            'js/jivochat',
                            [
                                'crmHashId' => $trader->get('crmHashId'),
                                'userName' => $trader->getFullName(),
                                'email' => $trader->get('email'),
                                'phone' => $trader->get('phone'),
                            ]
                        )
                    );
            }
//        }
    }
}


