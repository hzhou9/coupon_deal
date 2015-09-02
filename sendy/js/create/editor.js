$(document).ready(
	function()
	{
		CKEDITOR.replace( 'html', {
			fullPage: true,
			allowedContent: true,
			filebrowserUploadUrl: 'includes/create/upload.php',
			height: '570px',
			extraPlugins: 'codemirror'
		});
	}
);