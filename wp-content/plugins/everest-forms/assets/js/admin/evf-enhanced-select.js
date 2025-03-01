/*global evf_enhanced_select_params */
jQuery( function( $ ) {

	function getEnhancedSelectFormatString() {
		return {
			'language': {
				errorLoading: function() {
					// Workaround for https://github.com/select2/select2/issues/4355 instead of i18n_ajax_error.
					return evf_enhanced_select_params.i18n_searching;
				},
				inputTooLong: function( args ) {
					var overChars = args.input.length - args.maximum;

					if ( 1 === overChars ) {
						return evf_enhanced_select_params.i18n_input_too_long_1;
					}

					return evf_enhanced_select_params.i18n_input_too_long_n.replace( '%qty%', overChars );
				},
				inputTooShort: function( args ) {
					var remainingChars = args.minimum - args.input.length;

					if ( 1 === remainingChars ) {
						return evf_enhanced_select_params.i18n_input_too_short_1;
					}

					return evf_enhanced_select_params.i18n_input_too_short_n.replace( '%qty%', remainingChars );
				},
				loadingMore: function() {
					return evf_enhanced_select_params.i18n_load_more;
				},
				maximumSelected: function( args ) {
					if ( args.maximum === 1 ) {
						return evf_enhanced_select_params.i18n_selection_too_long_1;
					}

					return evf_enhanced_select_params.i18n_selection_too_long_n.replace( '%qty%', args.maximum );
				},
				noResults: function() {
					return evf_enhanced_select_params.i18n_no_matches;
				},
				searching: function() {
					return evf_enhanced_select_params.i18n_searching;
				}
			}
		};
	}

	try {
		$( document.body ).on( 'evf-enhanced-select-init', function() {

			// Regular select boxes
			$( ':input.evf-enhanced-select' ).filter( ':not(.enhanced)' ).each( function() {
				var select2_args = $.extend({
					minimumResultsForSearch: 10,
					allowClear:  $( this ).data( 'allow_clear' ) ? true : false,
					placeholder: $( this ).data( 'placeholder' )
				}, getEnhancedSelectFormatString() );

				$( this ).selectWoo( select2_args ).addClass( 'enhanced' );
			});

			$( ':input.evf-enhanced-select-nostd' ).filter( ':not(.enhanced)' ).each( function() {
				var select2_args = $.extend({
					minimumResultsForSearch: 10,
					allowClear:  true,
					placeholder: $( this ).data( 'placeholder' )
				}, getEnhancedSelectFormatString() );

				$( this ).selectWoo( select2_args ).addClass( 'enhanced' );
			});

			// Setup multi-select2 with Select/Unselect All buttons.
			var SelectionAdapter, DropdownAdapter;
			$.fn.select2.amd.require(
				[
					"select2/selection/single",
					"select2/selection/placeholder",
					"select2/dropdown",
					"select2/dropdown/search",
					"select2/dropdown/attachBody",
					"select2/utils",
					"select2/selection/eventRelay",
				],
				function (
					SingleSelection,
					Placeholder,
					Dropdown,
					DropdownSearch,
					AttachBody,
					Utils,
					EventRelay
				) {
				// Add placeholder which shows current number of selections
				SelectionAdapter = Utils.Decorate(SingleSelection, Placeholder);

				// Allow to flow/fire events
				SelectionAdapter = Utils.Decorate(SelectionAdapter, EventRelay);

				// Add search box in dropdown
				DropdownAdapter = Utils.Decorate(Dropdown, DropdownSearch);

				// Add attach-body in dropdown
				DropdownAdapter = Utils.Decorate(DropdownAdapter, AttachBody);
				function UnselectAll() {}
					UnselectAll.prototype.render = function (decorated) {
						var self = this;
						var $rendered = decorated.call(this);
						var $unSelectAllButton = $(
							'<button class="btn btn-default evf-select2-unselect-all-btn" type="button">' + evf_enhanced_select_params.i18n_unselect_all + '</button>'
						);

						$unSelectAllButton.on("click", function () {
							self.$element.val([]);
							self.$element.trigger("change");
							self.trigger("close");
						});
						$rendered
							.find(".select2-dropdown")
							.prepend($unSelectAllButton);

						return $rendered;
					};

					// Add unselect all button in dropdown
					DropdownAdapter = Utils.Decorate(DropdownAdapter, UnselectAll);

					function SelectAll() {}
					SelectAll.prototype.render = function (decorated) {
						var self 			 = this;
						var $rendered 		 = decorated.call(this);
						var $selectAllButton = $(
							'<button class="btn btn-default evf-select2-select-all-btn" type="button">' + evf_enhanced_select_params.i18n_select_all + '</button>'
						);

						$selectAllButton.on("click", function () {
							var $options = self.$element.find("option");
							var values = [];

							$options.each(function () {
								values.push($(this).val());
							});
							self.$element.val(values);
							self.$element.trigger("change");
							self.trigger("close");
						});
						$rendered
							.find(".select2-dropdown")
							.prepend($selectAllButton);

						return $rendered;
					};

					// Add select all button in dropdown
					DropdownAdapter = Utils.Decorate(DropdownAdapter, SelectAll);

					var allSelect2 = $( "select.evf-select2-multiple" );


					if(0 === allSelect2.length){
						return;
					}

					allSelect2.each( function() {
						var $this = $( this );

						$this.find('option').filter(function() {
							return ( ( $(this).val() ).length == 0);
						}).remove();

						function formatResult(state) {
							if ( ! state.id ) {
								return state.text;
							}
							return $( '<div></div>' ).text( state.text ).addClass( 'wrap' );
						}

						var select2_args = $.extend({
							templateResult: formatResult,
							closeOnSelect: false,
							placeholder: $( this ).data( 'placeholder' ) || '',
							selectionAdapter: SelectionAdapter,
							dropdownAdapter:  DropdownAdapter,
							width: typeof $( this ).attr( 'style' ) !== 'undefined' &&  -1 !== $( this ).attr( 'style' ).indexOf( 'width' ) ? $( this ).css( 'width' ) : '100%',
							templateSelection: function(data) {

								if ( ! data.id ) {
									return data.text;
								}

								var selected_len = ($this.val() || []).length;
								return $this.data( 'selected_msg' ) ? $this.data( 'selected_msg' ).replace( '%qty%', selected_len ) : '';
							}
						}, getEnhancedSelectFormatString() );

						$this.selectWoo( select2_args );
					});
				}
			);

		}).trigger( 'evf-enhanced-select-init' );

		$( 'html' ).on( 'click', function( event ) {
			if ( this === event.target ) {
				$( '.evf-enhanced-select' ).filter( '.select2-hidden-accessible' ).selectWoo( 'close' );
			}
		} );
		$('.evf-enhanced-normal-select').each(function () {
			$(this).select2();
		})
	} catch( err ) {
		// If select2 failed (conflict?) log the error but don't stop other scripts breaking.
		window.console.log( err );
	}
});
