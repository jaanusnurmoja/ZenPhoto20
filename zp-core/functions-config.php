<?php

/**
 * configuration handler functions
 *
 * @author Stephen Billard (sbillard)
 *
 * @package core
 */

/**
 * Updates an item in the configuration file
 * @param unknown_type $item
 * @param unknown_type $value
 * @param unknown_type $quote
 */
function updateConfigItem($item, $value, $zp_cfg, $quote = true) {
	if ($quote) {
		$value = '"' . $value . '"';
	}
	$i = strpos($zp_cfg, $item);
	if ($i === false) {
		$parts = preg_split('~\/\*.*Do not edit below this line.*\*\/~', $zp_cfg);
		if (isset($parts[1])) {
			$zp_cfg = $parts[0] . "\$conf['" . $item . "'] = " . $value . ";\n/** Do not edit below this line. **/" . $parts[1];
		} else {
			zp_error(gettext('The configuration file is corrupt. You will need to restore it from a backup.'));
		}
	} else {
		$i = strpos($zp_cfg, '=', $i);
		$j = strpos($zp_cfg, "\n", $i);
		$zp_cfg = substr($zp_cfg, 0, $i) . '= ' . $value . ';' . substr($zp_cfg, $j);
	}
	return $zp_cfg;
}

/**
 * backs-up and updates the configuration file
 *
 * @param string $zp_cfg
 */
function storeConfig($zp_cfg) {
//	debugLogBacktrace(gettext('Updating the configuration file'));
	$mod = fileperms(SERVERPATH . '/' . DATA_FOLDER . '/' . CONFIGFILE) & 0777;
	@rename(SERVERPATH . '/' . DATA_FOLDER . '/' . CONFIGFILE, $backkup = SERVERPATH . '/' . DATA_FOLDER . '/' . stripSuffix(CONFIGFILE) . '.bak.php');
	@chmod($backup, $mod);
	file_put_contents(SERVERPATH . '/' . DATA_FOLDER . '/' . CONFIGFILE, $zp_cfg);
	@chmod($backup, SERVERPATH . '/' . DATA_FOLDER . '/' . CONFIGFILE, $mod);
}

?>