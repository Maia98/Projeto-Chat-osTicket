<?php

require_once(INCLUDE_DIR.'/class.plugin.php');
require_once(INCLUDE_DIR.'/class.forms.php');


class RadiobuttonsConfig extends PluginConfig {

    // Provide compatibility function for versions of osTicket prior to
    // translation support (v1.9.4)
    function translate() {
        if (!method_exists('Plugin', 'translate')) {
            return array(
                function($x) { return $x; },
                function($x, $y, $n) { return $n != 1 ? $y : $x; },
            );
        }
        return Plugin::translate('field-radiobuttons');
    }

    function getOptions() {
        list($__, $_N) = self::translate();
        return array(
            'category' => new TextboxField(array(
                'label' => $__('Escolher categoria'),
                'hint' => $__('Em que categoria você deseja que o campo apareça.'),
                'default' => $__('Basic Fields'),
                'configuration' => array('size'=>40, 'length'=>60),
            )),
            'uninstall-method' => new ChoiceField([
                'label' => $__('Método de desinstalação'),
                'required' => false,
                'hint' => $__('Selecione o que deseja que o plugin faça quando for desinstalado.'),
                'default' => 'convert',
                'choices' => array(
                    'convert' => __('Convert radiobuttons to choices'),
                    'prevent' => __('If radiobuttons remain prevent uninstall'),
                    'warn' => __('Inform the admin if instances remain'),
                    'nothing' => __('Just remove the plugin'),
                )
            ])
        );
    }
}
