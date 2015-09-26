/**
 * @preserve jsCharting color picker - v0.1.0
* http://www.jscharting.com/
* Copyright (c) 2013 Corporate Web Solutions Ltd.
*
* Portions based on:
* jscolor, JavaScript Color Picker, 1.4.1
* GNU Lesser General Public License, http://www.gnu.org/copyleft/lesser.html
* Jan Odvarko, http://odvarko.cz
*
* */



(function() {

var $global = {name: "jscColorPicker"};

(function(g) {

	g.Class =
	{
		define: function() {
			var prototypeContent = null;
			var baseClass = null;
			var mixins = null;
			if (arguments.length == 1) {
				prototypeContent = arguments[0];
			}
			else if (arguments.length == 2) {
				baseClass = arguments[0];
				prototypeContent = arguments[1];
			}
			else if (arguments.length == 3) {
				baseClass = arguments[0];
				mixins = arguments[1];
				prototypeContent = arguments[2];
			}

			if (mixins == null) {
				mixins = [];
			}
			if (prototypeContent == null) {
				prototypeContent = {};
			}

			function theClass() {
				this.initialize.apply(this, arguments);
			}

			if (baseClass) {
				var subclass = function() { };
				subclass.prototype = baseClass.prototype;
				theClass.prototype = new subclass();
				theClass.base = baseClass.prototype;
			}

			for (var p in prototypeContent) {
				if (prototypeContent.hasOwnProperty(p)) theClass.prototype[p] = prototypeContent[p];
			}
			for (var i = 0; i < mixins.length; ++i) {
				var mixin = mixins[i];
				for (var mp in mixin) {
					if (!mixin.hasOwnProperty(mp)) continue;
					if (mp == 'initialize') continue;
					theClass.prototype[mp] = mixin[mp];
				}
			}

			if (!theClass.prototype.initialize) {
				theClass.prototype.initialize = function() { };
			}

			theClass.prototype.constructor = theClass;

			return theClass;
		},

		"abstract": function() {
			return abstractStub;
		},

		isInstanceOfType: function(obj, type, directInstanceCheck) {
			if (!(obj instanceof type)) return false;
			if (!directInstanceCheck) return true;
			return obj.constructor == type;
		}
	};

	var abstractException = "Abstract method is not overridden.";
	var abstractStub = function() {
		throw abstractException;
	};
	abstractStub.hint = function(message) {
		return function() { throw (message || abstractException); };
	};

})($global);

(function(g) {

	var arrayPrototype = Array.prototype;

	///==============================================================================================
	/// Represents array utilities.
	///==============================================================================================
	g.Array = {
		///
		/// Iterates through the array elements and executes a specified action.
		/// 
		/// @param array An array to be iterated through.
		/// 
		/// @param action An action to be executed for each element of the array. It consumes
		///   following arguments:
		///   element - an element from the source array;
		///   index - an index of the element in the array;
		///   array - a source array.
		/// 
		/// @param context - A context object for the action procedure.
		///
		forEach: function(array, action, context) {
			var nativeForEach = arrayPrototype.forEach;
			if (nativeForEach && array.forEach === nativeForEach) {
				array.forEach(action, context);
			} else {
				for (var i = 0; i < array.length; ++i) {
					action.call(context, array[i], i, array);
				}
			}
		},


		///
		/// Checks if the specified object is an array.
		/// 
		/// @param obj - An object to be tested.
		///
		isArray: function(obj) {
			if (obj === null) return false;
			if (Array.isArray) return Array.isArray(obj); // ECMAScript implementation.
			return Object.prototype.toString.call(obj) === "[object Array]";
		},
		

		///
		/// Searchs an item in the array.
		/// 
		/// @param array - An array tosearch in.
		/// 
		/// @param comparer - A function to determine if array item is matching search condition.
		/// 
		/// @return A first item matched search condition. If no items matched condition then null.
		///
		find: function(array, comparer) {
			for (var i = 0; i < array.length; ++i) {
				var item = array[i];
				if (comparer(item)) return item;
			}
			return null;
		}
	};

})($global);

(function(g) {

	var stringPrototype = String.prototype;

	///==============================================================================================
	/// Represents string utilities.
	///==============================================================================================
	g.String = {
		///
		/// Removes leading and trailing whitespaces from the string.
		/// 
		/// @param str A source string.
		/// 
		/// @result Resulted string.
		///
		trim: function(str) {
			if (stringPrototype.trim) return stringPrototype.trim.call(str);
			return str.replace(/^\s+|\s+$/g, "");
		}
	};

})($global);

(function (g, window, $) {

	///===========================================================================
	/// Represents an abstract dropdown base type.
	/// 
	/// @param container - A DOM element (normally DIV) to be used as a container
	///   for the dropdown.
	/// 
	/// @param itemsSource - A source of the dropdown items (options). Currently
	///   only array of items is supported as items source.
	///===========================================================================
	g.DropdownBase = g.Class.define({
		
		initialize: function (container, itemsSource, settings) {
			this.container = $(container);
			this.itemsSource = itemsSource;
			var me = this;

			settings = settings || {};
			this.optionsPopupZIndex = settings.optionsPopupZIndex;

			this.options = [];
			createDom.call(this);

			this.trigger.click(function() {
				me.toggleOptions();
			});
			this.container.click(function (e) {
				e.stopPropagation();
			});
			$(window.document).click(function() {
			    me.hideOptions();
             //   GLOBAL.clearDropDown_UI_IfOpened();
			    
			});
			if ("ontouchstart" in window) {
				this.container.on("touchstart", function(e) {
					e.stopPropagation();
				});
				$(window.document).on("touchstart", function() {
					me.hideOptions();
                  //  GLOBAL.clearDropDown_UI_IfOpened();
				});
			}

			if (this.itemsSource.length > 0) this.selectItem(this.itemsSource[0]);
		},
		
		///
		/// Creates DOM elements forming content of the box.
		/// Abstract method.
		/// 
		/// @param boxContainer A box container jQuery element.
		///
		createBoxContent: g.Class['abstract'](), // function() {boxContainer}
		
		///
		/// Creates DOM elements forming item in dropdown options list.
		/// Abstract method.
		/// 
		/// @param item - An item in a this.itemsSource.
		/// 
		/// @param itemIndex - An item index in a list.
		/// 
		/// @param selectItemHandler - A callback to be called when item is selecting.
		///
		createItemContent: g.Class['abstract'](), // function(item, itemIndex) {}
		
		///
		/// Is called when selected item has changed.
		/// 
		/// @param item - A new selected item.
		/// 
		/// @param oldItem - A previously selected item.
		/// 
		/// @param itemOptionElement - An element corresponded to item in a options list.
		/// 
		/// @param oldItemOptionElement - An element corresponded to previous item in a options list.
		///
		onItemSelected: g.Class['abstract'](), // function(item, oldItem, itemOptionElement, oldItemOptionElement) {}
		
		
		///
		/// Gets true if options popup is currently opened. Otherwise, returns false.
		///
		optionsOpened: function() {
			return this.optionsPopup.is(":visible");
		},

		///
		/// Shows options popup.
		///
		showOptions: function() {
			updateOptionsPosition.call(this);
			this.optionsPopup.show();
		},
		
		///
		/// Hides options popup.
		///
		hideOptions: function() {
			this.optionsPopup.hide();
		},
		
		///
		/// Toggles options popup.
		///
		toggleOptions: function() {
			if (this.optionsOpened()) {
				this.hideOptions();
			} else {
				this.showOptions();
			}
		},
		
		///
		/// Selects item.
		/// 
		/// @param item - An item to be selected;
		///
		selectItem: function(item) {
			var oldItem = this.currentItem;
			if (oldItem == item) return;
			this.currentItem = item;

			for (
				var optionElement = null, oldOptionElement = null, i = 0;
				(optionElement == null || oldOptionElement == null) && i < this.options.length;
				++i
			) {
				var option = this.options[i];
				if (option.item == item) optionElement = option.optionElement;
				if (option.item == oldItem) oldOptionElement = option.optionElement;
			}

			this.onItemSelected(item, oldItem, optionElement, oldOptionElement);
		},
		
		getOptionElementByItem: function(item) {
			for (var i = 0; i < this.options.length; ++i) {
				var option = this.options[i];
				if (option.item == item) return option.optionElement;
			}
			return null;
		},
		
		///
		/// Gets index of the item in a list.
		/// 
		/// @param item - An item object.
		///
		getItemIndex: function(item) {
			for (var i = 0; i < this.itemsSource.length; ++i) {
				if (this.itemsSource[i] == item) return i;
			}
			return null;
		},
		
		///
		/// Resizes input box elements according to container width.
		/// 
		/// @param containerWidth - A container width.
		///
		onInputBoxResize: function(containerWidth) {
			this.box.width(containerWidth);
			this.boxContainer.width(
				containerWidth - this.box.find(".left").width() - this.trigger.width()
			);
		},
		
		///
		/// Disposes resources when dropdown control is not needed any more.
		///
		dispose: function() {
			this.optionsPopup.remove();
		}
	});
	
	
	///
	/// Creates DOM for the dropdown.
	/// 
	/// @param container - a container element to host dropdown elements.
	///
	function createDom() {
		this.container.addClass("dropdown");
		this.container.append(
			"<div class='box'>" +
				"<div class='main'></div>" +
				"<div class='left'></div>" +
				"<a class='trigger'></a>" +
			"</div>"
		);
		this.box = this.container.children(".box");
		this.boxContainer = this.box.children(".main");
		this.trigger = this.box.children(".trigger");
		
		this.optionsPopup = $(
			"<div class='dropdown-options'>" +
				"<div class='top'>" +
					"<div class='right'></div>" +
				"</div>" +
				"<div class='main'>" +
					"<div class='list-wrapper'>" +
						"<div class='list'></div>" +
					"</div>" +
				"</div>" +
				"<div class='bottom'>" +
					"<div class='right'></div>" +
				"</div>" +
			"</div>"
		);
		$("body").append(this.optionsPopup);
		if (this.optionsPopupZIndex) {
			this.optionsPopup.css("z-index", this.optionsPopupZIndex);
		}
		this.optionsContainer = this.optionsPopup.find(".list");

		this.containerWidth = this.container.width();
		this.onInputBoxResize(this.containerWidth);

		this.createBoxContent(this.boxContainer);

		var me = this;

		window.setInterval(function() {
			var newWidth = me.container.width();
			if (newWidth == me.containerWidth) return;
			me.containerWidth = newWidth;
			me.onInputBoxResize(newWidth);
		}, 500);

		g.Array.forEach(this.itemsSource, function(item, index) {
			var optionElement = me.createItemContent(
				me.optionsContainer,
				item,
				index,
				function() {
					me.selectItem(item);
					me.hideOptions();
				}
			);
			me.options.push({optionElement: optionElement, item: item});
		});
	}
	
	///
	/// Updates options popup position to fit into viewport.
	///
	function updateOptionsPosition() {
		var $w = $(window);
		var viewportWidth = $w.width(), viewportHeight = $w.height();
		var viewportX = $w.scrollLeft(), viewportY = $w.scrollTop();
		var boxWidth = this.box.width(), boxHeight = this.box.height();
		var boxOffset = this.container.offset();
		var boxX = boxOffset.left, boxY = boxOffset.top;

		var defaultMaxHeight = 360;
		this.optionsContainer.css("max-height", defaultMaxHeight + "px");
		var optionsWidth = this.optionsPopup.width();
		var optionsHeight = this.optionsPopup.height();

		var optionsX = boxX - viewportX + optionsWidth > viewportWidth ?
			boxX + boxWidth - optionsWidth :
			boxX;

		var bottomSpace = viewportHeight - (boxY - viewportY + boxHeight);
		var topSpace = boxY - viewportY;
		
		var optionsY = null;

		if (bottomSpace > optionsHeight) {
			optionsY = boxY + boxHeight + 1;
		} else {
			if (topSpace > optionsHeight) {
				optionsY = boxY - optionsHeight;
			}
		}

		var minMaxHeight = 120;
		var optionsDecorationHeight = optionsHeight - defaultMaxHeight;

		if (optionsY == null) {
			if (topSpace > bottomSpace && topSpace > minMaxHeight + optionsDecorationHeight) {
				this.optionsContainer.css("max-height", (topSpace - optionsDecorationHeight) + "px");
				optionsY = boxY - topSpace;
			} else {
				bottomSpace = bottomSpace > minMaxHeight + optionsDecorationHeight + 1 ? bottomSpace : minMaxHeight;
				this.optionsContainer.css("max-height", (bottomSpace - optionsDecorationHeight - 1) + "px");
				optionsY = boxY + boxHeight + 1;
			}
		}

		this.optionsPopup.css("left", optionsX);
		this.optionsPopup.css("top", optionsY);
	}
	
})($global, window, window.jQuery);

(function (g, window, $) {

	///===========================================================================
	/// Represents a searchable dropdown control.
	/// 
	/// @param container - A DOM element (normally DIV) to be used as a container
	///   for the palette picker control.
	/// 
	/// @param itemsSource - A source of the dropdown items (options). Currently
	///   only array of items is supported as items source.
	///===========================================================================
	g.SearchableDropdown = g.Class.define(g.DropdownBase, {

		initialize: function (container, itemsSource, settings) {
			g.SearchableDropdown.base.initialize.call(this, container, itemsSource, settings);

			var me = this;
			this.blockSearchBoxUpdating = false;
			this.abortBlur = false;

			this.container.keydown(function(e) {
				onKeyDown.call(me, e);
			});
		},

		///
		/// Shows options popup.
		///
		showOptions: function() {
			g.SearchableDropdown.base.showOptions.call(this);
			scrollToCurrentItem.call(this);
		},

	///
		/// Hides options popup.
		///
		hideOptions: function() {
			g.SearchableDropdown.base.hideOptions.call(this);
			this.searchBox.val(this.currentItem == null ? "" : this.getItemDisplayText(this.currentItem));
		},
		
		///
		/// Gets text representing item in a box.
		/// 
		/// @param item - An item object.
		///
		getItemDisplayText: function(item) {
			return item.toString();
		},
		
		///
		/// Gets text to be comparing while searching.
		/// 
		/// @param item - An item object.
		///
		getItemSearchText: function(item) {
			return item.toString();
		},
		
		///
		/// Creates DOM elements forming content of the box.
		/// Overrides method of the base type.
		/// 
		/// @param boxContainer A box container jQuery element.
		///
		createBoxContent: function(boxContainer) {
			this.container.attr("tabindex", "0");
			this.container.addClass("searchable-dropdown");
			this.optionsPopup.addClass("searchable-dropdown-options");
			
			this.searchBox = $("<input class='search-box' type='text' />");
			boxContainer.append(this.searchBox);

			var me = this;

			this.container.mousedown(function(e) {
				if (e.target != me.searchBox[0] && me.searchBox.is(":focus")) {
					me.abortBlur = true;
				}
			});
			this.searchBox.on("blur", function() {
				if (me.abortBlur) {
					me.abortBlur = false;
					return;
				}
				window.setTimeout(
					function() {
						me.abortBlur || me.hideOptions();
						me.abortBlur = false;
					},
					0
				);
			});

			this.searchBox.on("paste", function() {
				search.call(me, me.searchBox.val());
			});
			this.searchBox.on("keyup", function() {
				search.call(me, me.searchBox.val());
			});
		},
		
		///
		/// Is called when selected item has changed. Updates search box with selected item.
		/// 
		/// @param item - A new selected item.
		///
		onItemSelected: function(item) {
			if (this.blockSearchBoxUpdating) return;
			this.searchBox.val(item == null ? "" : this.getItemDisplayText(item));
		}
	});
	

	function search(text) {
		var me = this;

		text = text.toLowerCase();
		var foundItem = g.Array.find(this.itemsSource, function(item) {
			var itemText = me.getItemSearchText(item).toLowerCase();
			return itemText.indexOf(text) >= 0;
		});
		if (foundItem == null || foundItem == this.currentItem) return;

		this.blockSearchBoxUpdating = true;
		this.selectItem(foundItem);
		this.blockSearchBoxUpdating = false;
		
		this.showOptions();
	}
	
	function scrollToCurrentItem() {
		var optionElement = this.getOptionElementByItem(this.currentItem);
		if (optionElement == null) return;

		this.optionsContainer.scrollTop(getOptionElementYOffset.call(this, optionElement));
	}
	
	function getOptionElementYOffset(optionElement) {
		return optionElement[0].offsetTop - this.optionsContainer.children()[0].offsetTop;
	}
	
	function makeItemVisible(item) {
		var optionElement = this.getOptionElementByItem(item);
		if (optionElement == null) return;
		
		var optionsListScrollTop = this.optionsContainer.scrollTop();
		var optionsListHeight = this.optionsContainer.height();
		var optionTopOffset = getOptionElementYOffset.call(this, optionElement);
		var optionHeight = optionElement.outerHeight(true);

		if (optionsListScrollTop > optionTopOffset) {
			this.optionsContainer.scrollTop(optionTopOffset);
			return;
		}
		
		if (optionsListScrollTop + optionsListHeight < optionTopOffset + optionHeight) {
			this.optionsContainer.scrollTop(optionTopOffset + optionHeight - optionsListHeight);
		}
	}
	

	function onKeyDown(event) {
		switch (event.which) {
			case 38: // Up arrow
				moveToPreviousItem.call(this, event);
				break;
			case 40: // Down arrow
				moveToNextItem.call(this, event);
				break;
			case 27: // Esc
				this.hideOptions();
				break;
			case 13: // Enter
				this.toggleOptions();
				break;
		}
	}
	
	function moveToNextItem(event) {
		moveCurrentItemBy.call(this, 1, event);
	}
	
	function moveToPreviousItem(event) {
		moveCurrentItemBy.call(this, -1, event);
	}
	
	function moveCurrentItemBy(offset, event) {
		event.stopPropagation();
		event.preventDefault();

		this.abortBlur = true;
		this.container.focus();
		var index = this.getItemIndex(this.currentItem);
		var targetIndex = index + offset;
		if (index == null || targetIndex < 0 || targetIndex >= this.itemsSource.length) return;
		var targetItem = this.itemsSource[targetIndex];
		this.selectItem(targetItem);
		makeItemVisible.call(this, targetItem);
	}

})($global, window, window.jQuery);

if (typeof window.Jsc === "undefined") {
	window.Jsc = {};
}

(function(g) {

	///==============================================================================================
	/// Represents a base abstract type for the color format which is supported for the
	/// color picker input value.
	///==============================================================================================
	g.ColorFormatBase = g.Class.define({

		///
		/// Parses string representation of the color into RGBA array.
		/// 
		/// @param strColor A string representation of the color to be parsed.
		/// 
		/// @returns An RGBA array representing parsed color or null if the color cannot be parsed.
		///
		parseColor: g.Class['abstract'](),

		///
		/// Gets string representation of the color represented by RGBA array.
		/// 
		/// @param color An RGBA array representing color.
		/// 
		/// @returns A string representation of the color in a current format.
		///
		formatColor: g.Class['abstract']()
	});


	///===============================================================================================
	/// Represents an object that manages color format in color picker.
	/// 
	/// @param colorFormats Supported color formats configuration. Configuration has the
	///   following structure:
	///   {
	///     {name: "hex", colorFormat: new HexColorFormat()},
	///     {name: "rgba", colorFormat: new RgbaColorFormat()},
	///     {name: "named", colorFormat: new NamedColorFormat(), fallback: "hex"}
	///   }
	/// 
	/// @param colorSetter A color setter function from owner. Consumes two arguments:
	///   rgba - an rgba array representing the color to be set;
	///   options - options passed to setColorFromString.
	/// 
	/// @param colorGetter A color getter function from owner. Gets color as rgba array.
	///===============================================================================================
	g.ColorFormatManager = g.Class.define({
		initialize: function (colorFormats, colorSetter, colorGetter) {
			if (!colorFormats || colorFormats.length == null) {
				throw "Color formats are not specified.";
			}
			if (!colorSetter) {
				throw "Color setter is not specified";
			}
			if (!colorGetter) {
				throw "Color getter is not specified";
			}

			this.colorFormats = colorFormats;
			this.colorSetter = colorSetter;
			this.colorGetter = colorGetter;
		},
		

		///
		/// Sets color from string.
		/// 
		/// @param strColor A string representation of the color.
		/// 
		/// @returns Returns true if the color was actually set. Otherwise, false.
		///
		setColorFromString: function (strColor, options) {
			for (var i = 0; i < this.colorFormats.length; ++i) {
				var colorFormatsItem = this.colorFormats[i];
                var color = null;
                   try{

                        color = colorFormatsItem.colorFormat.parseColor(strColor);
                   }catch(e1){
                       color = null;
                   }

				if (color == null) continue;

				this.setColor(color, strColor, colorFormatsItem, options);
				return true;
			}
			return false;
		},


		///
		/// Sets color.
		/// 
		/// @param rgba An rgba array representing the color.
		/// 
		/// @param strColor A color string that was passed from outside.
		/// 
		/// @param colorFormat A color format item that has parsed color string.
		/// 
		/// @param options Options that was passed to setColorFromString.
		///
		setColor: function(rgba, strColor, colorFormat, options) {
			options = options || {};
			this.currentColor = rgba;
			this.currentColorFormat = colorFormat;
			this.colorSetter(rgba, options);
		},


		///
		/// Gets formatted color string.
		///
		getColorString: function() {
			var rgba = this.colorGetter();
			
			for (
				var colorFormat = this.currentColorFormat || this.colorFormats[0];
				colorFormat != null;
				colorFormat = this.getColorFormatByName(colorFormat.fallback)
			) {
					var formattedColor = colorFormat.colorFormat.formatColor(rgba);
					if (formattedColor != null) return formattedColor;
			}
			
			return "";
		},
		
		
		///
		/// Gets color format by name.
		/// 
		/// @param name A color format name.
		///
		getColorFormatByName: function(name) {
			if (name == null) return null;
			for (var i = 0; i < this.colorFormats.length; ++i) {
				if (this.colorFormats[i].name == name) return this.colorFormats[i];
			}
			return null;
		}

	});

})($global);

(function(g) {

	///==============================================================================================
	/// Represents a hexadecimal color format.
	/// E.g. "#AA04FF" or "AA04FF" of "#aa04ff" etc. or '#A5F' (short format)
	/// 
	/// @param options Options for the hesadecimal color format. Supported options:
	///  tolerateHashPrefix - a boolean flag which shows whether the '#' symbol is
	///    acceptable as a prefix for hexadecimal value while parsing. Default is false.
	///  formatWithHashPrefix - a boolean flag which shows whether the '#' symbol is
	///    using as a prefix for hexadecimal value while formatting.  Default is false.
	///  formatInLowerCase - a boolean flag which shows whether the color is formatted in
	///    lower case.  Default is false.
	///  dontFormatTransparent - a boolean flag representing whether the transparent color will
	///    fail formatting (formatColor returns null). Default is false.
	///==============================================================================================
	g.HexColorFormat = g.Class.define(g.ColorFormatBase, {

		initialize: function(options) {
			options = options || {};
			this.tolerateHashPrefix = options.tolerateHashPrefix || false;
			this.formatWithHashPrefix = options.formatWithHashPrefix || false;
			this.formatInLowerCase = options.formatInLowerCase || false;
			this.dontFormatTransparent = options.dontFormatTransparent || false;
		},

		///
		/// Parses hexadecimal string representation of the color into RGBA array.
		/// 
		/// @param strColor A hexadecimal string representation of the color to be parsed.
		/// 
		/// @returns An RGBA array representing parsed color or null if the color cannot be parsed.
		///
		parseColor: function (strColor) {
			if (!strColor) return null;

			var regex = this.tolerateHashPrefix ? /^#?([0-9A-F]{3}([0-9A-F]{3})?)$/i : /^([0-9A-F]{3}([0-9A-F]{3})?)$/i;
			var matches = strColor.match(regex);
			if (!matches || matches.length < 2) return null;

			// normal 6-length format
			var matched = matches[1];
			if (matched.length == 6) {
				return [
					parseHex(matched.substr(0, 2)) / maxColorComponent,
					parseHex(matched.substr(2, 2)) / maxColorComponent,
					parseHex(matched.substr(4, 2)) / maxColorComponent,
					1
				];
			}

			// short 3-length format
			var rComponent = matched.charAt(0), gComponent = matched.charAt(1), bComponent = matched.charAt(2);
			return [
				parseHex(rComponent + rComponent) / maxColorComponent,
				parseHex(gComponent + gComponent) / maxColorComponent,
				parseHex(bComponent + bComponent) / maxColorComponent,
				1
			];
		},


		///
		/// Gets hexadecimal string representation of the color represented by RGBA array.
		/// 
		/// @param color An RGBA array representing color.
		/// 
		/// @returns A hexadecimal string representation of the color.
		///
		formatColor: function(color) {
			if (!g.Array.isArray(color) || color.length < 4) return null;
			
			if (this.dontFormatTransparent && color[3] < 1) return null;

			var result =
				formatHex(0x100 | Math.round(maxColorComponent*color[0])).substr(1) +
				formatHex(0x100 | Math.round(maxColorComponent*color[1])).substr(1) +
				formatHex(0x100 | Math.round(maxColorComponent*color[2])).substr(1);

			if (this.formatWithHashPrefix) result = "#" + result;
			if (!this.formatInLowerCase) result = result.toUpperCase();

			return result;
		}
	});


	var maxColorComponent = 255;

	function parseHex(str) {
		return parseInt(str, 16);
	}
	
	function formatHex(value) {
		return value.toString(16);
	}

})($global);

(function(g) {

	///==============================================================================================
	/// Represents an RGB color format.
	/// E.g. "rgb(255, 0, 0)" or "rgba(255, 0, 0, 0.8)".
	///==============================================================================================
	g.RgbColorFormat = g.Class.define(g.ColorFormatBase, {

		///
		/// Parses RGB string representation of the color into RGBA array.
		/// 
		/// @param strColor An RGB string representation of the color to be parsed.
		/// 
		/// @returns An RGBA array representing parsed color or null if the color cannot be parsed.
		///
		parseColor: function (strColor) {
			if (!strColor) return null;

			var rgbaMatches = strColor.match(rgbaRegex), rgbMatches = null;
			if (rgbaMatches == null) {
				rgbMatches = strColor.match(rgbRegex);
			}

			if (rgbaMatches != null) {
				return [
					parseColorComponent(rgbaMatches[1]),
					parseColorComponent(rgbaMatches[2]),
					parseColorComponent(rgbaMatches[3]),
					parseAlpha(rgbaMatches[4])
				];
			}
			
			if (rgbMatches != null) {
				return [
					parseColorComponent(rgbMatches[1]),
					parseColorComponent(rgbMatches[2]),
					parseColorComponent(rgbMatches[3]),
					1
				];
			}

			return null;
		},


		///
		/// Gets RGB string representation of the color represented by RGBA array.
		/// 
		/// @param color An RGBA array representing color.
		/// 
		/// @returns An RGB string representation of the color.
		///
		formatColor: function(color) {
			if (!g.Array.isArray(color) || color.length < 4) return null;
			
			if (color[3] == 1) {
				return "rgb(" +
					formatColorComponent(color[0]) + ", " +
					formatColorComponent(color[1]) + ", " +
					formatColorComponent(color[2]) + ")";
			}
			
			return "rgba(" +
				formatColorComponent(color[0]) + ", " +
				formatColorComponent(color[1]) + ", " +
				formatColorComponent(color[2]) + ", " +
				formatAlpha(color[3]) + ")";
		}
	});


	var rgbRegex = /^rgb\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*\)$/;
	var rgbaRegex = /^rgba\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]?(?:\.[0-9]+)?)\s*\)$/;
	
	var maxColorComponent = 255;

	function parseColorComponent(str) {
		var component = parseInt(str, 10);
		if (isNaN(component)) return 0;
		if (component > maxColorComponent) return 1;
		if (component < 0) return 0;
		return component / maxColorComponent;
	}
	
	function parseAlpha(str) {
		var alpha = parseFloat(str, 10);
		if (isNaN(alpha)) return 1;
		if (alpha > 1) return 1;
		if (alpha < 0) return 0;
		return alpha;
	}
	
	function formatColorComponent(colorComponent) {
		return Math.round(colorComponent * maxColorComponent).toString();
	}
	
	function formatAlpha(alpha) {
		if (alpha == 0) return "0";
		if (alpha == 1) return "1";
		// take 6 significant digits and remove trailing 0s.
		var fractional = Math.floor(alpha * 1000000).toString().replace(/^([0-9]*?)0+$/, "$1");
		return "0." + fractional;
	}

})($global);

