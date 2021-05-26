<?php

namespace srag\CustomInputGUIs\SrJiraProcessHelper\ColorPickerInputGUI;

use ilColorPickerInputGUI;
use srag\CustomInputGUIs\SrJiraProcessHelper\Template\Template;
use srag\DIC\SrJiraProcessHelper\DICTrait;

/**
 * Class ColorPickerInputGUI
 *
 * @package srag\CustomInputGUIs\SrJiraProcessHelper\ColorPickerInputGUI
 */
class ColorPickerInputGUI extends ilColorPickerInputGUI
{

    use DICTrait;

    /**
     * @inheritDoc
     */
    public function render(/*string*/ $a_mode = "") : string
    {
        $tpl = new Template("Services/Form/templates/default/tpl.property_form.html", true, true);

        $this->insert($tpl);

        $html = self::output()->getHTML($tpl);

        $html = preg_replace("/<\/div>\s*<!--/", "<!--", $html);
        $html = preg_replace("/<\/div>\s*<!--/", "<!--", $html);

        return $html;
    }
}
