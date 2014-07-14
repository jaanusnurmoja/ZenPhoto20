<?php
/**
 * ZenPhoto20 object paster for tinyMCE
 *
 * @author Stephen Billard (sbillard)
 *
 * copyright © 2014 Stephen L Billard
 *
 */
// force UTF-8 Ø
define('OFFSET_PATH', 3);
require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . "/admin-globals.php");
admin_securityChecks(ALBUM_RIGHTS | ZENPAGE_PAGES_RIGHTS | ZENPAGE_NEWS_RIGHTS, NULL);

header('Last-Modified: ' . ZP_LAST_MODIFIED);
header('Content-Type: text/html; charset=' . LOCAL_CHARSET);
?>
<!DOCTYPE html>
<html>
	<head>
		<?php printStandardMeta(); ?>
		<title>tinyMCE:obj</title>
		<script type="text/javascript" src="<?php echo WEBPATH . '/' . ZENFOLDER; ?>/js/jquery.js"></script>
		<script type="text/javascript" src="pasteobj_popup.js"></script>

	</head>

	<body>
		<h2><?php echo gettext('ZenPhoto20 object insertion'); ?></h2>
		<?php
		if (isset($_SESSION['pick'])) {
			$args = $_SESSION['pick'];
			if (isset($args['album'])) {
				if (isset($args['image'])) {
					$obj = newImage(NULL, array('folder' => $args['album'], 'filename' => $args['image']));
					$title = gettext('<em>image</em>: %s');
					$token = gettext('image with link to image');
					if (isset($args['picture'])) {
						$image = $args['picture'];
					} else {
						$image = $obj->getThumb();
					}
				} else {
					$obj = newAlbum($args['album']);
					$title = gettext('<em>album</em>: %s');
					$token = gettext('image with link to album');
					$image = $obj->getThumb();
				}
				// an image type object
			} else {
				// a simple link
				$image = false;
				if (isset($args['news'])) {
					$obj = newNews($args['news']);
					$title = gettext('<em>news article</em>: %s');
					$token = gettext('title with link to news article');
				}
				if (isset($args['pages'])) {
					$obj = newPage($args['pages']);
					$title = gettext('<em>page</em>: %s');
					$token = gettext('title with link to page');
				}
				if (isset($args['news_categories'])) {
					$obj = newCategory($args['news_categories']);
					$title = gettext('<em>category</em>: %s');
					$token = gettext('title with link to category');
				}
			}
			$link = $obj->getLink();
			if ($image && $obj->table == 'images') {
				$link2 = $obj->album->getLink();
			} else {
				$link2 = false;
			}
			?>
			<script type="text/javascript">
				// <!-- <![CDATA[
				var link = '<?php echo $link; ?>';
				var link2 = '<?php echo $link2; ?>';
				var title = '<?php echo html_encodeTagged($obj->getTitle()); ?>';
				var image = '<?php echo $image; ?>';


				function zenchange() {
					var selectedlink = $('input:radio[name=link]:checked').val();
					switch (selectedlink) {
						case 'none':
							if ($('#addcaption').prop('checked')) {
								caption = '<figcaption>' + title + '</figcaption>';
							} else {
								caption = '';
							}
							$('#content').html('<figure><img src="' + image + '" />' + caption + '</figure>');
							break;
						case 'title':
							if (image) {
								$('#content').html('<a href="">' + title + '</a>');
							} else {
								$('#content').html(title);
							}
							break;
						case 'link':
							if (image) {
								if ($('#addcaption').prop('checked')) {
									caption = '<figcaption><a href="' + link + '" title="' + title + '">' + title + '</a></figcaption>';
								} else {
									caption = '';
								}
								$('#content').html('<figure><a href="' + link + '" title="' + title + '"><img src="' + image + '" /></a>' + caption + '</figure>');
							} else {
								$('#content').html('<a href="' + link + '" title="' + title + '">' + title + ' </a>');
							}
							break;
						case 'link2':
							if ($('#addcaption').prop('checked')) {
								caption = '<figcaption><a href="' + link2 + '" title="' + title + '">' + title + '</a></figcaption>';
							} else {
								caption = '';
							}
							$('#content').html('<figure><a href="' + link2 + '" title="' + title + '"><img src="' + image + '" /></a>' + caption + '</figure>');
							break;
					}
				}

				function paste() {
					pasteObjPopup.execCommand('mceInsertContent', false, $('#content').html());
					pasteObjPopup.close();
				}

				window.onload = function() {
					zenchange();
				};
				// ]]> -->
			</script>
			<h3>
				<span class="buttons">
					<button type="button" title="<?php echo gettext('paste'); ?>" onclick="paste();">
						<img src="<?php echo WEBPATH . '/' . ZENFOLDER; ?>/images/pass.png" onclick="paste();"  />
						<?php echo gettext('paste'); ?>
					</button>
				</span>
				<?php printf($title, html_encodeTagged($obj->getTitle())); ?>

			</h3>
			<p>
				<?php
				if ($image) {
					?>
					<label class="nowrap"><input type="radio" name="link" value="none" id="link_none" onchange="zenchange();" /><?php echo gettext('image only'); ?></label>
					<?php
				} else {
					?>
					<label class="nowrap"><input type="radio" name="link" value="title" id="link_title" onchange="zenchange();" /><?php echo gettext('title only'); ?></label>
					<?php
				}
				?>
				<label class="nowrap"><input type="radio" name="link" value="link" id="link_on" checked="checked" onchange="zenchange();" /><?php echo $token; ?>
				</label>
				<?php
				if ($link2) {
					?>
					<label class="nowrap">
						<input type="radio" name="link" value="link2" id="link_album" onchange="zenchange();" />
						<?php echo gettext('image with link to album'); ?>
					</label>
					<?php
				}
				if ($image) {
					?>
					<br />
					<label><input type="checkbox" name="addcaption" id="addcaption" onchange="zenchange()"	/><?php echo gettext('Include caption'); ?></label>
					<?php
				}
				?>
			</p>


			<div id="content"></div>
			<?php
		} else {
			?>
			<p>
				<?php printf(gettext('No source has been picked. You can pick a ZenPhoto20 object for insertion by browsing to the object and clicking on the %s icon. Custom sized and cropped images may be picked from the <em>crop image</em> page if the <code>crop_image</code> plugin is enabled.'), '<img src="' . WEBPATH . '/' . ZENFOLDER . '/images/add.png" />'); ?>
			</p>
			<?php
		}
		?>
	</body>
</html>