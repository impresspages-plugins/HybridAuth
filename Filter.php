<?php
namespace Plugin\HybridAuth;

class Filter
{
    public static function User_registrationForm2_100($form)
    {
        $form = self::addSocIconsField($form);
        return $form;
    }

    public static function User_loginForm2_100($form)
    {
        $form = self::addSocIconsField($form);
        return $form;
    }

    private static function addSocIconsField($form){
        $socialLinks = \Plugin\HybridAuth\Model::getIconsForUserRegForm();
        $field = new \Ip\Form\Field\Info(
            array(
                'html' => $socialLinks
            ));
        $form->addField($field);

        return $form;
    }
}
