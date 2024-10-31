(function() {
	tinymce.create('tinymce.plugins.rahrayan', {
		init : function(ed, url) {
			ed.addButton('rahrayan', {
				title : 'ره رایان پیامک',
				image : url + '/logo2.png',
				onclick : function() {
					ed.windowManager.open({
						file : url + "/dialog.php",
						width : 370,
						height : 180,
						inline : 1,
						popup_css : false
					})
				}
			});
		},
		createControl : function(n, cm) {
			return null;
		},
	});
	tinymce.PluginManager.add('rahrayan', tinymce.plugins.rahrayan);
})();
