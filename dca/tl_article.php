<?php
/**
 * Backgroundimage
 * Extension for Contao Open Source CMS (contao.org)
 *
 * Copyright (c) 2015 POSTYOU
 *
 * @package background-image
 * @author  Gerald Meier
 * @link    http://www.postyou.de
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

// CSS for layout of file-field
if (TL_MODE == 'BE') {
    $GLOBALS['TL_CSS'][] = 'system/modules/background-image/assets/css/backend.css|screen';
}


$GLOBALS['TL_DCA']['tl_article']['palettes']['default'] =
    preg_replace('/;/', ';{backgroundImage_legend},addBackgroundImage;', $GLOBALS['TL_DCA']['tl_article']['palettes']['default'], 1);

// add Selector
$GLOBALS['TL_DCA']['tl_article']['palettes']['__selector__'][] = 'addBackgroundImage';

// add Subpalettes
$GLOBALS['TL_DCA']['tl_article']['subpalettes']['addBackgroundImage'] = 'backgroundImageFilepath,backgroundImagePos,backgroundImagePos2,backgroundImagePosTXT,backgroundImageRepeat,backgroundImageAttachment';

// Add fields
$GLOBALS['TL_DCA']['tl_article']['fields']['addBackgroundImage'] = array(
    'label'                => &$GLOBALS['TL_LANG']['tl_article']['addBackgroundImage'],
    'exclude'            => true,
    'inputType'            => 'checkbox',
    'eval'                => array('submitOnChange'=>true, 'tl_class' => 'clr w50'),
    'sql'                => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_article']['fields']['backgroundImageFilepath'] = array(
    'label'                => &$GLOBALS['TL_LANG']['tl_article']['backgroundImageFilepath'],
    'exclude'    => true,
    'inputType'    => 'fileTree',
    'explanation'    => 'backgroundImageFilepath',
    'eval'    => array('filesOnly'=>true, 'fieldType'=>'radio', 'extensions' =>'ico,jpg,jpeg,png,gif', 'mandatory'=>true, 'tl_class'=>'w50 background_image'),
    'sql'    => "binary(16) NULL"
);

$GLOBALS['TL_DCA']['tl_article']['fields']['backgroundImagePos'] = array(
    'label'                => &$GLOBALS['TL_LANG']['tl_article']['backgroundImagePos'],
    'default'            => "",
    'inputType'            => 'select',
    'options_callback' =>  array("my_tl_article","getPosOptns1"),
    'eval'                => array("doNotSaveEmpty"=>true,'tl_class' => 'w50 tl_new_short'),
    'save_callback'     => array(array("My_tl_article","saveAll")),
    'load_callback'     => array(array("My_tl_article","loadPos1")),
    'sql'                => "char(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_article']['fields']['backgroundImagePos2'] = array(
    'label'                => &$GLOBALS['TL_LANG']['tl_article']['backgroundImagePos2'],
//    'default'			=> "", if enabled db error when creating new
    'inputType'            => 'select',
    'options_callback' =>  array("my_tl_article","getPosOptns2"),
    'eval'                => array("doNotSaveEmpty"=>true,'tl_class' => 'w50 tl_new_short tl_scnd_short'),
    'load_callback'         =>array(function ($varValue, $dc) {
        $fieldName="backgroundImagePos2";
        if (isset($_POST[$fieldName]) && !empty($_POST[$fieldName])) {
            return \Input::post($fieldName);
        } elseif (isset($dc->{$fieldName})) {
            return $dc->{$fieldName};
        }
    }),
    'save_callback'     => array(function ($varValue, $dc) {
        return "";
    })
);

$GLOBALS['TL_DCA']['tl_article']['fields']['backgroundImagePosTXT'] = array(
    'label'    => &$GLOBALS['TL_LANG']['tl_article']['backgroundImagePosTXT'],
    'inputType'            => 'text',
    'eval'                => array("rgxp"=>"px","doNotSaveEmpty"=>true,'tl_class' => 'clr w50'),
    'load_callback'         =>array(function ($varValue, $dc) {
        $fieldName="backgroundImagePosTXT";
        if (isset($_POST[$fieldName]) && !empty($_POST[$fieldName])) {
            return \Input::post($fieldName);
        } elseif (isset($dc->{$fieldName})) {
            return $dc->{$fieldName};
        }
    }),
    'save_callback'     => array(function ($varValue, $dc) {
        return "";
    })
);

$GLOBALS['TL_DCA']['tl_article']['fields']['backgroundImageRepeat'] = array(
    'label'                => &$GLOBALS['TL_LANG']['tl_article']['backgroundImageRepeat'],
//    'default'			=> "", if enabled db error when creating new
    'inputType'            => 'select',
    'options_callback' =>  array("my_tl_article","getRepeatOptns"),
    'eval'                => array("doNotSaveEmpty"=>true,'tl_class' => 'clr w50'),
    'load_callback'         =>array(function ($varValue, $dc) {
        $fieldName="backgroundImageRepeat";
        if (isset($_POST[$fieldName]) && !empty($_POST[$fieldName])) {
            return \Input::post($fieldName);
        } elseif (isset($dc->{$fieldName})) {
            return $dc->{$fieldName};
        }
    }),
    'save_callback'     => array(function ($varValue, $dc) {
        return "";
    })

);

$GLOBALS['TL_DCA']['tl_article']['fields']['backgroundImageAttachment'] = array(
    'label'                => &$GLOBALS['TL_LANG']['tl_article']['backgroundImageAttachment'],
//    'default'			=> "", if enabled db error when creating new
    'inputType'            => 'select',
    'options_callback' =>  array("my_tl_article","getAttachmentOptns"),
    'eval'                => array("doNotSaveEmpty"=>true,'tl_class' => 'clr w50'),
    'load_callback'         =>array(function ($varValue, $dc) {
        $fieldName="backgroundImageAttachment";
        if (isset($_POST[$fieldName]) && !empty($_POST[$fieldName])) {
            return \Input::post($fieldName);
        } elseif (isset($dc->{$fieldName})) {
            return $dc->{$fieldName};
        }
    }),
    'save_callback'     => array(function ($varValue, $dc) {
        return "";
    })

);



class My_tl_article
{
    //nach oben background-color (Hintergrundfarbe)
//nach oben background-image (Hintergrundbild)

    public function loadPos1($varValue, $dc)
    {
        $out=deserialize($varValue, true);
        $dc->backgroundImagePos2=$out[1];
        $dc->backgroundImagePosTXT=$out[2];
        $dc->backgroundImageRepeat=$out[3];
        $dc->backgroundImageAttachment=$out[4];

        return $out[0];
    }

    public function saveAll($varValue, $dc)
    {
        $pos1=$varValue;
        $pos2=Input::post('backgroundImagePos2');
        $posTxt=Input::post('backgroundImagePosTXT');
        $rep=Input::post('backgroundImageRepeat');
        $att=Input::post('backgroundImageAttachment');

        $in=array($pos1,$pos2,$posTxt,$rep,$att);
//    var_dump($in);
        return serialize($in);
    }

    public function getRepeatOptns()
    {
        return array("","repeat","repeat-x","repeat-y","no-repeat");
    }
    public function getAttachmentOptns()
    {
        return array("","scroll","fixed");
    }
    public function getPosOptns1()
    {
        return array("","center","left","right");
    }
    public function getPosOptns2()
    {
        return array("","top","bottom","center");
    }
//nach oben background-repeat (Wiederholungs-Effekt)
//nach oben background-attachment (Wasserzeichen-Effekt)
//nach oben background-position (Hintergrundposition)
}
