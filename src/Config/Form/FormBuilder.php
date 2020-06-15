<?php

namespace srag\Plugins\SrJiraProcessHelper\Config\Form;

use ilSrJiraProcessHelperPlugin;
use ilTextInputGUI;
use srag\CustomInputGUIs\SrJiraProcessHelper\FormBuilder\AbstractFormBuilder;
use srag\CustomInputGUIs\SrJiraProcessHelper\InputGUIWrapperUIInputComponent\InputGUIWrapperUIInputComponent;
use srag\CustomInputGUIs\SrJiraProcessHelper\MultiLineNewInputGUI\MultiLineNewInputGUI;
use srag\JiraCurl\SrJiraProcessHelper\JiraCurl;
use srag\Plugins\SrJiraProcessHelper\Config\ConfigCtrl;
use srag\Plugins\SrJiraProcessHelper\Utils\SrJiraProcessHelperTrait;

/**
 * Class FormBuilder
 *
 * @package srag\Plugins\SrJiraProcessHelper\Config\Form
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class FormBuilder extends AbstractFormBuilder
{

    use SrJiraProcessHelperTrait;

    const KEY_JIRA_AUTHORIZATION = "jira_authorization";
    const KEY_JIRA_DOMAIN = "jira_domain";
    const KEY_JIRA_PASSWORD = "jira_password";
    const KEY_JIRA_USERNAME = "jira_username";
    const KEY_MAPPING = "mapping";
    const PLUGIN_CLASS_NAME = ilSrJiraProcessHelperPlugin::class;


    /**
     * @inheritDoc
     *
     * @param ConfigCtrl $parent
     */
    public function __construct(ConfigCtrl $parent)
    {
        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    protected function getButtons() : array
    {
        $buttons = [
            ConfigCtrl::CMD_UPDATE_CONFIGURE => self::plugin()->translate("save", ConfigCtrl::LANG_MODULE)
        ];

        return $buttons;
    }


    /**
     * @inheritDoc
     */
    protected function getData() : array
    {
        $data = [
            "jira"            => [
                self::KEY_JIRA_DOMAIN        => self::srJiraProcessHelper()->config()->getValue(self::KEY_JIRA_DOMAIN),
                self::KEY_JIRA_AUTHORIZATION => [
                    "value"        => self::srJiraProcessHelper()->config()->getValue(self::KEY_JIRA_AUTHORIZATION),
                    "group_values" => [
                        self::KEY_JIRA_USERNAME => self::srJiraProcessHelper()->config()->getValue(self::KEY_JIRA_USERNAME),
                        self::KEY_JIRA_PASSWORD => self::srJiraProcessHelper()->config()->getValue(self::KEY_JIRA_PASSWORD)
                    ]
                ]
            ],
            self::KEY_MAPPING => [
                self::KEY_MAPPING => self::srJiraProcessHelper()->config()->getValue(self::KEY_MAPPING)
            ]
        ];

        return $data;
    }


    /**
     * @inheritDoc
     */
    protected function getFields() : array
    {
        $jira_authorization_usernamepassword_fields = [
            self::KEY_JIRA_USERNAME => self::dic()->ui()->factory()->input()->field()->text(self::plugin()->translate(self::KEY_JIRA_USERNAME, ConfigCtrl::LANG_MODULE))->withRequired(true),
            self::KEY_JIRA_PASSWORD => self::dic()->ui()->factory()->input()->field()->password(self::plugin()->translate(self::KEY_JIRA_PASSWORD, ConfigCtrl::LANG_MODULE))->withRequired(true)
        ];
        if (self::version()->is6()) {
            $jira_authorization = self::dic()->ui()->factory()->input()->field()->switchableGroup([
                JiraCurl::AUTHORIZATION_USERNAMEPASSWORD => self::dic()
                    ->ui()
                    ->factory()
                    ->input()
                    ->field()
                    ->group($jira_authorization_usernamepassword_fields,
                        self::plugin()->translate(self::KEY_JIRA_AUTHORIZATION . "_" . JiraCurl::AUTHORIZATION_USERNAMEPASSWORD, ConfigCtrl::LANG_MODULE))
            ], self::plugin()->translate(self::KEY_JIRA_AUTHORIZATION, ConfigCtrl::LANG_MODULE))->withRequired(true);
        } else {
            $jira_authorization = self::dic()
                ->ui()
                ->factory()
                ->input()
                ->field()
                ->radio(self::plugin()->translate(self::KEY_JIRA_AUTHORIZATION, ConfigCtrl::LANG_MODULE))
                ->withRequired(true)
                ->withOption(JiraCurl::AUTHORIZATION_USERNAMEPASSWORD,
                    self::plugin()->translate(self::KEY_JIRA_AUTHORIZATION . "_" . JiraCurl::AUTHORIZATION_USERNAMEPASSWORD, ConfigCtrl::LANG_MODULE), null,
                    $jira_authorization_usernamepassword_fields);
        }

        $mapping = (new InputGUIWrapperUIInputComponent(new MultiLineNewInputGUI(self::plugin()
            ->translate(self::KEY_MAPPING, ConfigCtrl::LANG_MODULE))))->withRequired(true);
        $mapping->getInput()->setShowSort(false);
        $input = new ilTextInputGUI(self::plugin()
            ->translate(self::KEY_MAPPING . "_email_domain", ConfigCtrl::LANG_MODULE), "email_domain");
        $input->setRequired(true);
        $mapping->getInput()->addInput($input);
        $input = new ilTextInputGUI(self::plugin()
            ->translate(self::KEY_MAPPING . "_assign_jira_user", ConfigCtrl::LANG_MODULE), "assign_jira_user");
        $input->setRequired(true);
        $mapping->getInput()->addInput($input);

        $fields = [
            "jira"            => self::dic()->ui()->factory()->input()->field()->section([
                self::KEY_JIRA_DOMAIN        => self::dic()->ui()->factory()->input()->field()->text(self::plugin()->translate(self::KEY_JIRA_DOMAIN, ConfigCtrl::LANG_MODULE))->withRequired(true),
                self::KEY_JIRA_AUTHORIZATION => $jira_authorization
            ], self::plugin()->translate("jira", ConfigCtrl::LANG_MODULE)),
            self::KEY_MAPPING => self::dic()->ui()->factory()->input()->field()->section([
                self::KEY_MAPPING => $mapping
            ], self::plugin()->translate(self::KEY_MAPPING, ConfigCtrl::LANG_MODULE))
        ];

        return $fields;
    }


    /**
     * @inheritDoc
     */
    protected function getTitle() : string
    {
        return self::plugin()->translate("configuration", ConfigCtrl::LANG_MODULE);
    }


    /**
     * @inheritDoc
     */
    protected function storeData(array $data)/* : void*/
    {
        self::srJiraProcessHelper()->config()->setValue(self::KEY_JIRA_DOMAIN, strval($data["jira"][self::KEY_JIRA_DOMAIN]));
        if (self::version()->is6()) {
            self::srJiraProcessHelper()->config()->setValue(self::KEY_JIRA_AUTHORIZATION, strval($data["jira"][self::KEY_JIRA_AUTHORIZATION][0]));
            self::srJiraProcessHelper()->config()->setValue(self::KEY_JIRA_USERNAME, strval($data["jira"][self::KEY_JIRA_AUTHORIZATION][1][self::KEY_JIRA_USERNAME]));
            self::srJiraProcessHelper()->config()->setValue(self::KEY_JIRA_PASSWORD, $data["jira"][self::KEY_JIRA_AUTHORIZATION][1][self::KEY_JIRA_PASSWORD]->toString());
        } else {
            self::srJiraProcessHelper()->config()->setValue(self::KEY_JIRA_AUTHORIZATION, strval($data["jira"][self::KEY_JIRA_AUTHORIZATION]["value"]));
            self::srJiraProcessHelper()->config()->setValue(self::KEY_JIRA_USERNAME, strval($data["jira"][self::KEY_JIRA_AUTHORIZATION]["group_values"][self::KEY_JIRA_USERNAME]));
            self::srJiraProcessHelper()->config()->setValue(self::KEY_JIRA_PASSWORD, $data["jira"][self::KEY_JIRA_AUTHORIZATION]["group_values"][self::KEY_JIRA_PASSWORD]->toString());
        }
        self::srJiraProcessHelper()->config()->setValue(self::KEY_MAPPING, (array) $data[self::KEY_MAPPING][self::KEY_MAPPING]);
    }
}
