<?php

/**
 * The configuration parameters for TinyMCE 4.x.
 *
 * Comment form plugin default light configuration
 * @author Stephen Billard (sbillard)
 */
$MCEcss = 'dark_content.css';
$MCEskin = "tundora";
$MCEselector = "textarea.textarea_inputbox";
$MCEplugins = "advlist autolink lists link image charmap print preview hr anchor pagebreak " .
				"searchreplace visualblocks code " .
				"insertdatetime media contextmenu " .
				"emoticons paste";
$MCEtoolbars[1] = "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | preview | emoticons | code";
$MCEstatusbar = false;
$MCEmenubar = false;
include(SERVERPATH . '/' . ZENFOLDER . '/' . PLUGIN_FOLDER . '/tinymce/config/config.js.php');
