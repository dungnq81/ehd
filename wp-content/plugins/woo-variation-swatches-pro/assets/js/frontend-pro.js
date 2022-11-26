/*!
 * Variation Swatches for WooCommerce - PRO
 *
 * Author: Emran Ahmed ( emran.bd.08@gmail.com )
 * Date: 11/17/2022, 7:31:14 PM
 * Released under the GPLv3 license.
 */
/******/ (function() { // webpackBootstrap
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other entry modules.
!function() {
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

// ================================================================
// WooCommerce Variation Swatches - Pro
// ================================================================

/*global _, wp, wc_add_to_cart_variation_params, woo_variation_swatches_pro_params, woo_variation_swatches_pro_options */
(function (window) {
  'use strict';

  var Plugin = function ($) {
    return /*#__PURE__*/function () {
      function _class2(element, options, name) {
        var _this = this;

        _classCallCheck(this, _class2);

        _defineProperty(this, "defaults", {});

        _defineProperty(this, "onInit", function (event) {
          // this.init();
          _this.initFetch();
        });

        _defineProperty(this, "onAjaxAddToCart", function (event) {
          if ($(event.target).is('.loading')) {
            return false;
          }

          if ($(event.target).is('.wvs_ajax_add_to_cart')) {
            event.preventDefault();
            event.stopPropagation();

            _this.AjaxAddToCart(event.target);
          }
        });

        _defineProperty(this, "onExpandVariableItems", function (event) {
          event.preventDefault();

          _this.expandVariableItems(event);
        });

        _defineProperty(this, "onPreviewChange", function (event) {
          event.preventDefault();

          _this.$element.off('reset_data.wc-variation-form'); // this.previewChange(event.currentTarget);


          _this.fetchPreviewChange(event.currentTarget);
        });

        _defineProperty(this, "onChange", function (event) {
          // this.$element.find('input[name="variation_id"], input.variation_id').val('').trigger('change')
          // this.$element.find('.wc-no-matching-variations').remove()
          _this.$element.trigger('woocommerce_variation_select_change');

          _this.$element.trigger('check_variations'); // Custom event for when variation selection has been changed


          _this.$element.trigger('woocommerce_variation_has_changed');
        });

        _defineProperty(this, "onReset", function (event) {
          event.preventDefault();

          _this.reset();
        });

        _defineProperty(this, "onResetDisplayedVariation", function (event) {
          _this.resetDisplayedVariation();
        });

        _defineProperty(this, "onUpdateAttributes", function (event) {
          _this.updateAttributes(event);
        });

        _defineProperty(this, "onCheckVariations", function (event) {
          _this.checkVariations();
        });

        _defineProperty(this, "onVariationChanged", function (event) {
          _this.setupSwatchesItems();
        });

        _defineProperty(this, "onFoundVariation", function (event, variation, is_ajax) {
          _this.foundVariation(variation, is_ajax);
        });

        // Assign
        this.name = name;
        this.element = element;
        this.$element = $(element);
        this.settings = $.extend(true, {}, this.defaults, options);
        this.product_variations = this.$element.data('product_variations') || [];
        this.is_ajax_variation = this.product_variations.length < 1;
        this.product_id = parseInt(this.$element.data('product_id'), 10);
        this.threshold_min = parseInt(this.$element.data('threshold_min'), 10);
        this.threshold_max = parseInt(this.$element.data('threshold_max'), 10);
        this.total_children = parseInt(this.$element.data('total_children'), 10);
        this.total_attribute = parseInt(this.$element.data('total_attribute'), 10);
        this.xhr = false;
        this.previewXhr = false;
        this.loading = true;
        this.$information = this.$element.find('.wvs-archive-information');
        this.$wrapper = this.$element.closest(woo_variation_swatches_pro_options.archive_product_wrapper);
        this.$image = this.$wrapper.find(woo_variation_swatches_pro_options.archive_image_selector);
        this.$cart_button = this.$wrapper.find(woo_variation_swatches_pro_options.archive_cart_button_selector);
        this.$price = this.$wrapper.find('.price');
        this.$firstUL = this.$element.find('.variations ul:first'); // If loop_add_to_cart button available

        this.is_cart_button_available = this.$cart_button.length > 0; // this.$cart_button_html = this.$cart_button.clone().html();

        this.$price_html = this.$price.clone().html();
        this.$attributeFields = this.$element.find('.variations select');
        this.$resetVariations = this.$element.find('.wvs_archive_reset_variations');
        var single_variation_preview_selector = false;
        var single_variation_preview_selected = false;

        if (woo_variation_swatches_pro_options.enable_single_variation_preview && woo_variation_swatches_pro_options.enable_single_variation_preview_archive) {
          var _name = this.$firstUL.data('preview_attribute_name') ? this.$firstUL.data('preview_attribute_name') : this.$attributeFields.first().data('attribute_name');

          single_variation_preview_selector = ".variations select[data-attribute_name='".concat(_name, "']");
        } // Initial state.


        this.$element.off('.wc-variation-form');
        this.$element.addClass('wvs-pro-loaded'); // Events

        this.$element.on('click.wc-variation-form', '.wvs_archive_reset_variations > a', this.onReset);
        this.$element.on('change.wc-variation-form', '.variations select', this.onChange); // Start

        this.$element.on('check_variations.wc-variation-form', this.onCheckVariations);
        this.$element.on('update_variation_values.wc-variation-form', this.onUpdateAttributes);
        this.$element.on('found_variation.wc-variation-form', this.onFoundVariation);
        this.$element.on('reset_data.wc-variation-form', this.onResetDisplayedVariation);
        this.$element.on('woocommerce_variation_has_changed.wc-variation-form', this.onVariationChanged);

        if (this.is_cart_button_available) {
          this.$cart_button.on('click.wc-variation-form', this.onAjaxAddToCart);
        }

        if (this.haveSingleVariationPreview()) {
          this.$element.on('click.wc-variation-form', '.wvs_archive_reset_variations > a', this.onResetDisplayedVariation);
          this.$element.on('change.wc-variation-form', single_variation_preview_selector, this.onPreviewChange);
        }

        if ('expand' === woo_variation_swatches_pro_options.catalog_mode_behaviour) {
          this.$element.on('click.wc-variation-form', '.woo-variation-swatches-variable-item-more', this.onExpandVariableItems);
        }

        this.$element.on('woo_variation_swatches_pro_loaded.wc-variation-form', this.onInit);
        this.$element.trigger('woo_variation_swatches_pro_loaded', this);
      }

      _createClass(_class2, [{
        key: "start",
        value: function start() {
          var _this2 = this;

          // Init after gallery.
          setTimeout(function () {
            _this2.$element.trigger('check_variations'); // @TODO: Issue if "wc_variation_form" triggers


            _this2.$element.trigger('woo_variation_swatches_pro', _this2);

            _this2.swatchInit();
          }, 100);
        }
      }, {
        key: "haveSingleVariationPreview",
        value: function haveSingleVariationPreview() {
          return !woo_variation_swatches_pro_options.enable_catalog_mode && woo_variation_swatches_pro_options.enable_single_variation_preview && woo_variation_swatches_pro_options.enable_single_variation_preview_archive;
        }
      }, {
        key: "AjaxAddToCart",
        value: function AjaxAddToCart(button) {
          var $thisbutton = $(button);
          var quantity = $thisbutton.data('quantity');
          var args = this.getQueryArgs($thisbutton.attr('href'));
          var variations = this.getAttributes(args);
          var product_id = args.variation_id;

          var data = _objectSpread(_objectSpread({}, variations), {}, {
            quantity: quantity,
            product_id: product_id
          });

          $thisbutton.removeClass('added');
          $thisbutton.addClass('loading'); // Trigger event.

          $(document.body).trigger('adding_to_cart', [$thisbutton, data]);
          $.ajax({
            dataType: 'json',
            global: false,
            url: woo_variation_swatches_pro_params.wc_ajax_url.toString().replace('%%endpoint%%', 'woo_add_to_cart_variation'),
            method: 'POST',
            data: data
          }).fail(function (jqXHR, textStatus) {
            console.error("product cart data not available.", data, textStatus);
          }).done(function (response) {
            if (!response) {
              return;
            }

            if (response.error && response.product_url) {
              window.location = response.product_url;
              return;
            } // Redirect to cart option


            if (woo_variation_swatches_pro_options.cart_redirect_after_add === 'yes') {
              window.location = woo_variation_swatches_pro_options.cart_url;
              return;
            } // Trigger event so themes can refresh other areas.


            $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $thisbutton]);
          }).always(function () {});
        }
      }, {
        key: "getAttributes",
        value: function getAttributes(raw_attributes) {
          var attributes = {};

          for (var attr_name in raw_attributes) {
            if ('attribute_' === attr_name.substring(0, 10)) {
              attributes[attr_name] = raw_attributes[attr_name];
            }
          }

          return attributes;
        }
      }, {
        key: "expandVariableItems",
        value: function expandVariableItems(event) {
          $(event.currentTarget).parent().removeClass('enabled-display-limit-mode enabled-catalog-display-limit-mode');
          $(event.currentTarget).remove();
        }
      }, {
        key: "init",
        value: function init() {
          var _this3 = this;

          var limit = this.threshold_max;
          var total = this.total_children; // The Logic
          // threshold_min = 30
          // threshold_max = 200
          // total_children = 20
          // then load by html attr
          //
          // threshold_min = 30
          // threshold_max = 200
          // total_children = 100
          // then load all variations by ajax
          //
          // threshold_min = 30
          // threshold_max = 200
          // total_children = 500
          // then load selected variations only via ajax
          // Store default image

          this.defaultImage();
          this.defaultCartButton();

          if (this.isAjaxVariation() && limit >= total) {
            if (this.xhr) {
              this.xhr.abort();
            }

            if (woo_variation_swatches_pro_options.enable_archive_preloader) {
              this.$element.block({
                message: null,
                overlayCSS: {
                  background: '#FFFFFF',
                  opacity: 0.6
                }
              });
            }

            this.xhr = $.ajax({
              global: false,
              cache: true,
              // We want to cache it on browser
              url: woo_variation_swatches_pro_params.wc_ajax_url.toString().replace('%%endpoint%%', 'woo_get_variations'),
              method: 'POST',
              data: {
                product_id: this.product_id,
                is_archive: true
              }
            });
            this.xhr.fail(function (jqXHR, textStatus) {
              console.error("product archive variations not available on: ".concat(_this3.product_id, "."), textStatus);
            });
            this.xhr.done(function (variations) {
              if (variations) {
                _this3.$element.data('product_variations', variations);

                _this3.product_variations = _this3.$element.data('product_variations');
                _this3.is_ajax_variation = false;

                _this3.start();
              }
            });
            this.xhr.always(function () {
              if (woo_variation_swatches_pro_options.enable_archive_preloader) {
                _this3.$element.unblock();
              }
            });
          } else {
            this.start();
          }
        }
      }, {
        key: "initFetch",
        value: function initFetch() {
          var _this4 = this;

          var limit = this.threshold_max;
          var total = this.total_children; // The Logic
          // threshold_min = 30
          // threshold_max = 200
          // total_children = 20
          // then load by html attr
          //
          // threshold_min = 30
          // threshold_max = 200
          // total_children = 100
          // then load all variations by ajax
          //
          // threshold_min = 30
          // threshold_max = 200
          // total_children = 500
          // then load selected variations only via ajax
          // Store default image

          this.defaultImage();
          this.defaultCartButton();

          if (this.isAjaxVariation() && limit >= total) {
            if (woo_variation_swatches_pro_options.enable_archive_preloader) {
              this.$element.block({
                message: null,
                overlayCSS: {
                  background: '#FFFFFF',
                  opacity: 0.6
                }
              });
            }

            wp.apiFetch({
              path: "woo-variation-swatches/v1/archive-product/".concat(this.product_id)
            }).then(function (variations) {
              _this4.$element.data('product_variations', variations);

              _this4.product_variations = _this4.$element.data('product_variations');
              _this4.is_ajax_variation = false;

              _this4.start();
            })["catch"](function (error) {
              console.error("archive product variations fetching failed: ".concat(_this4.product_id, "."), error);
            })["finally"](function () {
              if (woo_variation_swatches_pro_options.enable_archive_preloader) {
                _this4.$element.unblock();
              }
            });
          } else {
            this.start();
          }
        }
      }, {
        key: "fetchPreviewChange",
        value: function fetchPreviewChange(el) {
          var _this5 = this;

          var attribute_name = $(el).data('attribute_name') || $(el).attr('name');
          var value = $(el).val() || '';
          var currentAttributes = {};
          var attributes = this.getChosenAttributes();

          if (value && attributes.count && attributes.count > attributes.chosenCount) {
            currentAttributes['product_id'] = this.product_id;
            currentAttributes[attribute_name] = value;
            wp.apiFetch({
              path: wp.url.addQueryArgs("/woo-variation-swatches/v1/archive-product-preview", currentAttributes)
            }).then(function (variation) {
              _this5.updatePreviewImage(variation);
            })["catch"](function (error) {
              console.error("archive product variation preview fetching failed: ".concat(_this5.product_id, "."), error);
            })["finally"](function () {});
          }
        }
      }, {
        key: "previewChange",
        value: function previewChange(el) {
          var _this6 = this;

          var attribute_name = $(el).data('attribute_name') || $(el).attr('name');
          var value = $(el).val() || '';
          var currentAttributes = {};
          var attributes = this.getChosenAttributes();

          if (value && attributes.count && attributes.count > attributes.chosenCount) {
            currentAttributes['product_id'] = this.product_id;
            currentAttributes[attribute_name] = value;
            this.previewXhr = $.ajax({
              global: false,
              url: woo_variation_swatches_pro_params.wc_ajax_url.toString().replace('%%endpoint%%', 'woo_get_preview_variation'),
              method: 'POST',
              data: currentAttributes
            });
            this.previewXhr.fail(function (jqXHR, textStatus) {
              console.error("archive product preview not available on ".concat(_this6.product_id, "."), attribute_name, textStatus);
            });
            this.previewXhr.done(function (variation) {
              // console.log(variation)
              _this6.updatePreviewImage(variation);
            });
          }
        }
      }, {
        key: "getAvailableVariations",
        value: function getAvailableVariations() {
          return this.$element.data('product_variations') || [];
        }
      }, {
        key: "toggleResetLink",
        value: function toggleResetLink(show) {
          if (show) {
            this.$resetVariations.removeClass('show hide').addClass('show');
          } else {
            this.$resetVariations.removeClass('show hide').addClass('hide');
          }
        }
      }, {
        key: "reset",
        value: function reset() {
          this.$attributeFields.val('').trigger('change');
          this.$element.trigger('reset_data');
        }
      }, {
        key: "getChosenAttributes",
        value: function getChosenAttributes() {
          var data = {};
          var count = 0;
          var chosen = 0;
          this.$attributeFields.each(function () {
            var attribute_name = $(this).data('attribute_name') || $(this).attr('name');
            var value = $(this).val() || '';

            if (value.length > 0) {
              chosen++;
            }

            count++;
            data[attribute_name] = value;
          });
          return {
            'count': count,
            'chosenCount': chosen,
            'data': data
          };
        }
      }, {
        key: "isMatch",
        value: function isMatch(variation_attributes, attributes) {
          var match = true;

          for (var attr_name in variation_attributes) {
            if (variation_attributes.hasOwnProperty(attr_name)) {
              var val1 = variation_attributes[attr_name];
              var val2 = attributes[attr_name];

              if (val1 !== undefined && val2 !== undefined && val1.length !== 0 && val2.length !== 0 && val1 !== val2) {
                match = false;
              }
            }
          }

          return match;
        }
      }, {
        key: "findMatchingVariations",
        value: function findMatchingVariations(variations, attributes) {
          var matching = [];

          for (var i = 0; i < variations.length; i++) {
            var variation = variations[i];

            if (this.isMatch(variation.attributes, attributes)) {
              matching.push(variation);
            }
          }

          return matching;
        }
      }, {
        key: "updateAttributes",
        value: function updateAttributes(event) {
          var _this7 = this;

          var attributes = this.getChosenAttributes();
          var currentAttributes = attributes.data;

          if (this.isAjaxVariation()) {
            return;
          } // Loop through selects and disable/enable options based on selections.


          this.$attributeFields.each(function (index, el) {
            var current_attr_select = $(el),
                current_attr_name = current_attr_select.data('attribute_name') || current_attr_select.attr('name'),
                show_option_none = $(el).data('show_option_none'),
                option_gt_filter = ':gt(0)',
                attached_options_count = 0,
                new_attr_select = $('<select/>'),
                selected_attr_val = current_attr_select.val() || '',
                selected_attr_val_valid = true; // Reference options set at first.

            if (!current_attr_select.data('attribute_html')) {
              var refSelect = current_attr_select.clone(); // refSelect.find('option').removeAttr('disabled attached').removeAttr('selected')

              refSelect.find('option').prop('disabled', false).prop('selected', false).removeAttr('attached').removeClass('out-of-stock'); // current_attr_select.data('attribute_options', refSelect.find('option' + option_gt_filter).get()) // Legacy data attribute.

              current_attr_select.data('attribute_html', refSelect.html());
            }

            new_attr_select.html(current_attr_select.data('attribute_html')); // The attribute of this select field should not be taken into account when calculating its matching variations:
            // The constraints of this attribute are shaped by the values of the other attributes.

            var checkAttributes = $.extend(true, {}, currentAttributes);
            checkAttributes[current_attr_name] = '';

            var variations = _this7.findMatchingVariations(_this7.getAvailableVariations(), checkAttributes); // Loop through variations.


            for (var num in variations) {
              if (typeof variations[num] !== 'undefined') {
                var variationAttributes = variations[num].attributes;

                for (var attr_name in variationAttributes) {
                  if (variationAttributes.hasOwnProperty(attr_name)) {
                    var attr_val = variationAttributes[attr_name];
                    var variation_active = '';
                    var variation_out_of_stock = false;

                    if (attr_name === current_attr_name) {
                      if (variations[num].variation_is_active) {
                        variation_active = 'enabled';
                      } // Out Of Stock Class


                      if (!variations[num].is_in_stock) {
                        variation_out_of_stock = true;
                      }

                      if (attr_val) {
                        // Decode entities and add slashes.
                        attr_val = $('<div/>').html(attr_val).text(); // Attach.
                        // new_attr_select.find('option[value="' + form.addSlashes(attr_val) + '"]').addClass('attached ' + variation_active);
                        // Attach to matching options by value. This is done to compare
                        // TEXT values rather than any HTML entities.

                        var $option_elements = new_attr_select.find('option');

                        if ($option_elements.length) {
                          for (var i = 0, len = $option_elements.length; i < len; i++) {
                            var $option_element = $($option_elements[i]);
                            var option_value = $option_element.val(); // @TODO: WORK HERE

                            if (attr_val === option_value) {
                              $option_element.addClass('attached ' + variation_active); // 1+ attributes, 1+ selected then non selected show out of stock

                              if (attributes.count > 1 && attributes.chosenCount > 0 && !selected_attr_val && variation_out_of_stock) {
                                $option_element.addClass('out-of-stock');
                              } // 1+ attributes and all selected


                              if (attributes.count > 1 && attributes.chosenCount === attributes.count && variation_out_of_stock) {
                                $option_element.addClass('out-of-stock');
                              } // 1 attribute except catalog mode

                              /*if (!woo_variation_swatches_pro_options.enable_catalog_mode && attributes.count === 1 && variation_out_of_stock) {
                                  $option_element.addClass('out-of-stock')
                              }*/


                              if (!woo_variation_swatches_pro_options.enable_clickable_out_of_stock_archive && attributes.count === 1 && variation_out_of_stock) {
                                $option_element.addClass('out-of-stock');
                              }

                              break;
                            }
                          }
                        }
                      } else {
                        // Attach all apart from placeholder.
                        new_attr_select.find('option:gt(0)').addClass('attached ' + variation_active);
                      }
                    }
                  }
                }
              }
            } // Count available options.


            attached_options_count = new_attr_select.find('option.attached').length; // Check if current selection is in attached options.

            if (selected_attr_val) {
              selected_attr_val_valid = false;

              if (0 !== attached_options_count) {
                new_attr_select.find('option.attached.enabled').each(function () {
                  var option_value = $(this).val();

                  if (selected_attr_val === option_value) {
                    selected_attr_val_valid = true;
                    return false; // break.
                  }
                });
              }
            } // Detach the placeholder if:
            // - Valid options exist.
            // - The current selection is non-empty.
            // - The current selection is valid.
            // - Placeholders are not set to be permanently visible.


            if (attached_options_count > 0 && selected_attr_val && selected_attr_val_valid && 'no' === show_option_none) {
              new_attr_select.find('option:first').remove();
              option_gt_filter = '';
            } // Detach unattached.


            new_attr_select.find('option' + option_gt_filter + ':not(.attached)').remove(); // for out of stock
            // new_attr_select.find('option' + option_gt_filter + ':not(.attached):not(.out-of-stock)').remove()
            // Finally, copy to DOM and set value.

            current_attr_select.html(new_attr_select.html());
            current_attr_select.find('option' + option_gt_filter + ':not(.enabled)').prop('disabled', true); ////current_attr_select.find('option' + option_gt_filter + ':not(.enabled):not(.out-of-stock)').prop('disabled', true)
            //current_attr_select.find('option' + option_gt_filter + ':not(.enabled)').addClass('out-of-stock')
            // Choose selected value.

            if (selected_attr_val) {
              // If the previously selected value is no longer available, fall back to the placeholder (it's going to be there).
              if (selected_attr_val_valid) {
                current_attr_select.val(selected_attr_val);
              } else {
                // current_attr_select.val('').change()
                current_attr_select.val('').trigger('change');
              }
            } else {
              current_attr_select.val(''); // No change event to prevent infinite loop.
            }
          }); // Custom event for when variations have been updated.

          this.$element.trigger('woocommerce_update_variation_values');
        }
      }, {
        key: "checkVariations",
        value: function checkVariations() {
          var _this8 = this;

          var chosenAttributes = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
          var attributes = chosenAttributes ? chosenAttributes : this.getChosenAttributes();
          var currentAttributes = attributes.data;

          if (attributes.count && attributes.count === attributes.chosenCount) {
            if (this.isAjaxVariation()) {
              // attributes based attr
              if (this.xhr) {
                this.xhr.abort();
              }

              this.$element.block({
                message: null,
                overlayCSS: {
                  background: '#FFFFFF',
                  opacity: 0.6
                }
              });
              currentAttributes.product_id = this.product_id;
              currentAttributes.custom_data = this.$element.data('custom_data');
              this.xhr = $.ajax({
                global: false,
                url: woo_variation_swatches_pro_params.wc_ajax_url.toString().replace('%%endpoint%%', 'woo_get_variation'),
                method: 'POST',
                data: currentAttributes
              });
              this.xhr.fail(function (jqXHR, textStatus) {
                console.error("product variations not available on ".concat(_this8.product_id, "."), textStatus);
              });
              this.xhr.done(function (variation) {
                if (variation) {
                  _this8.$element.trigger('found_variation', [variation, true]);
                } else {
                  _this8.$element.trigger('reset_data');

                  attributes.chosenCount = 0;
                }
              });
              this.xhr.always(function () {
                _this8.$element.unblock();
              });
            } else {
              // by html attr
              this.$element.trigger('update_variation_values');
              var variations = this.getAvailableVariations();
              var matching_variations = this.findMatchingVariations(variations, currentAttributes);
              var variation = matching_variations.shift();

              if (variation) {
                this.$element.trigger('found_variation', [variation, false]);
              } else {
                this.$element.trigger('reset_data');
                attributes.chosenCount = 0;
              }
            }
          } else {
            this.$element.trigger('update_variation_values');
            this.$element.trigger('reset_data');
          } // Show reset link.


          this.toggleResetLink(attributes.chosenCount > 0);
          this.$element.trigger('woo_variation_swatches_pro_check_variations', [attributes]);
        }
      }, {
        key: "isAjaxVariation",
        value: function isAjaxVariation() {
          return this.is_ajax_variation;
        }
      }, {
        key: "swatchInit",
        value: function swatchInit() {
          this.setupSwatchesItems();
          this.setupSwatchesEvents();
        }
      }, {
        key: "setupSwatchesItems",
        value: function setupSwatchesItems() {
          var _this9 = this;

          var self = this;
          this.$element.find('ul.variable-items-wrapper').each(function (i, element) {
            var selected = '';
            var select = $(element).parent().find('select.woo-variation-raw-select');
            var options = select.find('option');
            var disabled = select.find('option:disabled');
            var out_of_stock = select.find('option.enabled.out-of-stock');
            var current = select.find('option:selected');
            var eq = select.find('option').eq(1);
            var selects = [];
            var disabled_selects = [];
            var out_of_stocks = []; // All Options

            options.each(function () {
              if ($(this).val() !== '') {
                selects.push($(this).val());
                selected = current.length === 0 ? eq.val() : current.val();
              }
            }); // Disabled

            disabled.each(function () {
              if ($(this).val() !== '') {
                disabled_selects.push($(this).val());
              }
            }); // Out Of Stocks

            out_of_stock.each(function () {
              if ($(this).val() !== '') {
                out_of_stocks.push($(this).val());
              }
            });

            var in_stocks = _.difference(selects, disabled_selects);

            _this9.setupSwatchesItem(element, selected, in_stocks, out_of_stocks);
          });
        }
      }, {
        key: "setupSwatchesItem",
        value: function setupSwatchesItem(element, selected, in_stocks, out_of_stocks) {
          var _this10 = this;

          // Mark Selected
          $(element).find('li.variable-item').each(function (index, el) {
            var attribute_value = $(el).attr('data-value');
            var attribute_title = $(el).attr('data-title'); // Resetting LI

            $(el).removeClass('selected disabled no-stock').addClass('disabled');
            $(el).attr('aria-checked', 'false');
            $(el).attr('tabindex', '-1');
            $(el).attr('data-wvstooltip-out-of-stock', '');
            $(el).find('input.variable-item-radio-input:radio').prop('disabled', true).prop('checked', false); // Ajax variation

            if (_this10.isAjaxVariation()) {
              $(el).find('input.variable-item-radio-input:radio').prop('disabled', false);
              $(el).removeClass('selected disabled no-stock'); // Selected

              if (attribute_value === selected) {
                $(el).addClass('selected');
                $(el).attr('aria-checked', 'true');
                $(el).attr('tabindex', '0');
                $(el).find('input.variable-item-radio-input:radio').prop('disabled', false).prop('checked', true);
                $(el).trigger('wvs-item-updated', [selected, attribute_value]);
              }
            } else {
              // Default Selected
              // We can't use es6 includes for IE11
              // in_stocks.includes(attribute_value)
              // _.contains(in_stocks, attribute_value)
              // _.includes(in_stocks, attribute_value)
              if (_.includes(in_stocks, attribute_value)) {
                $(el).removeClass('selected disabled');
                $(el).removeAttr('aria-hidden');
                $(el).attr('tabindex', '0');
                $(el).find('input.variable-item-radio-input:radio').prop('disabled', false); // Selected

                if (attribute_value === selected) {
                  $(el).addClass('selected');
                  $(el).attr('aria-checked', 'true');
                  $(el).find('input.variable-item-radio-input:radio').prop('checked', true);
                  $(el).trigger('wvs-item-updated', [selected, attribute_value]);
                }
              } // Out of Stock


              if (_.includes(out_of_stocks, attribute_value) && woo_variation_swatches_pro_options.clickable_out_of_stock) {
                $(el).removeClass('disabled').addClass('no-stock');
                $(el).attr('data-wvstooltip-out-of-stock', woo_variation_swatches_pro_options.out_of_stock_tooltip_text);
              }
            }
          });
        }
      }, {
        key: "setupSwatchesEvents",
        value: function setupSwatchesEvents() {
          var _this11 = this;

          var self = this;
          this.$element.find('ul.variable-items-wrapper').each(function (i, element) {
            var select = $(element).parent().find('select.woo-variation-raw-select'); // Trigger Select event based on list

            if (woo_variation_swatches_pro_options.clear_on_reselect) {
              // Non Selected Item Should Select
              $(element).on('click.wc-variation-form', 'li.variable-item:not(.selected):not(.radio-variable-item)', function (event) {
                event.preventDefault();
                event.stopPropagation();
                var value = $(this).data('value');
                select.val(value).trigger('change');
                select.trigger('click'); // select.trigger('focusin')

                if (woo_variation_swatches_pro_options.is_mobile) {//     select.trigger('touchstart')
                } // $(this).trigger('focus') // Mobile tooltip


                $(this).trigger('wvs-selected-item', [value, select, self.$element]); // Custom Event for li
              }); // Selected Item Should un Select

              $(element).on('click.wc-variation-form', 'li.variable-item.selected:not(.radio-variable-item)', function (event) {
                event.preventDefault();
                event.stopPropagation();
                var value = $(this).val();

                if (woo_variation_swatches_pro_options.enable_catalog_mode && 'hover' === woo_variation_swatches_pro_options.catalog_mode_trigger) {
                  return false;
                }

                select.val('').trigger('change');
                select.trigger('click');
                $(this).trigger('wvs-unselected-item', [value, select, self.$element]); // Custom Event for li
              }); // RADIO
              // On Click trigger change event on Radio button

              $(element).on('click.wc-variation-form', 'input.variable-item-radio-input:radio', function (event) {
                event.stopPropagation();
                $(this).trigger('change.wc-variation-form', {
                  radioChange: true
                });
              });
              $(element).on('change.wc-variation-form', 'input.variable-item-radio-input:radio', function (event, params) {
                event.preventDefault();
                event.stopPropagation();

                if (params && params.radioChange) {
                  var value = $(this).val();
                  var is_selected = $(this).parent('li.radio-variable-item').hasClass('selected');

                  if (is_selected) {
                    select.val('').trigger('change');
                    $(this).parent('li.radio-variable-item').trigger('wvs-unselected-item', [value, select, self.$element]); // Custom Event for li
                  } else {
                    select.val(value).trigger('change');
                    $(this).parent('li.radio-variable-item').trigger('wvs-selected-item', [value, select, self.$element]); // Custom Event for li
                  }

                  select.trigger('click'); //select.trigger('focusin')

                  if (woo_variation_swatches_pro_options.is_mobile) {//    select.trigger('touchstart')
                  }
                }
              });
            } else {
              $(element).on('click.wc-variation-form', 'li.variable-item:not(.radio-variable-item)', function (event) {
                event.preventDefault();
                event.stopPropagation();
                var value = $(this).data('value');
                select.val(value).trigger('change');
                select.trigger('click'); // select.trigger('focusin')

                if (woo_variation_swatches_pro_options.is_mobile) {//   select.trigger('touchstart')
                } // $(this).trigger('focus') // Mobile tooltip


                $(this).trigger('wvs-selected-item', [value, select, self._element]); // Custom Event for li
              }); // Radio

              $(element).on('change.wc-variation-form', 'input.variable-item-radio-input:radio', function (event) {
                event.preventDefault();
                event.stopPropagation();
                var value = $(this).val();
                select.val(value).trigger('change');
                select.trigger('click'); // select.trigger('focusin')

                if (woo_variation_swatches_pro_options.is_mobile) {//   select.trigger('touchstart')
                } // Radio


                $(this).parent('li.radio-variable-item').removeClass('selected disabled no-stock').addClass('selected');
                $(this).parent('li.radio-variable-item').trigger('wvs-selected-item', [value, select, self.$element]); // Custom Event for li
              });
            } // Keyboard Access


            $(element).on('keydown.wvs', 'li.variable-item:not(.disabled)', function (event) {
              if (event.keyCode && 32 === event.keyCode || event.key && ' ' === event.key || event.keyCode && 13 === event.keyCode || event.key && 'enter' === event.key.toLowerCase()) {
                event.preventDefault();
                $(this).trigger('click');
              }
            });

            if (!woo_variation_swatches_pro_options.is_mobile && woo_variation_swatches_pro_options.enable_catalog_mode && 'hover' === woo_variation_swatches_pro_options.catalog_mode_trigger) {
              if (_this11.threshold_max < _this11.total_children) {
                $(element).on('mouseenter.wvs', 'li.variable-item:not(.radio-variable-item)', function () {
                  $(this).trigger('click');
                  $(element).off('mouseenter.wvs');
                });
              } else {
                $(element).on('mouseenter.wvs', 'li.variable-item:not(.radio-variable-item)', function (event) {
                  $(this).trigger('click');
                });
              } // linkable_attribute


              if (woo_variation_swatches_pro_options.linkable_attribute) {
                $(element).on('click.linkable', 'li.variable-item:not(.radio-variable-item)', function (event) {
                  if ('undefined' !== typeof event.originalEvent) {
                    var url = $(this).attr('data-url');
                    url ? window.location.href = url : '';
                  }
                });
              }
            }
          });
        } // End ---

      }, {
        key: "resetDisplayedVariation",
        value: function resetDisplayedVariation() {
          this.resetPrice();
          this.resetImage();
          this.resetAvailabilityInfo();
          this.resetCartButton();
        }
      }, {
        key: "foundVariation",
        value: function foundVariation(variation, is_ajax) {
          var purchasable = true;
          var template;
          var $template_html; // this.getVariation(variation, is_ajax)

          this.updateImage(variation);
          this.reAttachCatalogModeHover();

          if (!woo_variation_swatches_pro_options.enable_catalog_mode) {
            this.updateAvailabilityInfo(variation);
            this.updatePrice(variation);
          } // Enable or disable the add to cart button


          if (!variation.is_purchasable || !variation.is_in_stock || !variation.variation_is_visible) {
            purchasable = false;
          }

          if (purchasable && !woo_variation_swatches_pro_options.enable_catalog_mode) {
            this.updateCartButton(variation);
          } else {
            this.resetCartButton();
          } // Only One attribute in this product


          if (this.total_attribute === 1 && woo_variation_swatches_pro_options.enable_catalog_mode && woo_variation_swatches_pro_options.disable_catalog_mode_on_single_attribute) {
            this.updateAvailabilityInfo(variation);
            this.updatePrice(variation);

            if (purchasable) {
              this.updateCartButton(variation);
            } else {
              this.resetCartButton();
            }
          }

          this.$element.trigger('show_variation', [variation, purchasable]);
        }
      }, {
        key: "reAttachCatalogModeHover",
        value: function reAttachCatalogModeHover() {
          if (!woo_variation_swatches_pro_options.is_mobile && this.threshold_max < this.total_children && woo_variation_swatches_pro_options.enable_catalog_mode && 'hover' === woo_variation_swatches_pro_options.catalog_mode_trigger) {
            this.$element.find('ul.variable-items-wrapper').each(function (i, element) {
              $(element).one('mouseenter.wc-variation-form', 'li.variable-item:not(.radio-variable-item):not(.selected)', function () {
                $(this).trigger('click');
              });
            });
          }
        }
      }, {
        key: "updateCartButton",
        value: function updateCartButton(variation) {
          if (!this.is_cart_button_available) {
            return;
          } // console.log(JSON.stringify(variation.attributes))


          this.$cart_button.removeClass('added');
          this.$wrapper.find('.added_to_cart').remove();

          var params = _objectSpread(_objectSpread({}, this.getChosenAttributes().data), {}, {
            'add-to-cart': this.product_id,
            variation_id: variation.variation_id
          });

          this.$cart_button.attr('href', this.addQueryArgs(this.$cart_button.attr('href'), params));
          this.$cart_button.html(variation.add_to_cart_text); // this.$cart_button.attr('href', variation.add_to_cart_url)

          this.$cart_button.attr('aria-label', variation.add_to_cart_description);

          if ('yes' === woo_variation_swatches_pro_options.enable_ajax_add_to_cart) {
            this.$cart_button.addClass('wvs_ajax_add_to_cart');
          }
        }
      }, {
        key: "resetCartButton",
        value: function resetCartButton() {
          if (!this.is_cart_button_available) {
            return;
          }

          this.$cart_button.data('variation_id', '');
          this.$cart_button.data('variations', '');
          this.$cart_button.html(this.$cart_button.attr('data-o_html'));
          this.$cart_button.attr('data-product_id', this.$cart_button.attr('data-o_product_id'));
          this.$cart_button.attr('href', this.$cart_button.attr('data-o_href'));
          this.$cart_button.attr('aria-label', this.$cart_button.attr('data-o_aria-label'));
          this.$cart_button.removeClass('wvs_ajax_add_to_cart');
          this.$cart_button.removeClass('added');
          this.$wrapper.find('.added_to_cart').remove();
        }
      }, {
        key: "addQueryArgs",
        value: function addQueryArgs(url) {
          var param = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
          var createURL = new URL(url);

          for (var key in param) {
            createURL.searchParams.set(key, param[key]);
          }

          return createURL.href;
        }
      }, {
        key: "getQueryArgs",
        value: function getQueryArgs(url) {
          var createURL = new URL(url);
          return Object.fromEntries(createURL.searchParams);
        }
      }, {
        key: "updateAvailabilityInfo",
        value: function updateAvailabilityInfo(variation) {
          if (woo_variation_swatches_pro_options.archive_show_availability) {
            var $template_html;
            var template = !variation.variation_is_visible ? wp.template('wvs-unavailable-variation-template') : wp.template('wvs-variation-template');
            $template_html = template({
              variation: variation
            });
            $template_html = $template_html.replace('/*<![CDATA[*/', '');
            $template_html = $template_html.replace('/*]]>*/', '');
            this.$information.html($template_html);
          }
        }
      }, {
        key: "resetAvailabilityInfo",
        value: function resetAvailabilityInfo() {
          this.$information.html('');
        }
      }, {
        key: "updatePrice",
        value: function updatePrice(variation) {
          if (variation && variation.price_html && variation.price_html.length > 1) {
            this.$price.html(variation.price_html);
          }
        }
      }, {
        key: "resetPrice",
        value: function resetPrice() {
          this.$price.html(this.$price_html);
        }
      }, {
        key: "defaultImage",
        value: function defaultImage() {
          this.$image.attr('data-o_src', this.$image.attr('src'));

          if (this.$image.attr('srcset')) {
            this.$image.attr('data-o_srcset', this.$image.attr('srcset'));
          }

          if (this.$image.attr('sizes')) {
            this.$image.attr('data-o_sizes', this.$image.attr('sizes'));
          }
        }
      }, {
        key: "defaultCartButton",
        value: function defaultCartButton() {
          if (!this.is_cart_button_available) {
            return;
          }

          this.$cart_button.attr('data-o_html', this.$cart_button.html()); // this.$cart_button.attr('data-o_product_id', this.$cart_button.attr('data-product_id'))

          if (this.$cart_button.attr('href')) {
            this.$cart_button.attr('data-o_href', this.$cart_button.attr('href'));
          }

          if (this.$cart_button.attr('aria-label')) {
            this.$cart_button.attr('data-o_aria-label', this.$cart_button.attr('aria-label'));
          }
        }
      }, {
        key: "updateImage",
        value: function updateImage(variation) {
          if (variation && variation.image && variation.image.src && variation.image.src.length > 1) {
            this.$image.attr('src', variation.image.src);

            if (variation.image.srcset && variation.image.srcset.length > 1) {
              this.$image.attr('srcset', variation.image.srcset);
            }

            if (variation.image.sizes && variation.image.sizes.length > 1) {
              this.$image.attr('sizes', variation.image.sizes);
            }
          }
        }
      }, {
        key: "updatePreviewImage",
        value: function updatePreviewImage(variation) {
          if (variation && variation.image && variation.image.thumb_src && variation.image.thumb_src.length > 1) {
            this.$image.attr('src', variation.image.thumb_src);

            if (variation.image.srcset && variation.image.srcset.length > 1) {
              this.$image.attr('srcset', variation.image.srcset);
            }

            if (variation.image.sizes && variation.image.sizes.length > 1) {
              this.$image.attr('sizes', variation.image.sizes);
            }
          }
        }
      }, {
        key: "resetImage",
        value: function resetImage() {
          this.$image.attr('src', this.$image.attr('data-o_src'));

          if (this.$image.attr('data-o_srcset')) {
            this.$image.attr('srcset', this.$image.attr('data-o_srcset'));
          }

          if (this.$image.attr('data-o_sizes')) {
            this.$image.attr('sizes', this.$image.attr('data-o_sizes'));
          }
        }
      }, {
        key: "destroy",
        value: function destroy() {
          this.$element.off('.wc-variation-form');
          this.$element.removeClass('wvs-pro-loaded');
          this.$element.removeData(this.name);
        }
      }]);

      return _class2;
    }();
  }(jQuery);

  var jQueryPlugin = function ($) {
    return function (PluginName, ClassName) {
      $.fn[PluginName] = function (options) {
        var _this12 = this;

        for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
          args[_key - 1] = arguments[_key];
        }

        return this.each(function (index, element) {
          var $element = $(element); // let $element = $(this)

          var data = $element.data(PluginName);

          if (!data) {
            data = new ClassName($element, $.extend({}, options), PluginName);
            $element.data(PluginName, data);
          }

          if (typeof options === 'string') {
            if (_typeof(data[options]) === 'object') {
              return data[options];
            }

            if (typeof data[options] === 'function') {
              var _data;

              return (_data = data)[options].apply(_data, args);
            }
          }

          return _this12;
        });
      }; // Constructor


      $.fn[PluginName].Constructor = ClassName; // Short hand

      $[PluginName] = function (options) {
        var _$;

        for (var _len2 = arguments.length, args = new Array(_len2 > 1 ? _len2 - 1 : 0), _key2 = 1; _key2 < _len2; _key2++) {
          args[_key2 - 1] = arguments[_key2];
        }

        return (_$ = $({}))[PluginName].apply(_$, [options].concat(args));
      }; // No Conflict


      $.fn[PluginName].noConflict = function () {
        return $.fn[PluginName];
      };
    };
  }(jQuery);

  jQueryPlugin('WooVariationSwatchesPro', Plugin);
})(window);
}();
// This entry need to be wrapped in an IIFE because it need to be isolated against other entry modules.
!function() {
jQuery(function ($) {
  try {
    $(document).on('woo_variation_swatches_pro_init', function () {
      $('.wvs-archive-variations-wrapper:not(.wvs-pro-loaded)').WooVariationSwatchesPro();
    }).trigger('woo_variation_swatches_pro_init');
  } catch (err) {
    // If failed (conflict?) log the error but don't stop other scripts breaking.
    window.console.log('Variation Swatches Pro:', err);
  } // Support JetWooBuilder Smart Filter


  $(document).on('jet-filter-content-rendered.wvs', function () {
    $(document).trigger('woo_variation_swatches_pro_init');
  }); // Support for Yith Infinite Scroll

  $(document).on('yith_infs_added_elem.wvs', function () {
    $(document).trigger('woo_variation_swatches_pro_init');
  }); // Support for Astra Theme Infinite Scroll

  $(document).on('astraInfinitePaginationLoaded.wvs', function () {
    $(document).trigger('woo_variation_swatches_pro_init');
  }); // Try to cover all ajax data complete

  $(document).ajaxComplete(function (event, request, settings) {
    _.delay(function () {//  $(document).trigger('woo_variation_swatches_pro_init')
    }, 100);
  }); // Support for Jetpack's Infinite Scroll,

  $(document.body).on('post-load.wvs', function () {
    $(document).trigger('woo_variation_swatches_pro_init');
  }); // Support for Yith Ajax Filter

  $(document).on('yith-wcan-ajax-filtered.wvs', function () {
    $(document).trigger('woo_variation_swatches_pro_init');
  });
  $(document).on('yith_wcwl_reload_fragments', function () {
    $(document).trigger('woo_variation_swatches_pro_init');
  }); // Support for beRocket ajax filters

  $(document).on('berocket_ajax_products_loaded.wvs berocket_ajax_products_infinite_loaded.wvs', function () {
    $(document).trigger('woo_variation_swatches_pro_init');
  }); // FlatSome Infinite Scroll Support

  $('.shop-container .products, .infinite-scroll-wrap').on('append.infiniteScroll', function (event, response, path) {
    $(document).trigger('woo_variation_swatches_pro_init');
  }); // FacetWP Load More

  $(document).on('facetwp-loaded.wvs', function () {
    $(document.body).trigger('woo_variation_swatches_pro_init');
  }); // Savoy Load More

  $(document).on('nm_infload_after.wvs nm_ajax_shop_update_content.wvs', function () {
    $(document).trigger('woo_variation_swatches_pro_init');
  }); // WooCommerce Filter Nav

  $('body').on('aln_reloaded.wvs', function () {
    _.delay(function () {
      $(document).trigger('woo_variation_swatches_pro_init');
    }, 100);
  });
});
}();
/******/ })()
;