(function(g) {

	///==============================================================================================
	/// Represents a named color format.
	/// E.g. "Blue" or "red".
	///==============================================================================================
	g.NamedColorFormat = g.Class.define(g.ColorFormatBase, {
		initialize: function() {
			this.hexColorFormat = new g.HexColorFormat({tolerateHashPrefix: true, formatInLowerCase: true});
		},


		///
		/// Parses color name into RGBA array.
		/// 
		/// @param strColor A name of the color to be parsed.
		/// 
		/// @returns An RGBA array representing parsed color or null if the color cannot be parsed.
		///
		parseColor: function (strColor) {
			if (!strColor) return null;

			strColor = g.String.trim(strColor).toLowerCase();
			if (!colorsNameToHex.hasOwnProperty(strColor)) return null;
			var hex = colorsNameToHex[strColor];
			return hex == null ? null : this.hexColorFormat.parseColor(hex);
		},


		///
		/// Gets name of the color represented by RGBA array.
		/// 
		/// @param color An RGBA array representing color.
		/// 
		/// @returns A color name.
		///
		formatColor: function(color) {
			if (!g.Array.isArray(color) || color.length < 4) return null;

			if (color[3] < 1) return null;

			var hex = this.hexColorFormat.formatColor(color);
			if (hex == null) return null;

			return colorsHexToName["$" + hex] || null;
		}
	});


	var colorsNameToHex = {
		"aliceblue": "#f0f8ff", "antiquewhite": "#faebd7", "aqua": "#00ffff", "aquamarine": "#7fffd4", "azure": "#f0ffff",
		"beige": "#f5f5dc", "bisque": "#ffe4c4", "black": "#000000", "blanchedalmond": "#ffebcd", "blue": "#0000ff",
		"blueviolet": "#8a2be2", "brown": "#a52a2a", "burlywood": "#deb887", "cadetblue": "#5f9ea0", "chartreuse": "#7fff00",
		"chocolate": "#d2691e", "coral": "#ff7f50", "cornflowerblue": "#6495ed", "cornsilk": "#fff8dc", "crimson": "#dc143c",
		"cyan": "#00ffff", "darkblue": "#00008b", "darkcyan": "#008b8b", "darkgoldenrod": "#b8860b", "darkgray": "#a9a9a9",
		"darkgreen": "#006400", "darkkhaki": "#bdb76b", "darkmagenta": "#8b008b", "darkolivegreen": "#556b2f", "darkorange": "#ff8c00",
		"darkorchid": "#9932cc", "darkred": "#8b0000", "darksalmon": "#e9967a", "darkseagreen": "#8fbc8f", "darkslateblue": "#483d8b",
		"darkslategray": "#2f4f4f", "darkturquoise": "#00ced1", "darkviolet": "#9400d3", "deeppink": "#ff1493", "deepskyblue": "#00bfff",
		"dimgray": "#696969", "dodgerblue": "#1e90ff", "firebrick": "#b22222", "floralwhite": "#fffaf0", "forestgreen": "#228b22",
		"fuchsia": "#ff00ff", "gainsboro": "#dcdcdc", "ghostwhite": "#f8f8ff", "gold": "#ffd700", "goldenrod": "#daa520",
		"gray": "#808080", "green": "#008000", "greenyellow": "#adff2f", "honeydew": "#f0fff0", "hotpink": "#ff69b4",
		"indianred ": "#cd5c5c", "indigo ": "#4b0082", "ivory": "#fffff0", "khaki": "#f0e68c", "lavender": "#e6e6fa",
		"lavenderblush": "#fff0f5", "lawngreen": "#7cfc00", "lemonchiffon": "#fffacd", "lightblue": "#add8e6", "lightcoral": "#f08080",
		"lightcyan": "#e0ffff", "lightgoldenrodyellow": "#fafad2", "lightgray": "#d3d3d3", "lightgreen": "#90ee90", "lightpink": "#ffb6c1",
		"lightsalmon": "#ffa07a", "lightseagreen": "#20b2aa", "lightskyblue": "#87cefa", "lightslategray": "#778899", "lightsteelblue": "#b0c4de",
		"lightyellow": "#ffffe0", "lime": "#00ff00", "limegreen": "#32cd32", "linen": "#faf0e6", "magenta": "#ff00ff",
		"maroon": "#800000", "mediumaquamarine": "#66cdaa", "mediumblue": "#0000cd", "mediumorchid": "#ba55d3", "mediumpurple": "#9370d8",
		"mediumseagreen": "#3cb371", "mediumslateblue": "#7b68ee", "mediumspringgreen": "#00fa9a", "mediumturquoise": "#48d1cc", "mediumvioletred": "#c71585",
		"midnightblue": "#191970", "mintcream": "#f5fffa", "mistyrose": "#ffe4e1", "moccasin": "#ffe4b5", "navajowhite": "#ffdead",
		"navy": "#000080", "oldlace": "#fdf5e6", "olive": "#808000", "olivedrab": "#6b8e23", "orange": "#ffa500",
		"orangered": "#ff4500", "orchid": "#da70d6", "palegoldenrod": "#eee8aa", "palegreen": "#98fb98", "paleturquoise": "#afeeee",
		"palevioletred": "#d87093", "papayawhip": "#ffefd5", "peachpuff": "#ffdab9", "peru": "#cd853f", "pink": "#ffc0cb",
		"plum": "#dda0dd", "powderblue": "#b0e0e6", "purple": "#800080", "red": "#ff0000", "rosybrown": "#bc8f8f",
		"royalblue": "#4169e1", "saddlebrown": "#8b4513", "salmon": "#fa8072", "sandybrown": "#f4a460", "seagreen": "#2e8b57",
		"seashell": "#fff5ee", "sienna": "#a0522d", "silver": "#c0c0c0", "skyblue": "#87ceeb", "slateblue": "#6a5acd",
		"slategray": "#708090", "snow": "#fffafa", "springgreen": "#00ff7f", "steelblue": "#4682b4", "tan": "#d2b48c",
		"teal": "#008080", "thistle": "#d8bfd8", "tomato": "#ff6347", "turquoise": "#40e0d0", "violet": "#ee82ee",
		"wheat": "#f5deb3", "white": "#ffffff", "whitesmoke": "#f5f5f5", "yellow": "#ffff00", "yellowgreen": "#9acd32"
	};

	var colorsHexToName = {
		"$f0f8ff": "aliceblue", "$faebd7": "antiquewhite", "$00ffff": "aqua", "$7fffd4": "aquamarine", "$f0ffff": "azure",
		"$f5f5dc": "beige", "$ffe4c4": "bisque", "$000000": "black", "$ffebcd": "blanchedalmond", "$0000ff": "blue",
		"$8a2be2": "blueviolet", "$a52a2a": "brown", "$deb887": "burlywood", "$5f9ea0": "cadetblue", "$7fff00": "chartreuse",
		"$d2691e": "chocolate", "$ff7f50": "coral", "$6495ed": "cornflowerblue", "$fff8dc": "cornsilk", "$dc143c": "crimson",
		"$00008b": "darkblue", "$008b8b": "darkcyan", "$b8860b": "darkgoldenrod", "$a9a9a9": "darkgray", "$006400": "darkgreen",
		"$bdb76b": "darkkhaki", "$8b008b": "darkmagenta", "$556b2f": "darkolivegreen", "$ff8c00": "darkorange", "$9932cc": "darkorchid",
		"$8b0000": "darkred", "$e9967a": "darksalmon", "$8fbc8f": "darkseagreen", "$483d8b": "darkslateblue", "$2f4f4f": "darkslategray",
		"$00ced1": "darkturquoise", "$9400d3": "darkviolet", "$ff1493": "deeppink", "$00bfff": "deepskyblue", "$696969": "dimgray",
		"$1e90ff": "dodgerblue", "$b22222": "firebrick", "$fffaf0": "floralwhite", "$228b22": "forestgreen", "$dcdcdc": "gainsboro",
		"$f8f8ff": "ghostwhite", "$ffd700": "gold", "$daa520": "goldenrod", "$808080": "gray", "$008000": "green",
		"$adff2f": "greenyellow", "$f0fff0": "honeydew", "$ff69b4": "hotpink", "$cd5c5c": "indianred ", "$4b0082": "indigo ",
		"$fffff0": "ivory", "$f0e68c": "khaki", "$e6e6fa": "lavender", "$fff0f5": "lavenderblush", "$7cfc00": "lawngreen",
		"$fffacd": "lemonchiffon", "$add8e6": "lightblue", "$f08080": "lightcoral", "$e0ffff": "lightcyan", "$fafad2": "lightgoldenrodyellow",
		"$d3d3d3": "lightgray", "$90ee90": "lightgreen", "$ffb6c1": "lightpink", "$ffa07a": "lightsalmon", "$20b2aa": "lightseagreen",
		"$87cefa": "lightskyblue", "$778899": "lightslategray", "$b0c4de": "lightsteelblue", "$ffffe0": "lightyellow", "$00ff00": "lime",
		"$32cd32": "limegreen", "$faf0e6": "linen", "$ff00ff": "magenta", "$800000": "maroon", "$66cdaa": "mediumaquamarine",
		"$0000cd": "mediumblue", "$ba55d3": "mediumorchid", "$9370d8": "mediumpurple", "$3cb371": "mediumseagreen", "$7b68ee": "mediumslateblue",
		"$00fa9a": "mediumspringgreen", "$48d1cc": "mediumturquoise", "$c71585": "mediumvioletred", "$191970": "midnightblue", "$f5fffa": "mintcream",
		"$ffe4e1": "mistyrose", "$ffe4b5": "moccasin", "$ffdead": "navajowhite", "$000080": "navy", "$fdf5e6": "oldlace",
		"$808000": "olive", "$6b8e23": "olivedrab", "$ffa500": "orange", "$ff4500": "orangered", "$da70d6": "orchid",
		"$eee8aa": "palegoldenrod", "$98fb98": "palegreen", "$afeeee": "paleturquoise", "$d87093": "palevioletred", "$ffefd5": "papayawhip",
		"$ffdab9": "peachpuff", "$cd853f": "peru", "$ffc0cb": "pink", "$dda0dd": "plum", "$b0e0e6": "powderblue",
		"$800080": "purple", "$ff0000": "red", "$bc8f8f": "rosybrown", "$4169e1": "royalblue", "$8b4513": "saddlebrown",
		"$fa8072": "salmon", "$f4a460": "sandybrown", "$2e8b57": "seagreen", "$fff5ee": "seashell", "$a0522d": "sienna",
		"$c0c0c0": "silver", "$87ceeb": "skyblue", "$6a5acd": "slateblue", "$708090": "slategray", "$fffafa": "snow",
		"$00ff7f": "springgreen", "$4682b4": "steelblue", "$d2b48c": "tan", "$008080": "teal", "$d8bfd8": "thistle",
		"$ff6347": "tomato", "$40e0d0": "turquoise", "$ee82ee": "violet", "$f5deb3": "wheat", "$ffffff": "white",
		"$f5f5f5": "whitesmoke", "$ffff00": "yellow", "$9acd32": "yellowgreen"
	};


})($global);

