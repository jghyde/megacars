<?php 
?>

<html>
<style type="text/css">

body {
	color: #444;
	font-family: "Open Sans",sans-serif;
	font-size: 13px;
	line-height: 1.4em;
}

.clickmeter-button-wpstyle{
	text-align: center;
	color: #555;
	border-color: #ccc;
	background: #f7f7f7;
	-webkit-box-shadow: inset 0 1px 0 #fff,0 1px 0 rgba(0,0,0,.08);
	box-shadow: inset 0 1px 0 #fff,0 1px 0 rgba(0,0,0,.08);
	vertical-align: top;
	display: inline-block;
	text-decoration: none;
	font-size: 13px;
	line-height: 26px;
	height: 28px;
	margin: 0;
	padding: 0 10px 1px;
	cursor: pointer;
	border-width: 1px;
	border-style: solid;
	-webkit-appearance: none;
	-webkit-border-radius: 3px;
	border-radius: 3px;
	white-space: nowrap;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}

a {
	color: #0074a2;
	-webkit-transition-property: border,background,color;
	transition-property: border,background,color;
	-webkit-transition-duration: .05s;
	transition-duration: .05s;
	-webkit-transition-timing-function: ease-in-out;
	transition-timing-function: ease-in-out;
}

input.link_button {
	background:none;
	border:none; 
	padding:0;
	cursor: pointer;
	text-decoration: underline;
	color:#0074a2;
	line-height: 1.4em;
	font-size: 13px;
}
</style>

<script type="text/javascript">

	var args = top.tinymce.activeEditor.windowManager.getParams();

	function copyToClipboard() {
		window.prompt("Copy to clipboard: Ctrl+C, Enter", args.arg2);
	}
	
	document.write('<center>');
	document.write('<br><a style="font-size:14px" id="clickmeter_popup_tl" target="_blank" href="'+args.arg2+'">'+args.arg2+'</a><br><br>');
	document.write('<a title="edit tracking link on ClickMeter" target="blank" href="http://my.clickmeter.com/go?val='+args.arg4+'&returnUrl=%2Flinks%2Fedit%2F'+args.arg3+'">Edit</a> | <a title="view info about this tracking link on ClickMeter" target="_blank" href="http://my.clickmeter.com/go?val='+args.arg4+'&returnUrl=%2FLinks%3FlinkId%3D'+args.arg3+'">Stats</a> | <input title="copy to clipboard" type="button" class="link_button" value="Copy" onclick="copyToClipboard()"/>');
	document.write('</center>');

</script>
<body>

</body>
</html>
