jQuery(function($) {	var myNicEditor = new nicEditor({		iconsPath: '/shaded/components/nicEdit/mooIconsLarge.png',			buttonList : ['save','bold','italic','left','center','right','justify','ol','ul','fontFormat','indent','outdent','image','upload','link','unlink'],	});	myNicEditor.setPanel($("#editHeader .editContainer").get(0));		var editables = $(".editable");	editables.each(function() {		myNicEditor.addInstance(this);	});		window.saveContent = function() {		var content = {};				editables.each(function() {			content[$(this).parent().attr("id").substr(7)] = {				type: "content",				content: $(this).html()			};		});				jQuery.ajax({			url: "/shaded/admin/layout/setContent",			type: "post",			data: {				page: 1,				widgets: content			},			success: function() {				console.log("done");			}		});	};/*	$(".editable").simpledit({		//buttonPanel: "#editHeader .editContainer",		buttons: ["bold", "italic"]	});*//*	CKEDITOR.config.toolbar_Full = [		["Bold", "Italic", "-", "NumberedList", "BulletedList", "-", "Outdent", "Indent", "-", "Link", "Unlink", "-", "Image", '-', "Styles", "MoreOptions"],		'/',		["Cut", "Copy", "Paste", '-', 'Undo', 'Redo', '-'],		['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],		['SpecialChar', '-', 'Blockquote', '-', 'HorizontalRule', '-', 'Find', 'Replace', '-', 'SpellChecker', "-", "Source"]	];	function MoreOptions(editor) {		var toolbarLocation = editor.config.toolbarLocation;		var toolbarDiv = document.getElementById('cke_' + toolbarLocation + '_' + editor.name);		console.log(toolbarDiv);		$(toolbarDiv).find('.cke_toolbar,.cke_break').each(function (index) {			if ($(this).hasClass('cke_break')) {				if (moreoptions == true) {					console.log(this);					$(this).toggle();				}				return false;			}		});	}	CKEDITOR.stylesSet.add('default', [		{ name: 'Heading 1', element: 'h1' },		{ name: 'Heading 2', element: 'h2' },		{ name: 'Heading 3', element: 'h3' },		{ name: 'Paragraph', element: 'p' },		{ name: 'Call To Action', element: 'a', attributes: { 'class': 'CallToAction'} },		{ name: 'Good vs. Bad List', element: 'ul', attributes: { 'class': 'GoodvsBad'} },		{ name: '- Good Item', element: 'li', attributes: { 'class': 'Good'} },		{ name: '- Bad Item', element: 'li', attributes: { 'class': 'Bad'} },		{ name: 'CSS Style', element: 'span', attributes: { 'class': 'my_style'} }	]);	CKEDITOR.plugins.add('MoreOptions', {		init: function (editor) {			editor.addCommand('MoreOptions', {				exec: function (editor) {					MoreOptions(editor);				}			});			editor.ui.addButton('MoreOptions', {				label: 'More Options',				icon: 'images/moreoptions.png',				command: 'MoreOptions'			});		}	});*/	CKEDITOR.config.keystrokes = [		[CKEDITOR.ALT + 121 /*F10*/, 'toolbarFocus'],		[CKEDITOR.ALT + 122 /*F11*/, 'elementsPathFocus'],		[CKEDITOR.SHIFT + 121 /*F10*/, 'contextMenu'],		[CKEDITOR.CTRL + 90 /*Z*/, 'undo'],		[CKEDITOR.CTRL + 89 /*Y*/, 'redo'],		[CKEDITOR.CTRL + CKEDITOR.SHIFT + 90 /*Z*/, 'redo'],		[CKEDITOR.CTRL + 76 /*L*/, 'link'],		[CKEDITOR.CTRL + 66 /*B*/, 'bold'],		[CKEDITOR.CTRL + 73 /*I*/, 'italic'],		[CKEDITOR.CTRL + 85 /*U*/, 'underline'],		[CKEDITOR.CTRL + 86 /*V*/, 'pastefromword'],		[CKEDITOR.SHIFT + 45 /*INS*/, 'pastefromword'],		[CKEDITOR.ALT + 109 /*-*/, 'toolbarCollapse']	];	CKEDITOR.config.pasteFromWordRemoveStyles = true;	CKEDITOR.config.pasteFromWordRemoveFontStyles = true;	CKEDITOR.config.extraPlugins = "MoreOptions";	CKEDITOR.config.contentsCss = ["/shaded/css/reset.css", "/shaded/css/screen.css", "/shaded/css/style.css"];//	CKEDITOR.config.toolbar = 'Full';//	CKEDITOR.config.height = 400;/*	$(".editable").ckeditor({		sharedSpaces: {			top : "editContainer"		},		on: {			instanceReady: function (ev) {				MoreOptions(this);				ev.editor._.commands.paste = ev.editor._.commands.pastefromword;			}		},		skin: 'wool-inline'	});	*/		//$('#slider').nivoSlider();});