window.jscolor = {


	dir : '', // location of jscolor directory (leave empty to autodetect)
	bindClass : 'color', // class name
	binding : true, // automatic binding via <input class="...">
	preloading : true, // use image preloading?


	install : function() {
		jscolor.addEvent(window, 'load', jscolor.init);
	},


	init : function() {
		if(jscolor.binding) {
			jscolor.bind();
		}
		if(jscolor.preloading) {
			jscolor.preload();
		}
	},


	getDir : function() {
		if (!jscolor.dir) {
			var detected = jscolor.detectDir();
			jscolor.dir = detected!==false ? detected : 'jscolor/';
		}
		return jscolor.dir;
	},


	detectDir : function() {
		var base = location.href;

		var e = document.getElementsByTagName('base');
		for(var i=0; i<e.length; i+=1) {
			if(e[i].href) { base = e[i].href; }
		}

		var regex = new RegExp("(^|\/)" + $global.name + "\.js([?#].*)?$", "i");

		var e = document.getElementsByTagName('script');
		for(var i=0; i<e.length; i+=1) {
			if(e[i].src && regex.test(e[i].src)) {
				var src = new jscolor.URI(e[i].src);
				var srcAbs = src.toAbsolute(base);
				srcAbs.path = srcAbs.path.replace(/[^\/]+$/, ''); // remove filename
				srcAbs.query = null;
				srcAbs.fragment = null;
				return srcAbs.toString() + "images/";
			}
		}
		return false;
	},


	bind : function() {
		var matchClass = new RegExp('(^|\\s)('+jscolor.bindClass+')\\s*(\\{[^}]*\\})?', 'i');
		var e = document.getElementsByTagName('input');
		for(var i=0; i<e.length; i+=1) {
			var m;
			if(!e[i].color && e[i].className && (m = e[i].className.match(matchClass))) {
				var prop = {};
				if(m[3]) {
					try {
						prop = (new Function ('return (' + m[3] + ')'))();
					} catch(eInvalidProp) {}
				}
				e[i].color = new jscolor.color(e[i], prop);
			}
		}
	},


	preload : function() {
		for(var fn in jscolor.imgRequire) {
			if(jscolor.imgRequire.hasOwnProperty(fn)) {
				jscolor.loadImage(fn);
			}
		}
	},


	images : {
		pad : [ 181, 101 ],
		sld: [ 16, 101 ],
		sldTransparency: [ 181, 16 ],
		cross : [ 15, 15 ],
		arrow : [ 7, 11 ]
	},


	imgRequire : {},
	imgLoaded : {},


	requireImage : function(filename) {
		jscolor.imgRequire[filename] = true;
	},


	loadImage : function(filename) {
		if(!jscolor.imgLoaded[filename]) {
			jscolor.imgLoaded[filename] = new Image();
			jscolor.imgLoaded[filename].src = jscolor.getDir()+filename;
		}
	},


	fetchElement : function(mixed) {
		return typeof mixed === 'string' ? document.getElementById(mixed) : mixed;
	},


	addEvent : function(el, evnt, func) {
		if(el.addEventListener) {
			el.addEventListener(evnt, func, false);
		} else if(el.attachEvent) {
			el.attachEvent('on'+evnt, func);
		}
	},

    removeEvent: function(el, event, func) {
        try {
            el.removeEventListener(event, func, false);
        } catch (e) {
            try {
                el.detachEvent('on' + event, func);
            } catch (e) {
                el['on' + event] = null;
            }
        }
    },

	fireEvent : function(el, evnt) {
		if(!el) {
			return;
		}
		if(document.createEvent) {
			var ev = document.createEvent('HTMLEvents');
			ev.initEvent(evnt, true, true);
			el.dispatchEvent(ev);
		} else if(document.createEventObject) {
			var ev = document.createEventObject();
			el.fireEvent('on'+evnt, ev);
		} else if(el['on'+evnt]) { // alternatively use the traditional event model (IE5)
			el['on'+evnt]();
		}
	},


	getElementPos : function(e) {
		var e1=e, e2=e;
		var x=0, y=0;
		if(e1.offsetParent) {
			do {
				x += e1.offsetLeft;
				y += e1.offsetTop;
			} while(e1 = e1.offsetParent);
		}
		while((e2 = e2.parentNode) && e2.nodeName.toUpperCase() !== 'BODY') {
			x -= e2.scrollLeft;
			y -= e2.scrollTop;
		}
		return [x, y];
	},


	getElementSize : function(e) {
		return [e.offsetWidth, e.offsetHeight];
	},
	
	getMousePos: function(e) {
		e = e || window.event;
		if (e.pageX == null && e.clientX != null) {
			var html = document.documentElement;
			var body = document.body;
			e.pageX = e.clientX + (html && html.scrollLeft || body && body.scrollLeft || 0) - (html.clientLeft || 0);
			e.pageY = e.clientY + (html && html.scrollTop || body && body.scrollTop || 0) - (html.clientTop || 0);
		}
		return {x: e.pageX, y: e.pageY};
	},

	getRelMousePos : function(e) {
		var x = 0, y = 0;
		e = e || window.event;
		if (typeof e.offsetX === 'number') {
			x = e.offsetX;
			y = e.offsetY;
		} else if (typeof e.layerX === 'number') {
			x = e.layerX;
			y = e.layerY;
		}
		return { x: x, y: y };
	},


	getViewPos : function() {
		if(typeof window.pageYOffset === 'number') {
			return [window.pageXOffset, window.pageYOffset];
		} else if(document.body && (document.body.scrollLeft || document.body.scrollTop)) {
			return [document.body.scrollLeft, document.body.scrollTop];
		} else if(document.documentElement && (document.documentElement.scrollLeft || document.documentElement.scrollTop)) {
			return [document.documentElement.scrollLeft, document.documentElement.scrollTop];
		} else {
			return [0, 0];
		}
	},


	getViewSize : function() {
		if(typeof window.innerWidth === 'number') {
			return [window.innerWidth, window.innerHeight];
		} else if(document.body && (document.body.clientWidth || document.body.clientHeight)) {
			return [document.body.clientWidth, document.body.clientHeight];
		} else if(document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight)) {
			return [document.documentElement.clientWidth, document.documentElement.clientHeight];
		} else {
			return [0, 0];
		}
	},


	trim : function(str) {
		return str.replace(/^\s+|\s+$/g, '');
	},


	setOpacity: function (elem, value) {
		if (typeof elem.style.opacity !== 'undefined') {
			elem.style.opacity = value.toString();
			return;
		}

		// Code is based on jQuery opacity support implementation
		
		var style = elem.style;
		var currentStyle = elem.currentStyle;
		var opacity = "alpha(opacity=" + value * 100 + ")";
		var filter = currentStyle && currentStyle.filter || style.filter || "";
		var ralpha = /alpha\([^)]*\)/i;

		// IE has trouble with opacity if it does not have layout
		// Force it by setting the zoom level
		style.zoom = 1;

		// if setting opacity to 1, and no other filters exist - attempt to remove filter attribute
		// if value === "", then remove inline opacity
		if ((value >= 1 || value === "") &&
				jscolor.trim(filter.replace(ralpha, "")) === "" &&
				style.removeAttribute ) {

			// Setting style.filter to null, "" & " " still leave "filter:" in the cssText
			// if "filter:" is present at all, clearType is disabled, we want to avoid this
			// style.removeAttribute is IE Only, but so apparently is this code path...
			style.removeAttribute( "filter" );

			// if there is no filter style applied in a css rule or unset inline opacity, we are done
			if ( value === "" || currentStyle && !currentStyle.filter ) return;
		}

		// otherwise, set new filter values
		style.filter = ralpha.test( filter ) ? filter.replace( ralpha, opacity ) : filter + " " + opacity;
		},


	URI : function(uri) { // See RFC3986

		this.scheme = null;
		this.authority = null;
		this.path = '';
		this.query = null;
		this.fragment = null;

		this.parse = function(uri) {
			var m = uri.match(/^(([A-Za-z][0-9A-Za-z+.-]*)(:))?((\/\/)([^\/?#]*))?([^?#]*)((\?)([^#]*))?((#)(.*))?/);
			this.scheme = m[3] ? m[2] : null;
			this.authority = m[5] ? m[6] : null;
			this.path = m[7];
			this.query = m[9] ? m[10] : null;
			this.fragment = m[12] ? m[13] : null;
			return this;
		};

		this.toString = function() {
			var result = '';
			if(this.scheme !== null) { result = result + this.scheme + ':'; }
			if(this.authority !== null) { result = result + '//' + this.authority; }
			if(this.path !== null) { result = result + this.path; }
			if(this.query !== null) { result = result + '?' + this.query; }
			if(this.fragment !== null) { result = result + '#' + this.fragment; }
			return result;
		};

		this.toAbsolute = function(base) {
			var base = new jscolor.URI(base);
			var r = this;
			var t = new jscolor.URI;

			if(base.scheme === null) { return false; }

			if(r.scheme !== null && r.scheme.toLowerCase() === base.scheme.toLowerCase()) {
				r.scheme = null;
			}

			if(r.scheme !== null) {
				t.scheme = r.scheme;
				t.authority = r.authority;
				t.path = removeDotSegments(r.path);
				t.query = r.query;
			} else {
				if(r.authority !== null) {
					t.authority = r.authority;
					t.path = removeDotSegments(r.path);
					t.query = r.query;
				} else {
					if(r.path === '') {
						t.path = base.path;
						if(r.query !== null) {
							t.query = r.query;
						} else {
							t.query = base.query;
						}
					} else {
						if(r.path.substr(0,1) === '/') {
							t.path = removeDotSegments(r.path);
						} else {
							if(base.authority !== null && base.path === '') {
								t.path = '/'+r.path;
							} else {
								t.path = base.path.replace(/[^\/]+$/,'')+r.path;
							}
							t.path = removeDotSegments(t.path);
						}
						t.query = r.query;
					}
					t.authority = base.authority;
				}
				t.scheme = base.scheme;
			}
			t.fragment = r.fragment;

			return t;
		};

		function removeDotSegments(path) {
			var out = '';
			while(path) {
				if(path.substr(0,3)==='../' || path.substr(0,2)==='./') {
					path = path.replace(/^\.+/,'').substr(1);
				} else if(path.substr(0,3)==='/./' || path==='/.') {
					path = '/'+path.substr(3);
				} else if(path.substr(0,4)==='/../' || path==='/..') {
					path = '/'+path.substr(4);
					out = out.replace(/\/?[^\/]*$/, '');
				} else if(path==='.' || path==='..') {
					path = '';
				} else {
					var rm = path.match(/^\/?[^\/]*/)[0];
					path = path.substr(rm.length);
					out = out + rm;
				}
			}
			return out;
		}

		if(uri) {
			this.parse(uri);
		}

	},


	//
	// Usage example:
	// var myColor = new jscolor.color(myInputElement)
	//

	color : function(target, prop) {


		this.required = true; // refuse empty values?
		this.adjust = true; // adjust value to uniform notation?
		this.hash = true; // prefix color with # symbol?
		this.caps = true; // uppercase?
		this.slider = true; // show the value/saturation slider?
		this.transparencySlider = true; // show the transparency slider?
		this.valueElement = target; // value holder
		this.styleElements = {
			opaqueColor: [target], // array of elements to reflect opaque part of the color
			transparancyBase: [], // array of elements to draw transparency base (squared background)
			transparency: [], // array of elements to draw transparency (normally these elements overflow transparencyBase elements)
			text: [] // array of elements to highlight text color according to selected color
		}; // where to reflect current color
		this.onImmediateChange = null; // onchange callback (can be either string or function)
		this.hsv = [0, 0, 1]; // read-only  0-6, 0-1, 0-1
		this.rgba = [1, 1, 1, 1]; // read-only  0-1, 0-1, 0-1, 0-1
		this.minH = 0; // read-only  0-6
		this.maxH = 6; // read-only  0-6
		this.minS = 0; // read-only  0-1
		this.maxS = 1; // read-only  0-1
		this.minV = 0; // read-only  0-1
		this.maxV = 1; // read-only  0-1

		this.pickerOnfocus = true; // display picker on focus?
		this.pickerMode = 'HSV'; // HSV | HVS
		this.pickerPosition = 'bottom'; // left | right | top | bottom
		this.pickerSmartPosition = true; // automatically adjust picker position when necessary
		this.pickerButtonHeight = 20; // px
		this.pickerClosable = true;
		this.pickerCloseText = '✔';
		this.pickerButtonColor = 'ButtonText'; // px
		this.pickerFace = 10; // px
		this.pickerFaceColor = 'ThreeDFace'; // CSS color
		this.pickerBorder = 1; // px
		this.pickerBorderColor = 'ThreeDHighlight ThreeDShadow ThreeDShadow ThreeDHighlight'; // CSS color
		this.pickerInset = 1; // px
		this.pickerInsetColor = 'ThreeDShadow ThreeDHighlight ThreeDHighlight ThreeDShadow'; // CSS color
		this.pickerZIndex = 10000;
        this.onOpen = function (){};//

		this.onClose = function (){};//

		for(var p in prop) {
			if(prop.hasOwnProperty(p)) {
				this[p] = prop[p];
			}
		}


		this.hidePicker = function() {

			if(isPickerOwner()) {
			    removePicker();
				if(this.onClose){
					this.onClose();
				}
              //  GLOBAL.clearDropDown_UI_IfOpened();
			  //  removeEvent(window.document, "mouseup", null);
			}
		};


		this.showPicker = function() {

			if (!isPickerOwner()) {
				if(this.onOpen){
					this.onOpen();
				}
				var tp = jscolor.getElementPos(target); // target pos
				var ts = jscolor.getElementSize(target); // target size
				var vp = jscolor.getViewPos(); // view pos
				var vs = jscolor.getViewSize(); // view size
				var ps = getPickerDims(this); // picker size
				var a, b, c;
				switch(this.pickerPosition.toLowerCase()) {
					case 'left': a=1; b=0; c=-1; break;
					case 'right':a=1; b=0; c=1; break;
					case 'top':  a=0; b=1; c=-1; break;
					default:     a=0; b=1; c=1; break;
				}
				var l = (ts[b]+ps[b])/2;

				// picker pos
				if (!this.pickerSmartPosition) {
					var pp = [
						tp[a],
						tp[b]+ts[b]-l+l*c
					];
				} else {
					var pp = [
						-vp[a]+tp[a]+ps[a] > vs[a] ?
							(-vp[a]+tp[a]+ts[a]/2 > vs[a]/2 && tp[a]+ts[a]-ps[a] >= 0 ? tp[a]+ts[a]-ps[a] : tp[a]) :
							tp[a],
						-vp[b]+tp[b]+ts[b]+ps[b]-l+l*c > vs[b] ?
							(-vp[b]+tp[b]+ts[b]/2 > vs[b]/2 && tp[b]+ts[b]-l-l*c >= 0 ? tp[b]+ts[b]-l-l*c : tp[b]+ts[b]-l+l*c) :
							(tp[b]+ts[b]-l+l*c >= 0 ? tp[b]+ts[b]-l+l*c : tp[b]+ts[b]-l-l*c)
					];
				}
				drawPicker(pp[a], pp[b]);
			}
		};


		function restoreStyleElements(originalStyles) {
			restoreStyles(styleElements.opaqueColor, originalStyles.opaqueColor);
			restoreStyles(styleElements.transparencyBase, originalStyles.transparencyBase);
			restoreStyles(styleElements.transparency, originalStyles.transparency);
		}

		this.importColor = function() {
			if(!valueElement) {
				this.exportColor();
			} else {
				if(!this.adjust) {
					if (!colorFormatManager.setColorFromString(valueElement.value, { flags: leaveValue })) {
						restoreStyleElements(this.originalStyles);
						this.exportColor(leaveValue | leaveStyle);
					}
				} else if(!this.required && /^\s*$/.test(valueElement.value)) {
					valueElement.value = '';
					restoreStyleElements(this.originalStyles);
					this.exportColor(leaveValue | leaveStyle);
				} else if (colorFormatManager.setColorFromString(valueElement.value)) {
					// OK
				} else {
					this.exportColor();
				}
			}
		};


		this.exportColor = function(flags) {
			var me = this;
			if(!(flags & leaveValue) && valueElement) {
				var value = this.toString();
				valueElement.value = value;
			}
			if (!(flags & leaveStyle) && styleElements) {
				var opaqueColor = getOpaqueColorAsCss();
				var textColor = this.rgba[3] > 0.4 && 0.213 * this.rgba[0] + 0.715 * this.rgba[1] + 0.072 * this.rgba[2] < 0.5 ? '#FFF' : '#000';

				setStyle(styleElements.text, { color: textColor });
				setStyle(
					styleElements.opaqueColor,
					{ backgroundColor: opaqueColor, backgroundImage: 'none' }
				);
				setStyle(
					styleElements.transparencyBase,
					{ backgroundImage: "url('" + jscolor.getDir() + "tbtransparencybg.gif')", backgroundRepeat: 'repeat' }
				);
				setStyle(styleElements.transparency, { backgroundColor: opaqueColor});
				forEach(styleElements.transparency, function(element) {
					jscolor.setOpacity(element, me.rgba[3]);
				});
			}
			if(!(flags & leavePad) && isPickerOwner()) {
				redrawPad();
			}
			if(!(flags & leaveSld) && isPickerOwner()) {
				redrawSld();
			}
		};


		this.fromHSV = function(h, s, v, flags) { // null = don't change
			if(h !== null) { h = Math.max(0.0, this.minH, Math.min(6.0, this.maxH, h)); }
			if(s !== null) { s = Math.max(0.0, this.minS, Math.min(1.0, this.maxS, s)); }
			if(v !== null) { v = Math.max(0.0, this.minV, Math.min(1.0, this.maxV, v)); }

			var rgb = HSV_RGB(
				h===null ? this.hsv[0] : (this.hsv[0]=h),
				s===null ? this.hsv[1] : (this.hsv[1]=s),
				v===null ? this.hsv[2] : (this.hsv[2]=v)
			);
			if (!this.rgba) {
				this.rgba = [rgb[0], rgb[1], rgb[2], 1];
			} else {
				this.rgba[0] = rgb[0];
				this.rgba[1] = rgb[1];
				this.rgba[2] = rgb[2];
			}

			this.exportColor(flags);
		};


		this.fromRGBA = function(r, g, b, a, flags) { // null = don't change
			if(r !== null) { r = Math.max(0.0, Math.min(1.0, r)); }
			if(g !== null) { g = Math.max(0.0, Math.min(1.0, g)); }
			if(b !== null) { b = Math.max(0.0, Math.min(1.0, b)); }
			if(a !== null) { a = Math.max(0.0, Math.min(1.0, a)); }

			var hsv = RGB_HSV(
				r===null ? this.rgba[0] : r,
				g===null ? this.rgba[1] : g,
				b===null ? this.rgba[2] : b
			);
			if(hsv[0] !== null) {
				this.hsv[0] = Math.max(0.0, this.minH, Math.min(6.0, this.maxH, hsv[0]));
			}
			if(hsv[2] !== 0) {
				this.hsv[1] = hsv[1]===null ? null : Math.max(0.0, this.minS, Math.min(1.0, this.maxS, hsv[1]));
			}
			this.hsv[2] = hsv[2]===null ? null : Math.max(0.0, this.minV, Math.min(1.0, this.maxV, hsv[2]));

			// update RGB according to final HSV, as some values might be trimmed
			var rgb = HSV_RGB(this.hsv[0], this.hsv[1], this.hsv[2]);
			this.rgba[0] = rgb[0];
			this.rgba[1] = rgb[1];
			this.rgba[2] = rgb[2];
			if (a !== null) this.rgba[3] = a;

			this.exportColor(flags);
		};


		this.fromString = function(str) {
			this.rgba = [1, 1, 1, 1];
			colorFormatManager.setColorFromString(str);
		};


		this.toString = function () {
			return colorFormatManager.getColorString();
		};

		this.getColor = function() {
			function convertComponent(component) {
				return Math.round(component * 255);
			}
			return [
				convertComponent(this.rgba[0]),
				convertComponent(this.rgba[1]),
				convertComponent(this.rgba[2]),
				this.rgba[3]
			];
		};
		
		function RGB_HSV(r, g, b) {
			var n = Math.min(Math.min(r,g),b);
			var v = Math.max(Math.max(r,g),b);
			var m = v - n;
			if(m === 0) { return [ null, 0, v ]; }
			var h = r===n ? 3+(b-g)/m : (g===n ? 5+(r-b)/m : 1+(g-r)/m);
			return [ h===6?0:h, m/v, v ];
		}


		function HSV_RGB(h, s, v) {
			if(h === null) { return [ v, v, v ]; }
			var i = Math.floor(h);
			var f = i%2 ? h-i : 1-(h-i);
			var m = v * (1 - s);
			var n = v * (1 - s*f);
			switch(i) {
				case 6:
				case 0: return [v,n,m];
				case 1: return [n,v,m];
				case 2: return [m,v,n];
				case 3: return [m,n,v];
				case 4: return [n,m,v];
				case 5: return [v,m,n];
			}
		}


		function removePicker() {
			delete jscolor.picker.owner;
			document.getElementsByTagName('body')[0].removeChild(jscolor.picker.boxB);

			//removeEvent(scolor.revmoveEvent);
			
		}
		function removeEvent(obj, type, fn) {
		    if (obj.detachEvent) {
		        obj.detachEvent('on' + type, obj[type + fn]);
		        obj[type + fn] = null;
		    } else
		        obj.removeEventListener(type, fn, false);
		}

		function drawPicker(x, y) {
			if(!jscolor.picker) {
				jscolor.picker = {
					box : document.createElement('div'),
					boxB : document.createElement('div'),
					pad : document.createElement('div'),
					padB : document.createElement('div'),
					padM: document.createElement('div'),
					padBrightness: document.createElement('div'),
					sld : document.createElement('div'),
					sldB : document.createElement('div'),
					sldM: document.createElement('div'),
					sldTransparency: document.createElement('div'),
					sldTransparencyB: document.createElement('div'),
					sldTransparencyM: document.createElement('div'),
					btn : document.createElement('div'),
					btnS : document.createElement('span'),
					btnT : document.createTextNode(THIS.pickerCloseText)
				};
				// brightness slider content
				for (var i = 0, segSize = 4; i < jscolor.images.sld[1]; i += segSize) {
					var seg = document.createElement('div');
					seg.style.height = segSize + 'px';
					seg.style.fontSize = '1px';
					seg.style.lineHeight = '0';
					jscolor.picker.sld.appendChild(seg);
				}
				// transparency slider content
				for (var i = 0, segSize = 4,
							segWidth = segSize + 'px', segHeight = jscolor.images.sldTransparency[0] + 'px',
							opacityStep = 1/jscolor.images.sldTransparency[0];
						i < jscolor.images.sldTransparency[0];
						i += segSize) {
					var seg = document.createElement('div');
					seg.style.position = 'absolute';
					seg.style.top = '0';
					seg.style.left = i + 'px';
					seg.style.width = segWidth;
					seg.style.height = segHeight;
					jscolor.setOpacity(seg, opacityStep*i);
					jscolor.picker.sldTransparency.appendChild(seg);
				}
				jscolor.picker.sldB.appendChild(jscolor.picker.sld);
				jscolor.picker.box.appendChild(jscolor.picker.sldB);
				jscolor.picker.box.appendChild(jscolor.picker.sldM);
				jscolor.picker.padB.appendChild(jscolor.picker.padBrightness);
				jscolor.picker.padB.appendChild(jscolor.picker.pad);
				jscolor.picker.box.appendChild(jscolor.picker.padB);
				jscolor.picker.box.appendChild(jscolor.picker.padM);
				jscolor.picker.sldTransparencyB.appendChild(jscolor.picker.sldTransparency);
				jscolor.picker.box.appendChild(jscolor.picker.sldTransparencyB);
				jscolor.picker.box.appendChild(jscolor.picker.sldTransparencyM);
				jscolor.picker.btnS.appendChild(jscolor.picker.btnT);
				jscolor.picker.btn.appendChild(jscolor.picker.btnS);
				jscolor.picker.box.appendChild(jscolor.picker.btn);
				jscolor.picker.boxB.appendChild(jscolor.picker.box);
			}

			var p = jscolor.picker;

			// controls interaction

			jscolor.addEvent(window.document, "mouseup", function (event) {

            //    GLOBAL.clearDropDown_UI_IfOpened();
			    event = event || window.event;
			    var target = event.target || event.srcElement;
			    if (target.style.position !== "absolute") {// make sure, we do not release if  the popup window is on.
			        removeEvent(window.document, "mouseup", arguments.callee);
			    }

			    //console.log(target);
			    if (holdPad) holdPad = false;
			    if (holdSld) holdSld = false;
			    if (holdTransparencySld) holdTransparencySld = false;
			    dispatchImmediateChange();
			    

			
			});

			// panel events
			p.box.onmouseup =
			p.box.onmouseout = function() { valueElement.focus(); };
			p.box.onmousedown = function () { abortBlur = true; };
			p.box.onmousemove = function() {
				if (document.selection) {
					document.selection.empty();
				} else if (window.getSelection) {
					window.getSelection().removeAllRanges();
				}
			};
			if('ontouchstart' in window) { // if touch device
				p.box.addEventListener('touchmove', function(e) {
					var event={
						'offsetX': e.touches[0].pageX-touchOffset.X,
						'offsetY': e.touches[0].pageY-touchOffset.Y
					};
					if (holdPad || holdSld || holdTransparencySld) {
						holdPad && setPad(event);
						holdSld && setSld(event);
						holdTransparencySld && setTransparencySld(event);
						dispatchImmediateChange();
					}
					e.stopPropagation(); // prevent move "view" on broswer
					e.preventDefault(); // prevent Default - Android Fix (else android generated only 1-2 touchmove events)
				}, false);
			}
			
			// pad events
			jscolor.addEvent(document, 'mousemove', function (e) {
				if (holdPad) {
					setPad(e, true);
					dispatchImmediateChange();
				}
			});
			p.padM.onmousedown = function (e) {
				window.setTimeout(function () { abortBlur = false; }, 0);
				if (e && e.preventDefault) e.preventDefault();
				p.padM.style.cursor = 'none';
				holdSld = false;
				holdPad = true;
				holdTransparencySld = false;
				setPad(e);
				//dispatchImmediateChange();
			};
			jscolor.addEvent(document, 'mouseup', function () { setPointerCursor(p.padM); });
			if('ontouchstart' in window) {
				p.padM.addEventListener('touchstart', function(e) {
					touchOffset={
						'X': e.target.offsetParent.offsetLeft,
						'Y': e.target.offsetParent.offsetTop
					};
					this.onmousedown({
						'offsetX':e.touches[0].pageX-touchOffset.X,
						'offsetY':e.touches[0].pageY-touchOffset.Y
					});
				});
			}
			
			// brightness slider events
			jscolor.addEvent(document, 'mousemove', function (e) {
				if (holdSld) {
					setSld(e, true);
				//	dispatchImmediateChange();
				}
			});
			p.sldM.onmousedown = function (e) {
				window.setTimeout(function() { abortBlur = false; }, 0);
				if (e && e.preventDefault) e.preventDefault();
				holdPad = false;
				holdTransparencySld = false;
				holdSld=true;
				setSld(e);
				//dispatchImmediateChange();
			};
			if('ontouchstart' in window) {
				p.sldM.addEventListener('touchstart', function(e) {
					touchOffset={
						'X': e.target.offsetParent.offsetLeft,
						'Y': e.target.offsetParent.offsetTop
					};
					this.onmousedown({
						'offsetX':e.touches[0].pageX-touchOffset.X,
						'offsetY':e.touches[0].pageY-touchOffset.Y
					});
				});
			}
			
			// transparency slider events
			jscolor.addEvent(document, 'mousemove', function (e) {
				if (holdTransparencySld) {
					setTransparencySld(e, true);
				//	dispatchImmediateChange();
				}
			});
			p.sldTransparencyM.onmousedown = function (e) {
				window.setTimeout(function () { abortBlur = false; }, 0);
				if (e && e.preventDefault) e.preventDefault();
				holdPad = false;
				holdSld = false;
				holdTransparencySld = true;
				setTransparencySld(e);
				//dispatchImmediateChange();
			};
			if ('ontouchstart' in window) {
				p.sldTransparencyM.addEventListener('touchstart', function (e) {
					touchOffset = {
						'X': e.target.offsetParent.offsetLeft,
						'Y': e.target.offsetParent.offsetTop
					};
					this.onmousedown({
						'offsetX': e.touches[0].pageX - touchOffset.X,
						'offsetY': e.touches[0].pageY - touchOffset.Y
					});
				});
			}

			// picker
			var dims = getPickerDims(THIS);
			p.box.style.width = dims[0] + 'px';
			p.box.style.height = dims[1] + 'px';

			// picker border
			p.boxB.style.position = 'absolute';
			p.boxB.style.clear = 'both';
			p.boxB.style.left = x+'px';
			p.boxB.style.top = y+'px';
			p.boxB.style.zIndex = THIS.pickerZIndex;
			p.boxB.style.border = THIS.pickerBorder+'px solid';
			p.boxB.style.borderColor = THIS.pickerBorderColor;
			p.boxB.style.background = THIS.pickerFaceColor;

			// pad image
			var padWidth = jscolor.images.pad[0] + 'px';
			var padHeight = jscolor.images.pad[1] + 'px';
			p.pad.style.width = padWidth;
			p.pad.style.height = padHeight;
			
			// pad brightness layer
			p.padBrightness.style.position = 'absolute';
			p.padBrightness.style.left = '0';
			p.padBrightness.style.top = '0';
			p.padBrightness.style.width = padWidth;
			p.padBrightness.style.height = padHeight;

			// pad border
			p.padB.style.position = 'absolute';
			p.padB.style.left = THIS.pickerFace+'px';
			p.padB.style.top = THIS.pickerFace+'px';
			p.padB.style.border = THIS.pickerInset+'px solid';
			p.padB.style.borderColor = THIS.pickerInsetColor;

			var padMHeight = THIS.transparencySlider || THIS.pickerClosable ?
				THIS.pickerFace + 2 * THIS.pickerInset + jscolor.images.pad[1] + jscolor.images.arrow[0] + 'px' :
				p.box.style.height;

			// pad mouse area
			p.padM.style.position = 'absolute';
			p.padM.style.left = '0';
			p.padM.style.top = '0';
			p.padM.style.width = THIS.pickerFace + 2*THIS.pickerInset + jscolor.images.pad[0] + jscolor.images.arrow[0] + 'px';
			p.padM.style.height = padMHeight;
			setPointerCursor(p.padM);

			// slider image
			p.sld.style.overflow = 'hidden';
			p.sld.style.width = jscolor.images.sld[0]+'px';
			p.sld.style.height = jscolor.images.sld[1]+'px';

			// slider border
			p.sldB.style.display = THIS.slider ? 'block' : 'none';
			p.sldB.style.position = 'absolute';
			p.sldB.style.right = THIS.pickerFace+'px';
			p.sldB.style.top = THIS.pickerFace+'px';
			p.sldB.style.border = THIS.pickerInset+'px solid';
			p.sldB.style.borderColor = THIS.pickerInsetColor;

			// slider mouse area
			p.sldM.style.display = THIS.slider ? 'block' : 'none';
			p.sldM.style.position = 'absolute';
			p.sldM.style.right = '0';
			p.sldM.style.top = '0';
			p.sldM.style.width = jscolor.images.sld[0] + jscolor.images.arrow[0] + THIS.pickerFace + 2*THIS.pickerInset + 'px';
			p.sldM.style.height = padMHeight;
			setPointerCursor(p.sldM);
			
			// transparency slider image
			p.sldTransparency.style.position = 'relative';
			p.sldTransparency.style.overflow = 'hidden';
			p.sldTransparency.style.width = jscolor.images.sldTransparency[0] + 'px';
			p.sldTransparency.style.height = jscolor.images.sldTransparency[1] + 'px';

			// transparency slider border
			p.sldTransparencyB.style.display = THIS.transparencySlider ? 'block' : 'none';
			p.sldTransparencyB.style.position = 'absolute';
			p.sldTransparencyB.style.left = THIS.pickerFace + 'px';
			p.sldTransparencyB.style.top = THIS.pickerFace + 2 * THIS.pickerInset + jscolor.images.pad[1] + 2*jscolor.images.arrow[0] + 'px';
			p.sldTransparencyB.style.border = THIS.pickerInset + 'px solid';
			p.sldTransparencyB.style.borderColor = THIS.pickerInsetColor;

			// transparency slider mouse area
			p.sldTransparencyM.style.display = THIS.transparencySlider ? 'block' : 'none';
			p.sldTransparencyM.style.position = 'absolute';
			p.sldTransparencyM.style.left = '0';
			p.sldTransparencyM.style.top = THIS.pickerFace + 2 * THIS.pickerInset + jscolor.images.pad[1] + jscolor.images.arrow[0] + 'px';
			p.sldTransparencyM.style.width = THIS.pickerFace + 2 * THIS.pickerInset + jscolor.images.sldTransparency[0] + jscolor.images.arrow[0] + 'px';
			p.sldTransparencyM.style.height = jscolor.images.arrow[0] + 2 * THIS.pickerInset + jscolor.images.sldTransparency[1] + 'px';
			setPointerCursor(p.sldTransparencyM);

			// "close" button
			function setBtnBorder() {
				var insetColors = THIS.pickerInsetColor.split(/\s+/);
				var pickerOutsetColor = insetColors.length < 2 ? insetColors[0] : insetColors[1] + ' ' + insetColors[0] + ' ' + insetColors[0] + ' ' + insetColors[1];
				p.btn.style.borderColor = pickerOutsetColor;
			}
			p.btn.style.display = THIS.pickerClosable ? 'block' : 'none';
			p.btn.style.position = 'absolute';
			p.btn.style.right = THIS.pickerFace + 'px';
			//p.btn.style.bottom = THIS.pickerFace + 'px';
			p.btn.style.top = THIS.pickerFace + 2 * THIS.pickerInset + jscolor.images.pad[1] + 2 * jscolor.images.arrow[0] + 'px';
			p.btn.style.padding = '0 6px';
			p.btn.style.height = '16px';
			p.btn.style.border = THIS.pickerInset + 'px solid';
			setBtnBorder();
			p.btn.style.color = THIS.pickerButtonColor;
			p.btn.style.font = '12px sans-serif';
			p.btn.style.textAlign = 'center';
			setPointerCursor(p.btn);
			p.btn.onmousedown = function () {
				THIS.hidePicker();
			};
			p.btnS.style.lineHeight = p.btn.style.height;

			// load images in optimal order
			switch(modeID) {
				case 0: var padImg = 'hs.png'; break;
				case 1: var padImg = 'hv.png'; break;
			}
			p.padM.style.backgroundImage = "url('"+jscolor.getDir()+"cross.gif')";
			p.padM.style.backgroundRepeat = "no-repeat";
			p.sldM.style.backgroundImage = "url('"+jscolor.getDir()+"arrow.gif')";
			p.sldM.style.backgroundRepeat = "no-repeat";
			p.sldTransparencyM.style.backgroundImage = "url('"+jscolor.getDir()+"varrow.gif')";
			p.sldTransparencyM.style.backgroundRepeat = "no-repeat";
			p.sldTransparency.style.backgroundImage = "url('" + jscolor.getDir() + "transparencybg.gif')";
			p.sldTransparency.style.backgroundPosition = "0 0";

           
			p.pad.style.backgroundImage = "url('"+jscolor.getDir()+padImg+"')";
			p.pad.style.backgroundRepeat = "no-repeat";
			p.pad.style.backgroundPosition = "0 0";
			
			// pad brightness layer background
			p.padBrightness.style.background = "#000";
			jscolor.setOpacity(p.padBrightness, 0);

			// place pointers
			redrawPad();
			redrawSld();

			jscolor.picker.owner = THIS;
			document.getElementsByTagName('body')[0].appendChild(p.boxB);
		}
		

		function setPointerCursor(element) {
			try {
				element.style.cursor = 'pointer';
			} catch (eOldIE) {
				element.style.cursor = 'hand';
			}
		}


		function getPickerDims(o) {
			var dims = [
				2*o.pickerInset + 2*o.pickerFace + jscolor.images.pad[0] +
					(o.slider ? 2*o.pickerInset + 2*jscolor.images.arrow[0] + jscolor.images.sld[0] : 0),
				2*o.pickerInset + 2*o.pickerFace + jscolor.images.pad[1] + 
					(o.transparencySlider || o.pickerClosable ? 2 * o.pickerInset + 2 * jscolor.images.arrow[0] + jscolor.images.sldTransparency[1] : 0)
			];
			return dims;
		}


		function redrawPad() {
			// redraw the pad pointer
			switch(modeID) {
				case 0: var yComponent = 1; break;
				case 1: var yComponent = 2; break;
			}
			var x = Math.round((THIS.hsv[0]/6) * (jscolor.images.pad[0]-1));
			var y = Math.round((1-THIS.hsv[yComponent]) * (jscolor.images.pad[1]-1));
			jscolor.picker.padM.style.backgroundPosition =
				(THIS.pickerFace+THIS.pickerInset+x - Math.floor(jscolor.images.cross[0]/2)) + 'px ' +
				(THIS.pickerFace+THIS.pickerInset+y - Math.floor(jscolor.images.cross[1]/2)) + 'px';

			// redraw the brightness slider image
			var seg = jscolor.picker.sld.childNodes;
			switch(modeID) {
				case 0:
					var rgb = HSV_RGB(THIS.hsv[0], THIS.hsv[1], 1);
					for(var i=0; i<seg.length; i+=1) {
						seg[i].style.backgroundColor = 'rgb('+
							(rgb[0]*(1-i/seg.length)*100)+'%,'+
							(rgb[1]*(1-i/seg.length)*100)+'%,'+
							(rgb[2]*(1-i/seg.length)*100)+'%)';
					}
					break;
				case 1:
					var rgb, s, c = [ THIS.hsv[2], 0, 0 ];
					var i = Math.floor(THIS.hsv[0]);
					var f = i%2 ? THIS.hsv[0]-i : 1-(THIS.hsv[0]-i);
					switch(i) {
						case 6:
						case 0: rgb=[0,1,2]; break;
						case 1: rgb=[1,0,2]; break;
						case 2: rgb=[2,0,1]; break;
						case 3: rgb=[2,1,0]; break;
						case 4: rgb=[1,2,0]; break;
						case 5: rgb=[0,2,1]; break;
					}
					for(var i=0; i<seg.length; i+=1) {
						s = 1 - 1/(seg.length-1)*i;
						c[1] = c[0] * (1 - s*f);
						c[2] = c[0] * (1 - s);
						seg[i].style.backgroundColor = 'rgb('+
							(c[rgb[0]]*100)+'%,'+
							(c[rgb[1]]*100)+'%,'+
							(c[rgb[2]]*100)+'%)';
					}
					break;
			}

			// redraw the transparency slider image
			redrawTransparencySliderImage();
		}
		

		function redrawTransparencySliderImage() {
			var segments = jscolor.picker.sldTransparency.childNodes;
			for (var i = 0; i < segments.length; ++i) {
				segments[i].style.backgroundColor = getOpaqueColorAsCss();
			}
		}
		

		function getOpaqueColorAsCss() {
			return cssColorFormat.formatColor(THIS.rgba);
		}


		function redrawSld() {
			// redraw the brightness slider pointer
			switch (modeID) {
				case 0: var yComponent = 2; break;
				case 1: var yComponent = 1; break;
			}
			var y = Math.round((1-THIS.hsv[yComponent]) * (jscolor.images.sld[1]-1));
			jscolor.picker.sldM.style.backgroundPosition =
				'0 ' + (THIS.pickerFace + THIS.pickerInset + y - Math.floor(jscolor.images.arrow[1] / 2)) + 'px';
			jscolor.setOpacity(jscolor.picker.padBrightness, 1 - THIS.hsv[yComponent]);
			
			// redraw the transparency slider pointer
			var transparencyX = Math.round(THIS.rgba[3] * (jscolor.images.sldTransparency[0] - 1));
			jscolor.picker.sldTransparencyM.style.backgroundPosition =
				THIS.pickerFace + THIS.pickerInset + transparencyX - Math.ceil(jscolor.images.arrow[0] / 2) - 1 + 'px 0';
		}


		function isPickerOwner() {
			return jscolor.picker && jscolor.picker.owner === THIS;
		}


		function blurTarget() {
			THIS.importColor();
			if(THIS.pickerOnfocus) {
				THIS.hidePicker();
			}
		}


		function blurValue() {
		}

		function setPad(e, globalEvent) {
			var mouseX, mouseY;
			if (globalEvent) {
				var mousePos = jscolor.getMousePos(e);
				var padPos = jscolor.getElementPos(jscolor.picker.padM);
				mouseX = mousePos.x - padPos[0];
				mouseY = mousePos.y - padPos[1];
			} else {
				var mouseRelPos = jscolor.getRelMousePos(e);
				mouseX = mouseRelPos.x;
				mouseY = mouseRelPos.y;
			}
			var x = mouseX - THIS.pickerFace - THIS.pickerInset;
			var y = mouseY - THIS.pickerFace - THIS.pickerInset;
			switch(modeID) {
				case 0: THIS.fromHSV(x*(6/(jscolor.images.pad[0]-1)), 1 - y/(jscolor.images.pad[1]-1), null, leaveSld); break;
				case 1: THIS.fromHSV(x*(6/(jscolor.images.pad[0]-1)), null, 1 - y/(jscolor.images.pad[1]-1), leaveSld); break;
			}
		}


		function setSld(e, globalEvent) {
			var mouseY = globalEvent ?
				jscolor.getMousePos(e).y - jscolor.getElementPos(jscolor.picker.sldM)[1] :
				jscolor.getRelMousePos(e).y;
			var y = mouseY - THIS.pickerFace - THIS.pickerInset;
			var brightness = 1 - y / (jscolor.images.sld[1] - 1);
			switch(modeID) {
				case 0: THIS.fromHSV(null, null, brightness, leavePad); break;
				case 1: THIS.fromHSV(null, brightness, null, leavePad); break;
			}
			jscolor.setOpacity(jscolor.picker.padBrightness, 1 - brightness);

			redrawTransparencySliderImage();
		}


		function setTransparencySld(e, globalEvent) {
			var mouseX = globalEvent ?
				jscolor.getMousePos(e).x - jscolor.getElementPos(jscolor.picker.sldTransparencyM)[0] :
				jscolor.getRelMousePos(e).x;
			var x = mouseX - THIS.pickerFace - THIS.pickerInset;
			var opacity = x / (jscolor.images.sldTransparency[0] - 1);
			THIS.rgba[3] = opacity < 0 ? 0 : (opacity > 1 ? 1 : opacity.toFixed(2));

			THIS.exportColor(leavePad);
		}


		function dispatchImmediateChange() {

			if (THIS.onImmediateChange) {

				var callback;

                // Dont let this fire unless last value dispatched is different than this one.

                if(dispatchImmediateChange.lastDispatchedVal !== valueElement.value){
                    if (typeof THIS.onImmediateChange === 'string') {
                        callback = new Function (THIS.onImmediateChange);
                    } else {
                        callback = THIS.onImmediateChange;
                    }
                    callback.call(THIS);
                    dispatchImmediateChange.lastDispatchedVal = valueElement.value;
                }


			}
		}

		function forEach(array, action) {
			if (array == null) return;
			if (typeof array.length === 'undefined') array = [array];
			for (var i = 0; i < array.length; i++) {
				action(array[i], i);
			}
		}


		function initStyleElements(elements) {
			var result = [];
			forEach(elements, function (element) {
				result.push(jscolor.fetchElement(element));
			});
			return result;
		}


		function transferStyles(elements) {
			var result = [];
			forEach(elements, function(element) {
				var elementStyles = {
					color: element.style.color,
					backgroundColor: element.style.backgroundColor,
					backgroundImage: element.style.backgroundImage,
					backgroundPosition: element.style.backgroundPosition,
					backgroundRepeat: element.style.backgroundRepeat
				};
				result.push(elementStyles);
			});
			return result;
		}
		
		function setStyle(elements, style) {
			forEach(elements, function (element) {
				if (typeof style.color !== 'undefined') element.style.color = style.color;
				if (typeof style.backgroundColor !== 'undefined') element.style.backgroundColor = style.backgroundColor;
				if (typeof style.backgroundImage !== 'undefined') element.style.backgroundImage = style.backgroundImage;
				if (typeof style.backgroundPosition !== 'undefined') element.style.backgroundPosition = style.backgroundPosition;
				if (typeof style.backgroundRepeat !== 'undefined') element.style.backgroundRepeat = style.backgroundRepeat;
			});
		}

		function restoreStyles(elements, styles) {
			forEach(elements, function(element, i) {
				element.style.color = styles[i].color;
				element.style.backgroundColor = styles[i].backgroundColor;
				element.style.backgroundImage = styles[i].backgroundImage;
				element.style.backgroundPosition = styles[i].backgroundPosition;
				element.style.backgroundRepeat = styles[i].backgroundRepeat;
			});
		}


		var THIS = this;
		var modeID = this.pickerMode.toLowerCase()==='hvs' ? 1 : 0;
		var abortBlur = false;
		var
			valueElement = jscolor.fetchElement(this.valueElement),
			styleElements = {
				opaqueColor: initStyleElements(this.styleElements.opaqueColor),
				transparencyBase: initStyleElements(this.styleElements.transparencyBase),
				transparency: initStyleElements(this.styleElements.transparency),
				text: initStyleElements(this.styleElements.text)
			};
		var
			holdPad = false,
			holdSld = false,
			holdTransparencySld = false,
			touchOffset = {};
		var
			leaveValue = 1<<0,
			leaveStyle = 1<<1,
			leavePad = 1<<2,
			leaveSld = 1 << 3;
		var cssColorFormat = new $global.HexColorFormat({ formatWithHashPrefix: true });
		var colorFormatManager = new $global.ColorFormatManager(
			[
				{
					name: 'hex',
					colorFormat: new $global.HexColorFormat({
						tolerateHashPrefix: true,
						formatWithHashPrefix: !!this.hash,
						formatInLowerCase: !this.caps,
						dontFormatTransparent: true
					}),
					fallback: "rgba"
				},
				{ name: 'rgba', colorFormat: new $global.RgbColorFormat() },
				{ name: 'named', colorFormat: new $global.NamedColorFormat(), fallback: "hex" }
			],
			function(rgba, options) { THIS.fromRGBA(rgba[0], rgba[1], rgba[2], rgba[3], options.flags); },
			function() { return THIS.rgba; }
		);

		// valueElement
		if (valueElement) {
			jscolor.addEvent(valueElement, 'click', function () {
				if (THIS.pickerOnfocus) { THIS.showPicker(); }
			});
			jscolor.addEvent(valueElement, 'blur', function () {
				if (!abortBlur) {
					window.setTimeout(function () { abortBlur || blurTarget(); abortBlur = false; }, 0);
				} else {
					abortBlur = false;
				}
			});
			
			var updateField = function() {
				colorFormatManager.setColorFromString(valueElement.value, { flags: leaveValue });
				dispatchImmediateChange();
			};

			var fieldKeyPress = function(e) {
				if (e.keyCode == 13) { // close picker on ENTER key
					blurTarget();
					if (e && e.preventDefault) e.preventDefault();
					return false;
				}
				return true;
			};

			jscolor.addEvent(valueElement, 'keypress', fieldKeyPress);


		  jscolor.addEvent(valueElement, 'keyup', updateField);
			jscolor.addEvent(valueElement, 'input', updateField);
			jscolor.addEvent(valueElement, 'blur', blurValue);
			valueElement.setAttribute('autocomplete', 'off');
		}

		// styleElements
		if (styleElements) {
			this.originalStyles = {
				opaqueColor: transferStyles(styleElements.opaqueColor),
				transparencyBase: transferStyles(styleElements.transparencyBase),
				transparency: transferStyles(styleElements.transparency)
			};
		}

		// require images
		switch(modeID) {
			case 0: jscolor.requireImage('hs.png'); break;
			case 1: jscolor.requireImage('hv.png'); break;
		}
		jscolor.requireImage('cross.gif');
		jscolor.requireImage('arrow.gif');

		this.importColor();
	}

};


jscolor.install();

(function (g, window, document) {
	
	///===========================================================================
	/// Represents color picker control.
	/// This control is thin wrapper over jsColor and input box. It wraps input box
	/// to nicely show color selected with jsColor (including transparency).
	/// 
	/// @param container - A DOM element (normally DIV) to be used as a container
	///   for the color picker control.
	/// 
	/// @param options - Options to be transfered to the jsColor.
	///===========================================================================
	Jsc.ColorPicker = g.Class.define({
		initialize: function (container, options) {
			var elements = buildDom(container);

			options = options || {};
			options.valueElement = elements.input;
			options.styleElements = {
				opaqueColor: elements.opaqueBar,
				transparencyBase: elements.transparentLayerBackground,
				transparency: elements.transparentLayer,
				text: elements.input
			};

			this.jsColor = new jscolor.color(container, options);

			var me = this;
			jscolor.addEvent(elements.opaqueBar, "click", function () {
				if (me.jsColor.pickerOnfocus) { me.showPicker(); }
			});
		},
		
		///
		/// Initializes color picker from the string representation of the color.
		/// It suports HEX, named and RGB/A formats.
		/// 
		/// @param str - string representation of the color.
		///
		fromString: function(str) {
			return this.jsColor.fromString(str);
		},

		///
		/// Gets string representation of the selected color.
		///
		toString: function() {
			return this.jsColor.toString();
		},
		
		///
		/// Gets a selected color in a form of RGBA array.
		///
		getColor: function() {
			return this.jsColor.getColor();
		},
		
		///
		/// Shows popup window for color selection.
		///
		showPicker: function() {
			this.jsColor.showPicker();
		},
		
		///
		/// Hides popup window for color selection.
		///
		hidePicker: function() {
			this.jsColor.hidePicker();
		}
	});


	function buildDom(container) {
		var elements = {
			opaqueBar: document.createElement("div"),
			holder: document.createElement("div"),
			transparentLayerBackground: document.createElement("div"),
			transparentLayer: document.createElement("div"),
			input: document.createElement("input")
		};

		container.appendChild(elements.opaqueBar);
		elements.holder.appendChild(elements.transparentLayerBackground);
		elements.holder.appendChild(elements.transparentLayer);
		elements.holder.appendChild(elements.input);
		container.appendChild(elements.holder);

		var height = 23;
		var width = container.clientWidth;
		var opaqueBarWidth = 10;

		container.style.overflow = "hidden";
		container.style.border = "#000 1px solid";
		container.style.height = height + "px";
		container.style.padding = "0";

		var floatStyle = "styleFloat";
		if (!document.all) {
			floatStyle = "cssFloat"; // for Firefox
		}

		elements.opaqueBar.style.width = opaqueBarWidth + "px";
		elements.opaqueBar.style.height = height + "px";
		elements.opaqueBar.style.margin = "0";
		elements.opaqueBar.style[floatStyle] = "left";

		var inputAreaWidth = width - opaqueBarWidth - 2;
		elements.holder.style.width = inputAreaWidth + "px";
		elements.holder.style.height = height - 5 + "px";
		elements.holder.style.padding = "2px 0 4px 2px";
		elements.holder.style[floatStyle] = "left";
		elements.holder.style.position = "relative";

		elements.transparentLayerBackground.style.position = "absolute";
		elements.transparentLayerBackground.style.zIndex = -3;//1;
		elements.transparentLayerBackground.style.top = "0";
		elements.transparentLayerBackground.style.height = height - 0 + "px";
		elements.transparentLayerBackground.style.left = "0";
		elements.transparentLayerBackground.style.right = "0";

		elements.transparentLayer.style.position = "absolute";
		elements.transparentLayer.style.zIndex = -2;//2;
		elements.transparentLayer.style.top = "0";
		elements.transparentLayer.style.height = height - 0 + "px";
		elements.transparentLayer.style.left = "0";
		elements.transparentLayer.style.right = "0";

		var inputBoxLeftMargin = 6;
		elements.input.style.position = "absolute";
		elements.input.style.zIndex = 3;// 3;
		elements.input.style.top = "2px";
		elements.input.style.left = inputBoxLeftMargin + "px";
		elements.input.style.width = inputAreaWidth - inputBoxLeftMargin + "px";
		elements.input.style.height = height - 7 + "px";
		elements.input.style.margin = "0";
		elements.input.style.padding = "1px 0 1px 0";
		elements.input.style.border = "none";
		elements.input.style.background = "none";
		elements.input.style.lineHeight = height - 7 + "px";
		elements.input.style.fontWeight = "bold";
		elements.input.style.outline = "none";
		
		return elements;
	}

})($global, window, document);

(function(g, $) {

	///===========================================================================
	/// Represents palette picker control.
	/// 
	/// @param container - A DOM element (normally DIV) to be used as a container
	///   for the palette picker control.
	///   
	/// @param settings - A settings for the control. Supported settings:
	///   minInputWidth - a minimal width for the input box.
	///===========================================================================
	Jsc.PalettePicker = g.Class.define(g.SearchableDropdown, {

		initialize: function(container, settings) {
			settings = settings || {};
			this.minInputWidth = settings.minInputWidth || 90;

			Jsc.PalettePicker.base.initialize.call(this, container, allPalettes, settings);
			this.colorBoxesAdjusted = false;
			
			this.onChange = null;
		},
		
		///
		/// Gets text representing item in a box.
		/// 
		/// @param palete - A palette object.
		///
		getItemDisplayText: function(palette) {
			return palette.name;
		},
		
		///
		/// Gets text to be comparing while searching.
		/// 
		/// @param palete - A palette object.
		///
		getItemSearchText: function(palette) {
			return palette.name;
		},
		
		///
		/// Shows options popup.
		///
		showOptions: function() {
			this.optionsPopup.css("visibility", "hidden");
			Jsc.PalettePicker.base.showOptions.call(this);
			adjustColorBoxes.call(this);
			this.optionsPopup.css("visibility", "");
		},

		///
		/// Creates DOM elements forming item in dropdown options list.
		/// Overrides method from the base type.
		/// 
		/// @param palette - A palette item.
		/// 
		/// 
		/// @param itemIndex - An item index in a list.
		/// 
		/// @param selectItemHandler - A callback to be called when item is selecting.
		///
		createItemContent: function(optionsContainer, palette, itemIndex, selectItemHandler) {
			var description = palette.name + " palette with " + palette.colors.length + " colors";

			var element = $(
				"<div class='palette-option'>" +
					"<div class='description'>" + description + "</div>" +
					"<div class='colors'></div>" +
				"</div>"
			);
			optionsContainer.append(element);
			element.click(selectItemHandler);

			var colorsElement = $(element.children()[1]);
			var colorsCount = palette.colors.length;
			var colors = "";
			var i;

			function addColorBox(colorIndex, isLast) {
				var color = palette.colors[colorIndex];
				var colorRgba = hexColorFormat.parseColor(color);
				var borderColor = scaleColor(colorRgba, -0.4);
				var rightBorder = isLast ? ";border-right-width: 1px;border-right-style:solid" : "";
				colors += "<span class='box' style='background-color:" + color + ";border-color:" + borderColor + rightBorder + "'></span>";
			}

			if (colorsCount < twoRowsThreshold) {
				colorsElement.addClass("two-rows");
				for (i = 0; i < colorsCount; ++i) {
					addColorBox(i, i == colorsCount - 1);
				}
			} else {
				// it is decided to omit last color for colors number not evenly divisible
				var rowLength = Math.floor(colorsCount / 2);
				colors += "<div class='row row-1'>";
				for (i = 0; i < rowLength; ++i) {
					addColorBox(i, i == rowLength - 1);
				}
				colors += "</div>";
				colors += "<div class='row row-2'>";
				var visibleColorsCount = rowLength * 2;
				for (i = rowLength; i < visibleColorsCount ; ++i) {
					addColorBox(i, i == visibleColorsCount - 1);
				}
				colors += "</div>";
			}
			colorsElement.append(colors);

			return element;
		},
		
		///
		/// Creates DOM elements forming content of the box.
		/// Overrides method of the base type, additionally creating a colors preview element.
		/// 
		/// @param boxContainer A box container jQuery element.
		///
		createBoxContent: function(boxContainer) {
			Jsc.PalettePicker.base.createBoxContent.call(this, boxContainer);

			this.container.addClass("palette-picker");
			this.optionsPopup.addClass("palette-picker-options");

			this.previewColors = $("<div class='colors-preview'></div>");
			boxContainer.append(this.previewColors);
		},

		///
		/// Is called when selected palette has changed. Highlights selected pallete in a list.
		/// 
		/// @param palette - A new selected palette.
		/// 
		/// @param oldPalette - A previously selected palette.
		/// 
		/// @param paletteOptionElement - An element corresponded to palette in a options list.
		/// 
		/// @param paletteItemOptionElement - An element corresponded to previous palette in a options list.
		///
		onItemSelected: function(palette, oldPalette, paletteOptionElement, oldPaletteOptionElement) {
			Jsc.PalettePicker.base.onItemSelected.call(this, palette, oldPalette, paletteOptionElement, oldPaletteOptionElement);

			paletteOptionElement.addClass("selected");
			if (oldPaletteOptionElement) oldPaletteOptionElement.removeClass("selected");

			this.previewColors.empty();
			for (var i = 0; i < palette.colors.length; ++i) {
				var color = palette.colors[i];
				this.previewColors.append("<span class='box' style='background-color:" + color + "'></span>");
			}
			this.previewColors.width(this.boxContainer.width());

			if (this.onChange) {
				this.onChange(palette.name);
			}
		},
		
		///
		/// Resizes input box according to container width.
		/// 
		/// @param containerWidth - A container width.
		///
		onInputBoxResize: function(containerWidth) {
			Jsc.PalettePicker.base.onInputBoxResize.call(this, containerWidth);

			if (this.previewColors) {
				this.previewColors.width(this.boxContainer.width());
			}
		},
		
		///
		/// Gets currently selected palette name.
		///
		getPalette: function() {
			return this.currentItem ? this.currentItem.name : null;
		},
		
		///
		/// Sets palette as a current.
		/// 
		/// @param paletteName - A palette name to set a current pallete.
		///
		setPalette: function(paletteName) {
			var palette = g.Array.find(this.itemsSource, function(item) { return item.name == paletteName; });
			if (palette == null) return;
			this.selectItem(palette);
		},
		
		///
		/// Sets handler for the palette changed event.
		/// 
		/// @param handler - A callback function to be called when palette in the picker changed.
		///   It takes a new palette name as an argument.
		///
		setOnChange: function(handler) {
			this.onChange = handler;
		}
	});


	function adjustColorBoxes() {
		if (this.colorBoxesAdjusted) return;

		var palettes = this.itemsSource;
		this.optionsContainer.children().children(".colors").each(function(paletteIndex, colorsElement) {
			colorsElement = $(colorsElement);
			var colorsWidth = colorsElement.width() - 1;
			var colorsCount = palettes[paletteIndex].colors.length;
			var rowColorsCount = colorsCount < twoRowsThreshold ? colorsCount : Math.floor(colorsCount / 2);
			var boxWidth = Math.floor(colorsWidth / rowColorsCount) - 1;
			if (rowColorsCount * (boxWidth + 1) < colorsWidth) boxWidth++;
			var extraPixels = colorsWidth - rowColorsCount * boxWidth;

			if (colorsCount < twoRowsThreshold) {
				colorsElement.children().each(function(colorIndex, box) {
					$(box).width(extraPixels-- > 0 ? boxWidth : boxWidth - 1);
				});
			} else {
				var rows = colorsElement.children();
				var row1Boxes = $(rows[0]).children(), row2Boxes = $(rows[1]).children();
				for (var i = 0; i < rowColorsCount; ++i) {
					var w = extraPixels-- > 0 ? boxWidth : boxWidth - 1;
					$(row1Boxes[i]).width(w);
					if (i < row2Boxes.length) $(row2Boxes[i]).width(w);
				}
			}
		});

		this.colorBoxesAdjusted = true;
	}


	var hexColorFormat = new g.HexColorFormat({ tolerateHashPrefix: true, formatWithHashPrefix: true });

	function scaleColor(rgba, scale) {
		if (scale > 1) scale = 1;
		if (scale < -1) scale = -1;

		if (scale > 0) {
			rgba[0] = rgba[0] + (1 - rgba[0]) * scale;
			rgba[1] = rgba[1] + (1 - rgba[1]) * scale;
			rgba[2] = rgba[2] + (1 - rgba[2]) * scale;
		}
		else {
			scale = 1 + scale;
			rgba[0] = rgba[0] * scale;
			rgba[1] = rgba[1] * scale;
			rgba[2] = rgba[2] * scale;
		}
		
		return hexColorFormat.formatColor(rgba);
	}


	///
	/// A threshold number of colors in palette to draw colors in a single row. If number of colors
	/// in palette is greater than this value then colors are shown in two rows.
	///
	var twoRowsThreshold = 30;
	
	///
	/// All known palettes.
	///
	var allPalettes = [
		{ name: "default", colors: ["#049DFF", "#FE6535", "#34FE35", "#FEFE05", "#D150B1", "#FF9A00", "#00C8C2", "#808ED7", "#C1F100", "#FEFFAF", "#2955B6", "#00A738", "#F54243", "#78BE3C", "#A87A89", "#3B3F84", "#E154F5", "#CD992D", "#8C2903", "#B9B09D", "#C9F9F9", "#5F035F", "#CACBFA", "#038183", "#0202FE", "#9CCEFE", "#FBCC9B", "#FFD731", "#1A3340", "#E7E495", "#7B823E", "#6BC0DE", "#8BFC70", "#D5987B", "#D5C923"] },
		{ name: "spreadsheet", colors: ["#9C9AFF", "#9C3063", "#FFFFCE", "#CEFFFF", "#630063", "#FF8284", "#0065CE", "#CECFFF", "#000084", "#FF00FF", "#FFFF00", "#00FFFF", "#840084", "#840000", "#008284", "#0000FF", "#00CFFF", "#CEFFFF", "#CEFFCE", "#FFFF9C", "#9CCFFF", "#FF9ACE", "#CE9AFF", "#FFCF9C", "#3165FF", "#31CFCE", "#9CCF00", "#FFCF00", "#FF9A00", "#FF6500", "#63659C", "#949694", "#003063", "#319A63", "#003000", "#313000", "#9C3000", "#9C3063", "#31309C", "#313031", "#000000", "#FFFFFF", "#FF0000", "#00FF00", "#0000FF", "#FFFF00", "#FF00FF", "#00FFFF", "#840000", "#008200", "#000084", "#848200", "#840084", "#008284", "#C6C3C6", "#848284"] },
		{ name: "oceanMidtones", colors: ["#4B649A", "#88E2D8", "#698380", "#0D93FA", "#5EE403", "#4088B2", "#FAEB8E", "#9ED266", "#666FA7", "#CD992C", "#85203D", "#4804FA", "#FC7DD5", "#7F9EEF", "#C335BA", "#63D2F8", "#1C2386", "#83D996", "#FEAB90", "#342172", "#309AE9", "#538772", "#BC16DF", "#07420A", "#DC7A4D", "#9ED99D", "#AA029B", "#AEB076", "#A40145", "#A64857", "#69C85B", "#624F03", "#0670AD", "#47A6BC", "#1814E0", "#7CA65E", "#495FD0", "#F2881E", "#D681E8", "#73AE86", "#2D16CC", "#889C78", "#AE792B", "#499869", "#EE625B", "#DE959C", "#952859", "#E55CE8", "#867540", "#3C8BCC"] },
		{ name: "mutedRainbow", colors: ["#B2213D", "#BAC014", "#64AF4A", "#DA5B8A", "#EE971F", "#989EDD", "#B7D3E6", "#8B44D7", "#DDC987", "#0157E1", "#0EA61E", "#EE6B07", "#B9CFB4", "#B24B42", "#0C78B0", "#2E5302", "#CE296D", "#F22889", "#FD27DB", "#64DCF5", "#8C6358", "#67CCB4", "#6CFE2C", "#17DDCD", "#290587", "#A37ECB", "#27AF76", "#85322D", "#741B7E", "#94F044", "#2B54F2", "#BD25E7", "#E9A3AA", "#FBC054", "#D4B64A", "#9EC822", "#0159A7", "#0F3D2D", "#7F0730", "#2AF2B0", "#35D60D", "#E7EB89", "#DA6ADE", "#9FB9D9", "#202AED", "#22AF7B", "#E2D451", "#5B9397", "#9B1B16", "#C53E1E"] },
		{ name: "pastelContrast", colors: ["#E68E42", "#3F5C79", "#E5D5AD", "#EC6CBB", "#E66457", "#F9E2D7", "#96A6A2", "#8121DB", "#6D38E9", "#C36845", "#D6CA21", "#99DED1", "#532E50", "#6469D4", "#5014C4", "#203AFB", "#07CE2E", "#94B32C", "#06656D", "#65D023", "#8E216D", "#6715A1", "#994C84", "#85A6B4", "#87313C", "#CC310C", "#174DFD", "#EDBB99", "#164216", "#F39379", "#63EC5D", "#2DD548", "#67E7FD", "#2285EC", "#778128", "#790D82", "#EE3834", "#604D83", "#25A064", "#D350D2", "#8AA5B7", "#0A21B8", "#575A5A", "#F36548", "#0DF3C6", "#10753A", "#709C34", "#92045A", "#45039E", "#D80834"] },
		{ name: "rainforest", colors: ["#3D8841", "#9BE239", "#23AAC4", "#0154C4", "#141CE1", "#507A03", "#14A091", "#B380FC", "#677487", "#E15195", "#AD6E9B", "#8C285A", "#CF5735", "#E1DF6C", "#4FEC79", "#8AC2D3", "#0DE68C", "#CFDF87", "#DA8907", "#44336D", "#B04058", "#2E53E5", "#2887F3", "#867F22", "#CD32C0", "#246493", "#82DCB1", "#B2D469", "#09DC8E", "#14B09D", "#538A23", "#C6309E", "#132098", "#92613A", "#DF8566", "#6902AD", "#C6B624", "#73C056", "#BBFD6A", "#EE2576", "#5714D5", "#5532F4", "#FD0701", "#211044", "#F9EF7A", "#76D24F", "#029ED9", "#E04CF9", "#78DEC9", "#CC0A32"] },
		{ name: "random", colors: ["#9ADB5E", "#61F34C", "#0C8DEF", "#C21EC7", "#737198", "#D5D73F", "#6085AE", "#67C358", "#B0C0BA", "#2FE630", "#93EF6B", "#377F4D", "#B06610", "#2F3B8C", "#CA0F7A", "#CA720A", "#A4D70F", "#6E875B", "#333318", "#05AF33", "#90DCA5", "#BF2E2D", "#5B3CF1", "#4A2470", "#2E304A", "#229BB4", "#DCE54E", "#B0890E", "#202468", "#10044B", "#35ABB6", "#DC9E5E", "#E50BDF", "#4D6E35", "#175AB2", "#9D3F56", "#101196", "#6327C9", "#E4DE52", "#979E09", "#C44A37", "#A51478", "#B95191", "#EB3B90", "#D38F76", "#18992E", "#5B81E4", "#1100F2", "#B4D184", "#EBF6BE"] },
		{ name: "autumn", colors: ["#860104", "#FFD62E", "#583410", "#FFFF6F", "#FF7000", "#551314", "#FEFFAF", "#7F4D1C", "#FFFF00", "#FF4502", "#830B0C", "#F6FCDA", "#000000", "#FDFFB1", "#FF9D02", "#EE1413", "#AE6A29", "#DE5600", "#E4A711", "#BB1313", "#D19500", "#61140D", "#BCB037", "#402E13", "#B8C267", "#B86D16", "#3F1815", "#C2C895", "#5E4520", "#A7B817", "#B85117", "#5F1A13", "#C4CAB4", "#000000", "#C1C898", "#B88918", "#AD3122", "#815F2D", "#A05813", "#A68D20", "#89291E", "#977E12"] },
		{ name: "bright", colors: ["#181161", "#D14524", "#F9F72B", "#83EA28", "#2BFBCD", "#A395DE", "#DC2ECE", "#4133F5", "#DA011D", "#01A468", "#837EB8", "#EA917C", "#C3B9F0", "#C972E8", "#958DFA", "#F87177", "#54D6A6", "#7B1D2C", "#D14524", "#E18514", "#AC7F13", "#84875D", "#BA5965", "#D42A5D", "#8E2C6F", "#6059A9", "#A4A0D4", "#F4F226", "#A8F960", "#72FEDF", "#CCC2F6", "#F69AEE", "#AFA9FC", "#FB9499", "#6BE6B9", "#8E569B", "#9EA89E", "#5FB8E9", "#D97C7A", "#CAC7E6", "#AA6F9E", "#9DB0A0", "#AA56BE", "#817BCA", "#CACF63", "#CFCEEB", "#E86F69", "#D0FDCC", "#E5E89C", "#12FE00"] },
		{ name: "lavender", colors: ["#4A2123", "#D52E06", "#B38D95", "#FFDBC5", "#C26444", "#82B2B8", "#AE7EA1", "#FFE19C", "#8D8A7E", "#553C15", "#DCD2D5", "#FFCA95", "#DAC5E4", "#DB9F62", "#FFDBD9", "#BC9F95", "#E49291", "#484C50", "#FAB36C", "#9A6343", "#E57B63", "#BED7DA", "#92392F", "#D7CEC2", "#FF9500", "#D0BBC1", "#759980", "#ADC0DF", "#E5BFD2", "#E0CC84", "#C0D0C5", "#CDD25E", "#FFC7C3", "#C1FFF1", "#C69658", "#F1F98E", "#61693C", "#A86567", "#FB4C26", "#88A259", "#FFFFC5", "#A5C18A", "#CAEBFF", "#C6A5D1", "#A4845C", "#5D531A", "#DDCFA2", "#D67F45", "#AA3B33", "#FFC667"] },
		{ name: "midTones", colors: ["#8A98DB", "#3C59BA", "#162651", "#73A278", "#B7F5DF", "#F8F7A0", "#5CC3A1", "#92973C", "#5D6448", "#8B775B", "#A64E1B", "#44475F", "#714B96", "#646DC4", "#D02154", "#F171A4", "#6F1842", "#ECA991", "#F8C12F", "#ED8F11", "#F1CFC4", "#D8BEF3", "#F8FA2C", "#324886", "#2E0A1C", "#A18BB6", "#70A3DD", "#0A0D46", "#0A0D46", "#55D3EB", "#CDFFD5", "#79DAC7", "#348796", "#94E3A7", "#19686D", "#18A564", "#18775A", "#47BD9A", "#36773D", "#98BC8E", "#B4B73A", "#7DC96B", "#52A095", "#1184B0", "#D8BEF3", "#1E4AB3", "#B1B7A1", "#848BD4", "#AAB5D2", "#B1B7A1", "#4A67C5"] },
		{ name: "mixed", colors: ["#BF2D30", "#5E8D34", "#855E26", "#000000", "#9FC2FF", "#C380B7", "#5174BE", "#D86934", "#9648B3", "#1E4BCC", "#D150B1", "#59B6E6", "#A36079", "#C9EEFF", "#7BD76C", "#FFE648", "#E4F6C2", "#9BAEAE", "#A2FFBA", "#8AB749", "#57C0C2", "#F3E5D8", "#FFF285", "#AAFF45", "#E0C18C", "#BEE7A4", "#37942D", "#DDFF00", "#216C54", "#DDF78F", "#617461", "#8BE767", "#D89A7C", "#7C8B59", "#234172", "#A3AD68", "#0C866D", "#827894", "#10869F", "#BD19CD", "#A087CB", "#5980E7", "#8F5CAD", "#5054A0", "#AE7C32", "#172730", "#8B113C", "#2A573F", "#2F0F4E", "#A36E68"] },
		{ name: "pastel", colors: ["#2A3D2F", "#BCD0A4", "#F7FFD1", "#AE708A", "#F5ADBD", "#F8D4DC", "#B1E6FF", "#7AAEB3", "#8FAF6E", "#101811", "#B6C4B0", "#FFBEC4", "#D06F71", "#DFCC65", "#BBD8BA", "#D77856", "#FDFFB6", "#E67347", "#B8EDFF", "#47C3F2", "#F89E8B", "#834C37", "#A1C261", "#E2FFCA", "#3B491F", "#52A095", "#93B7D3", "#647AAA", "#D7FFFF", "#FFFFD4", "#3F4E5D", "#A4BD74", "#FFC49A", "#FF6044", "#CAD5C9", "#74A05B", "#738368", "#9B4427", "#BE8B94", "#FF8C63", "#D9FFFF", "#88ADC8", "#D5A37F", "#FFDACA", "#E488A1", "#E997CC", "#52A095", "#849B50", "#618C73", "#C9DCB6"] },
		{ name: "poppies", colors: ["#D1CEBC", "#914927", "#000000", "#D0004A", "#E7C8C6", "#F8EB00", "#FFFBD0", "#C75D65", "#C3007C", "#009F3C", "#008873", "#0098A6", "#46648E", "#8EC7E6", "#827D4E", "#6FA573", "#737D83", "#DFB7AD", "#FF2525", "#E49F76", "#F3EAF2", "#FBF794", "#FF6E6E", "#E20106", "#A01717", "#4B2722", "#00892F", "#9CCB96", "#85D1D2", "#15EDEA", "#35C7F1", "#186F3F", "#67801C", "#655567", "#905D8D", "#F64041", "#A92E2E", "#E38B97", "#FBC197", "#B5B3B3", "#FEDD96", "#FDD1F2", "#DB3919", "#880104", "#420011", "#00CE74", "#98E600", "#00FC88", "#00A3D2", "#00447D"] },
		{ name: "spring", colors: ["#226309", "#3BDB09", "#F0FF5E", "#BCE86C", "#7A7124", "#AA9D2B", "#5C770E", "#C7E207", "#CBB60B", "#3E8908", "#0A1C0C", "#FFFA3C", "#7CE96C", "#514B18", "#B09F09", "#AFD408", "#F8E00E", "#4FA307", "#15480D", "#AFFF3C", "#513A10", "#7E7109", "#AFD408", "#15480D", "#2D992A", "#98BB7B", "#5D5F30", "#7C833D", "#405721", "#7FA432", "#8B932E", "#306422", "#0A1C0C", "#B3C45E", "#7EBC7D", "#7CBC80", "#3A3D1E", "#7B812B", "#6E9C30", "#ABB53B", "#3E7726", "#153415", "#87AD56", "#5D5F30"] },
		{ name: "warmEarth", colors: ["#D0B85D", "#648F8C", "#F89200", "#F9F29B", "#A7D3EF", "#FAF168", "#676A35", "#F0C963", "#FFEF7A", "#C6D332", "#AC8827", "#F3D864", "#FFFE49", "#D3ED62", "#B2AD61", "#687C3D", "#B0A89A", "#B07286", "#F6B899", "#CEA95C", "#6E581C", "#CFB999", "#F5A864", "#B4CC9D", "#516862", "#D4F8A2", "#4E150A", "#CF6E27", "#718E25", "#8F889E", "#D0C899", "#6F685B", "#B3AC29", "#E9D236", "#70370C", "#786766", "#9D721A", "#B4DA55", "#B06F52", "#324C19", "#36773D", "#98BC8E", "#483E46", "#D8BEF3", "#13706B", "#38384F", "#738C5E", "#FFDE00", "#FF6400", "#998A50"] },
		{ name: "waterMeadow", colors: ["#173C64", "#648F8C", "#51CFFF", "#95FFFF", "#A7D3EF", "#03343C", "#676A35", "#C49D35", "#FFEF7A", "#C6D332", "#B0EBBA", "#E1FFB6", "#FFFE49", "#9E6800", "#181F03", "#687C3D", "#CDFFD5", "#DEFCFF", "#E3DEF2", "#2F2F69", "#6E6EBE", "#03639F", "#55D3EB", "#F1C4B5", "#000A3F", "#52A095", "#1184B0", "#11B08C", "#7DC96B", "#9A9865", "#CCCD6D", "#FFE23E", "#FDFDB7", "#FAB07A", "#70370C", "#786766", "#002559", "#5B93E0", "#9893BD", "#5A809F", "#36773D", "#98BC8E", "#F2CA8E", "#D8BEF3", "#13706B", "#38384F", "#2CA6CB", "#08F4BA", "#8DF36E", "#998A50"] },
		{ name: "darkRainbow", colors: ["#640000", "#C86400", "#A08C00", "#006400", "#000064", "#320064", "#640064"] },
		{ name: "midRange", colors: ["#4682B4", "#9ACD32", "#708090", "#CD853F", "#B22222", "#FFA500", "#FFFFFF", "#FF4500", "#A0522D", "#FFD700", "#3CB371", "#54A9DD", "#6A5ACD", "#4169E1", "#9370DB", "#BA55D3", "#66CDAA", "#D8BFD8", "#FF69B4", "#DB7093"] },
		{ name: "vividDark", colors: ["#8B0000", "#FF8C00", "#8B4513", "#B8860B", "#808000", "#6B8E23", "#556B2F", "#006400", "#2E8B57", "#008B8B", "#00BFFF", "#4682B4", "#000080", "#483D8B", "#4B0082", "#800080", "#DC143C", "#000000", "#9400D3"] },
		{ name: "fiveColor1", colors: ["#468966", "#FFF0A5", "#FFB03B", "#B64926", "#8E2800"] },
		{ name: "fiveColor2", colors: ["#7D8A2E", "#C9D787", "#F9D690", "#FFC0A9", "#FF8598"] },
		{ name: "fiveColor3", colors: ["#393A3D", "#DB0048", "#5D9E9B", "#C8DBBF", "#E0FCEB"] },
		{ name: "fiveColor4", colors: ["#595441", "#B9B09D", "#DDE8EB", "#69BFDE", "#4A8797"] },
		{ name: "fiveColor5", colors: ["#7D7E9A", "#BFC7B2", "#D8D6B0", "#D0C3B3", "#9B7A76"] },
		{ name: "fiveColor6", colors: ["#C44C51", "#FFB6B8", "#FFEFB6", "#A2B5BF", "#5F8CA3"] },
		{ name: "fiveColor7", colors: ["#FCFFF5", "#D1DBBD", "#91AA9D", "#3E606F", "#193441"] },
		{ name: "fiveColor8", colors: ["#B0CC99", "#677E52", "#B7CA79", "#F6E8B1", "#89725B"] },
		{ name: "fiveColor9", colors: ["#F7F2B2", "#ADCF4F", "#84815B", "#4A1A2C", "#8E3557"] },
		{ name: "fiveColor10", colors: ["#E8E595", "#D0A825", "#40627C", "#26393D", "#FFFAE4"] },
		{ name: "fiveColor11", colors: ["#5A1F00", "#D1570D", "#FDE792", "#477725", "#A9CC66"] },
		{ name: "fiveColor12", colors: ["#595241", "#B8AE9C", "#DED7C6", "#ACCFCC", "#8A0917"] },
		{ name: "fiveColor13", colors: ["#66858D", "#93A299", "#C0BFA9", "#FFE7D5", "#FFBEAC"] },
		{ name: "fiveColor14", colors: ["#FFF0A3", "#B8CC6E", "#4B6000", "#E4F8FF", "#004460"] },
		{ name: "fiveColor15", colors: ["#C7F2F5", "#47A0A1", "#781515", "#B8864C", "#D9C39C"] },
		{ name: "fiveColor16", colors: ["#3B1801", "#82561A", "#FFF3D2", "#ABB886", "#4F462E"] },
		{ name: "fiveColor17", colors: ["#C3FF93", "#969865", "#6A4A2C", "#C1420E", "#FF8C47"] },
		{ name: "fiveColor18", colors: ["#759C52", "#C2E858", "#FFE3A6", "#E8A298", "#9C5241"] },
		{ name: "fiveColor19", colors: ["#123B4A", "#E6FFFF", "#C7C09A", "#FFE427", "#FFA908"] },
		{ name: "fiveColor20", colors: ["#ED9877", "#B0B9AD", "#AE555B", "#485566", "#837076"] },
		{ name: "fiveColor21", colors: ["#B8ECD7", "#083643", "#B1E001", "#CEF09D", "#476C5E"] },
		{ name: "fiveColor22", colors: ["#323240", "#968C63", "#FDFFC9", "#FFD175", "#C3C967"] },
		{ name: "fiveColor23", colors: ["#F0E14C", "#FFBB20", "#FA7B12", "#E85305", "#59CC0D"] },
		{ name: "fiveColor24", colors: ["#405952", "#9C9B7A", "#FFD393", "#FF974F", "#F54F29"] },
		{ name: "fiveColor25", colors: ["#762B1B", "#807227", "#CCBF7A", "#FFEF98", "#60B0A1"] },
		{ name: "fiveColor26", colors: ["#5D4970", "#372049", "#F1BAF3", "#FAEEFF", "#59535E"] },
		{ name: "fiveColor27", colors: ["#F9EBAE", "#789898", "#3C6573", "#E8B54D", "#B14D1C"] },
		{ name: "fiveColor28", colors: ["#B9121B", "#4C1B1B", "#F6E497", "#FCFAE1", "#BD8D46"] },
		{ name: "fiveColor29", colors: ["#B6D09C", "#727368", "#5C5C66", "#533E3E", "#CC9585"] },
		{ name: "fiveColor30", colors: ["#8F9AB3", "#B0D0DF", "#F1F3F0", "#FFACA1", "#A61618"] },
		{ name: "fiveColor31", colors: ["#FF5B2B", "#B1221C", "#34393E", "#8CC6D7", "#FFDA8C"] },
		{ name: "fiveColor32", colors: ["#E51E19", "#FC7529", "#F9F23D", "#8DEA55", "#0962B4"] },
		{ name: "fiveColor33", colors: ["#FD814C", "#2DD5EC", "#FF364D", "#A1F886", "#FFF37D"] },
		{ name: "fiveColor34", colors: ["#324732", "#EADAD2", "#7E1F2D", "#A1861D", "#97A9CA"] },
		{ name: "fiveColor35", colors: ["#FA5E73", "#E6DDD3", "#99CE5D", "#43456D", "#EB9765"] },
		{ name: "fiveColor36", colors: ["#3A8394", "#C6EDF7", "#D9F2B0", "#FFD1C1", "#B71E4F"] },
		{ name: "fiveColor37", colors: ["#463531", "#F57C75", "#C380C9", "#21D4F6", "#A5EBB0"] },
		{ name: "fiveColor38", colors: ["#997F87", "#F7D6CD", "#E6FCB5", "#C5E0DC", "#454B65"] },
		{ name: "fiveColor39", colors: ["#463531", "#F57C75", "#C380C9", "#21D4F6", "#A5EBB0"] },
		{ name: "fiveColor40", colors: ["#070707", "#FBDB5B", "#EC3608", "#80C3FF", "#448B14"] },
		{ name: "fiveColor41", colors: ["#E37484", "#CEDDE4", "#99B18D", "#E9CD6C", "#EAB79A"] },
		{ name: "fiveColor42", colors: ["#CAD6C3", "#5E7277", "#F99DB1", "#FBBFCC", "#F6E1DC"] },
		{ name: "fiveColor43", colors: ["#F9FDF3", "#2B3532", "#D33D3E", "#C3B9AD", "#005773"] },
		{ name: "fiveColor44", colors: ["#94B13A", "#2C81C0", "#6B4687", "#960303", "#F16723"] },
		{ name: "fiveColor45", colors: ["#DCE7B5", "#B0C4CE", "#FE4F13", "#E6384D", "#17304B"] },
		{ name: "fiveColor46", colors: ["#DA1C1C", "#68DA1C", "#F1E808", "#21A4D8", "#FC6A08"] }
	];

})($global, window.jQuery);
})();
