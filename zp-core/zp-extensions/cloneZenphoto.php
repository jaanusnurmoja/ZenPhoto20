<?php

/**
 * "Clones" the currrent installation to a new location using symlinks. The <i>zp-core</i>, <i>themes</i>, <i>user plugins</i>
 * folders and the root <i>index.php</i> file are symlinked. Setup will create the other needed folders.
 *
 * The <i>Clone installation</i> button will take you to the cloning page where you can select a folder destination
 * for the cloned installation. Upon successful cloning there will be a link to <var>setup</var> for the new
 * installation. (This presumes the <var>setup</var> files are present. If not you will be told which files
 * need to be reloaded.)
 *
 * <b>Note:</b> If the destination already has a installation these files and folders will be removed by the cloning
 * process!
 *
 * The <i>Delete setup scripts</i> button will remove the <var>setup</var> files from the current installation. This is
 * the same function provided by <i>Setup</i> after a successful install. It is provided here because you will likely not want to
 * remove the setup scripts until you have cloned and installed all desired destinations.
 *
 * @author Stephen Billard (sbillard)
 *
 * @package plugins
 * @subpackage admin
 */
$plugin_is_filter = 5 | ADMIN_PLUGIN;
$plugin_description = gettext('Allows multiple installations to share a single set of script files.');
$plugin_author = "Stephen Billard (sbillard)";
$plugin_disable = (SYMLINK) ? (zpFunctions::hasPrimaryScripts()) ? false : gettext('Only the primary installation may clone offspring installations.') : gettext('Your server does not support symbolic linking.');

require_once(SERVERPATH . '/' . ZENFOLDER . '/reconfigure.php');
if ($plugin_disable) {
	enableExtension('cloneZenphoto', 0);
} else {
	zp_register_filter('admin_utilities_buttons', 'cloneZenphoto::button');

	class cloneZenphoto {

		static function button($buttons) {
			$buttons[] = array(
							'category'		 => gettext('Admin'),
							'enable'			 => true,
							'button_text'	 => gettext('Clone installation'),
							'formname'		 => 'cloneZenphoto',
							'action'			 => FULLWEBPATH . '/' . ZENFOLDER . '/' . PLUGIN_FOLDER . '/cloneZenphoto/cloneTab.php',
							'icon'				 => 'images/folder.png',
							'title'				 => gettext('Create a new installation using links to the current install files.'),
							'alt'					 => gettext('Clone'),
							'hidden'			 => '',
							'rights'			 => ADMIN_RIGHTS
			);
			return $buttons;
		}

		static function setup() {
			global $_zp_current_admin_obj;
			$clones = array();
			if ($result = query('SELECT * FROM ' . prefix('plugin_storage') . ' WHERE `type`="clone"')) {
				while ($row = db_fetch_assoc($result)) {
					if (file_exists($row['aux'] . '/' . DATA_FOLDER . '/zenphoto.cfg.php')) {
						$clones[$row['aux']] = $row['data'] . '/' . ZENFOLDER . '/setup/index.php?autorun';
						$_SESSION['admin'][bin2hex($row['aux'])] = serialize($_zp_current_admin_obj);
					} else {
						query('DELETE FROM ' . prefix('plugin_storage') . ' WHERE `id` = ' . $row['id']);
					}
				}
				db_free_result($result);
			}
			return $clones;
		}

	}

}
?>