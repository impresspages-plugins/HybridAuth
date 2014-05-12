/**
 * @package ImpressPages
 *
 */

var IpWidget_HybridAuth = function () {
    "use strict";

    var $this = this;

    this.widgetObject = null;
    this.confirmButton = null;
    this.popup = null;
    this.data = {};
    this.textarea = null;

    this.init = function (widgetObject, data) {

        this.widgetObject = widgetObject;
        this.data = data;
        var context = this; // set this so $.proxy would work below

        var $widgetOverlay = $('<div></div>')
            .css('position', 'absolute')
            .css('z-index', 5)
            .width(this.widgetObject.width())
            .height(this.widgetObject.height());
        this.widgetObject.prepend($widgetOverlay);
        $widgetOverlay.on('click', $.proxy(openPopup, context));

    };

    this.onAdd = function () {
        $.proxy(openPopup, this)();
    };


    var openPopup = function () {
        var context = this;
        this.popup = $('#ipsWidgetHybridAuthPopup');
        this.confirmButton = this.popup.find('.ipsConfirm');
        this.form = this.popup.find('.ipsModuleFormPublic');
        this.popup.modal(); // open modal popup

        this.popup.find('input[name=useFacebook]').prop('checked', parseInt(this.data.useFacebook));
        this.popup.find('input[name=useGoogleplus]').prop('checked', parseInt(this.data.useGoogleplus));
        this.popup.find('input[name=useGithub]').prop('checked', parseInt(this.data.useGithub));

        this.confirmButton.off().on('click', function () {
            context.form.submit();
        });
        this.form.off().on('submit', $.proxy(save, this));

    };

    var save = function (e) {
        e.preventDefault();

        var useFacebook = this.popup.find('input[name=useFacebook]');
        var useGoogleplus = this.popup.find('input[name=useGoogleplus]');
        var useGithub = this.popup.find('input[name=useGithub]');
        var data = {
            useFacebook: useFacebook.prop('checked') ? 1 : 0,
            useGoogleplus: useGoogleplus.prop('checked') ? 1 : 0,
            useGithub: useGithub.prop('checked') ? 1 : 0
        };

        this.widgetObject.save(data, 1); // save and reload widget
        this.popup.modal('hide');
    };


};


