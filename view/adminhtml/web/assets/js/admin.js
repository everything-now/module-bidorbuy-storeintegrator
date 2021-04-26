require([
    'jquery',
    'mage/translate',
    'jquery/validate'
], function(){
    window.onload = function () {

        jQuery.validator.addMethod(
            'validate-filename', function (v) {
                if (v != '' && v) {
                    return /^[A-Za-z]+[A-Za-z0-9_-]+$/.test(v);
                }
                return true;
            }, jQuery.mage.__('Please use only letters (a-z or A-Z), numbers (0-9) "-" or "_" in this field, first character should be a letter.'));

        // download/remove logs
        $$('.loggingFormButton').each(function (element) {
            element.observe('click', function () {
                $('loggingFormFilename').value = (this.readAttribute('filename'));
                $('loggingFormAction').value = (this.readAttribute('action'));
                this.up('form').submit();

                $('loggingFormAction').value = '';
            });
        });

        // copy URL
        var select_text = "#tokenExportUrl, #tokenDownloadUrl";
        var ctrlDown = false, ctrlKey = 17, cKey = 67;

        $$(select_text).each(function (e) {
            e.setStyle({cursor: 'pointer'});
            e.observe('click', function () {
                this.select();
            });
        });

        $$(".copy-button, " + select_text).each(function (e) {
            e.observe('click', function (evt) {
                $("ctrl-c-message").setStyle({
                    top: (Event.pointerY(evt) - 80) + 'px',
                    left: Event.pointerX(evt) - (parseInt(this.getStyle('width')) / 2) + 'px',
                    display: 'block'
                });
            });
        });

        document.observe('keydown', function (e) {
            if (e.keyCode == ctrlKey) {
                ctrlDown = true;
            }
            if (ctrlDown && e.keyCode == cKey) {
                $("ctrl-c-message").hide();
            }
        }).observe('keyup', function (e) {
            if (e.keyCode == ctrlKey) {
                ctrlDown = false;
            }
        });

        if ($("ctrl-c-message")) {
            // We check here for existence to avoid more code writing and checking the section
            // and embedding the .js only in case if it's a configuration page. reference name="head" (bidorbuy.xml)
            $("ctrl-c-message").observe('mouseenter', function (e) {
                this.hide();
            });
        }

        $$(".copy-button").each(function (el) {
            el.observe('click', function () {
                el.previous('input').select();
            });
        });

        $$(".launch-button").each(function (el) {
            el.observe('click', function () {
                window.open(el.previous('input').value);
            });
        });

        $('reset-tokens').observe('click', function () {
            $('resetTokens').value = 1;
        });

        // Secondhand action categories

        $("includeProductConditionSecondhandCategories").observe("click", function () {
            $$("select#productConditionNewCategories option:selected").each(function (option) {
                // newOptions[option.value]['style'] = (typeof $$(this).attr('style')) == 'undefined' ? '' : $$(this).attr('style');
                $("productConditionSecondhandCategories").insert(option);

                var secondhandFieldValue = $('bidorbuystoreintegrator_productSettings_productConditionSecondhandCategories');
                if (secondhandFieldValue.value === "") {
                    secondhandFieldValue.value = option.value
                } else {
                    secondhandFieldValue.value = secondhandFieldValue.value + ',' + option.value
                }
                //option.remove();
            });
            return false;
        });

        $("excludeProductConditionSecondhandCategories").observe("click", function () {
            var secondhandFieldValue = $('bidorbuystoreintegrator_productSettings_productConditionSecondhandCategories');
            var valuesArray = secondhandFieldValue.value.split(',');
            $$("select#productConditionSecondhandCategories option:selected").each(function (option) {
                // newOptions[option.value]['style'] = (typeof $$(this).attr('style')) == 'undefined' ? '' : $$(this).attr('style');
                $("productConditionNewCategories").insert(option);
                var index = valuesArray.indexOf(option.value);
                if (index > -1) {
                    valuesArray.splice(index, 1);
                }
            });
            secondhandFieldValue.value = valuesArray.join(',');
            return false;
        });

        // Refurbished action categories

        $("includeProductConditionRefurbishedCategories").observe("click", function () {
            $$("select#productConditionNewCategories option:selected").each(function (option) {
                // newOptions[option.value]['style'] = (typeof $$(this).attr('style')) == 'undefined' ? '' : $$(this).attr('style');
                $("productConditionRefurbishedCategories").insert(option);

                var refurbishedFieldValue = $('bidorbuystoreintegrator_productSettings_productConditionRefurbishedCategories');
                if (refurbishedFieldValue.value === "") {
                    refurbishedFieldValue.value = option.value
                } else {
                    refurbishedFieldValue.value = refurbishedFieldValue.value + ',' + option.value
                }
            });
            return false;
        });

        $("excludeProductConditionRefurbishedCategories").observe("click", function () {
            var refurbishedFieldValue = $('bidorbuystoreintegrator_productSettings_productConditionRefurbishedCategories');
            var valuesArray = refurbishedFieldValue.value.split(',');
            $$("select#productConditionRefurbishedCategories option:selected").each(function (option) {
                // newOptions[option.value]['style'] = (typeof $$(this).attr('style')) == 'undefined' ? '' : $$(this).attr('style');
                $("productConditionNewCategories").insert(option);
                var index = valuesArray.indexOf(option.value);
                if (index > -1) {
                    valuesArray.splice(index, 1);
                }
                console.log(option.value, index,valuesArray);

            });
            refurbishedFieldValue.value = valuesArray.join(',');
            return false;
        });
    };
});