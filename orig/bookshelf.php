<?php
	/*
	Plugin Name: WorPress Goodreads Bookshelf
	Version: beta
	Plugin URI: http://thoughtcramps.com/wordpress-goodreads-bookshelf/
	Description: Display a custom list of books from your <a href="http://www.goodreads.com" target="_blank">Goodreads</a> bookshelves.  You can choose from any one of your bookshelves including your custom shelves on Goodreads.  Then you can stylize the display of the list however you want using your own HTML and CSS.
	Author: Sabrina Whaley
	Author URI: http://thoughtcramps.com/
	*/


	/*
	Copyright (c) 2009 Sabrina Whaley, http://thoughtcramps.com/
	Loosely based on fmTuner by Collin Allen, http://www.command-tab.com/

	Permission is hereby granted, free of charge, to any person obtaining
	a copy of this software and associated documentation files (the
	"Software"), to deal in the Software without restriction, including
	without limitation the rights to use, copy, modify, merge, publish,
	distribute, sublicense, and/or sell copies of the Software, and to
	permit persons to whom the Software is furnished to do so, subject to
	the following conditions:

	The above copyright notice and this permission notice shall be
	included in all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
	EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
	NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
	LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
	OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
	WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
	*/


	// Display books on your shelf, no duplicates.
	function bookshelf()
	{
		if (function_exists('simplexml_load_string') && function_exists('file_put_contents'))
		{
			// Fetch options from WordPress DB and set up variables
			$iCacheTime = get_option('bookshelf_update_frequency');
			$sCachePath = get_option('bookshelf_cachepath');
			$iBookLimit = get_option('bookshelf_book_limit');
			$sBaseUrl = 'http://www.goodreads.com/review/list_rss/';
			$sUserId = get_option('bookshelf_userid');
			$sUserPrivate = get_option('bookshelf_private');
			$sSecretKey = get_option('bookshelf_secret_key');
			$sShelf = get_option('bookshelf_shelf');
			$sCustomShelf = get_option('bookshelf_custom');
			$sSort = get_option('bookshelf_sort');
			$sOrder = get_option('bookshelf_order');
			$sApiKey = '9b5Ah7bNgosnx5k01YGX0g';
			$sDisplayFormat = get_option('bookshelf_display_format');
			if (isset($sSecretKey)) {
				$sKey = $sSecretKey;
			} else {
				$sKey = $sApiKey;
			}
			$sApiUrl = "{$sBaseUrl}{$sUserId}?key={$sKey}&method=reviews.list&limit={$iBookLimit}&shelf={$sShelf}{$sCustomShelf}&sort={$sSort}&order={$sOrder}";

			// Check if we're using images or not
			$bUsingImages = false;
			if (strpos($sDisplayFormat, '[cover]') === false)
			{
				$bUsingImages = false;
			}
			else
			{
				$bUsingImages = true;
			}

			// Is profile private?  If it has make sure there is a key
			if ($sUserId)
			{
				if ($sUserPrivate === 'true')
				{
					if ($sSecretKey != '')
					{
						$bUserId = true;
					}
					else
					{
						$bUserId = false;
						echo 'Your Goodreads profile must be private.  Please <a href="'.get_bloginfo('wpurl').'/wp-admin/options-general.php?page=bookshelf/bookshelf.php">set your secret key in the Bookshelf options</a> of your WordPress administration panel.';
					}
				}
				else
				{
					$bUserId = true;
				}
			}// end private check

			// Run only if the userid is set up
			if ($bUserId)
			{
				// If the cached XML exists on disk
				if (file_exists($sCachePath))
				{
					// Compare file modification time against update frequency
					if (time() - filemtime($sCachePath) > $iCacheTime)
					{
						// Cache miss
						$sBooksXml = bookshelf_fetch($sApiUrl);
						file_put_contents($sCachePath, $sBooksXml);
					}
					else
					{
						// Cache hit
						$sBooksXml = file_get_contents($sCachePath);
					}
				}
				else
				{
					// Fetch the XML for the first time
					$sBooksXml = bookshelf_fetch($sApiUrl);
					file_put_contents($sCachePath, $sBooksXml);
				}

				// Make sure this is really the file we needed
				if(strpos($sBooksXml,'<?xml') === false)
				{
						echo 'There has been an error retrieving the shelf data.  Your Goodreads profile might be private.  Please <a href="'.get_bloginfo('wpurl').'/wp-admin/options-general.php?page=bookshelf/bookshelf.php">verify the settings in the Bookshelf options</a> of your WordPress administration panel.<br />';
						echo $sApiUrl;
				}
				else
				{
					// Parse the XML
					$xBooksXml = simplexml_load_string($sBooksXml);
					$aBooks = array();
					$iTotal = 1;

					// If we have any parsed books
					if ($xBooksXml)
					{
						$xBooks = $xBooksXml->channel->item;

						// Loop over each book, outputting it in the desired format
						foreach($xBooks as $oBook)
						{
							// If we want to use images, but the current $oBook has no med image, skip it
							if ($bUsingImages && $oBook->book_medium_image_url == '')
							{
								continue;
							}

							$sAuthor = $oBook->author_name;
							$sCover = $oBook->book_medium_image_url;
							if(strpos($sCover,'nocover'))
							{
								$sCover = GetPluginUrl().'pixel_trans.gif';
							}

							// Store each book, and check it every iteration so as not to output duplicates
							$sKey = $sAuthor . ' - ' . $oBook->title;

							$sTrimReview = substr($oBook->user_review,0,200) . '...';
							$sTrimDescription = substr($oBook->book_description,0,200) . '...';
							// If the current book is not in $aBooks and we haven't hit the book limit
							if (!in_array($sKey, $aBooks) != "" && $iTotal <= $iBookLimit)
							{
								// Shove the current book into $aBooks to be checked for next time around
								$aBooks[] = $sKey;

								// Dump out the blob of HTML with data embedded
								$aTags = array(
									'/\[title\]/',
									'/\[author\]/',
									'/\[cover\]/',
									'/\[isbn\]/',
									'/\[read\]/',
									'/\[review\]/',
									'/\[short_review\]/',
									'/\[published\]/',
									'/\[description\]/',
									'/\[short_description\]/',
									'/\[user_rating\]/',
									'/\[avg_rating\]/',
									'/\[url\]/'
								);
								$aData = array(
									$oBook->title,
									$sAuthor,
									$sCover,
									$oBook->isbn,
									$oBook->user_read_at,
									$oBook->user_review,
									$sTrimReview,
									$oBook->book_published,
									$oBook->book_description,
									$sTrimDescription,
									$oBook->user_rating,
									$oBook->average_rating,
									$oBook->link
								);

								// Clean up data, prevent XSS, etc.
								foreach ($aData as $iKey => $sValue)
								{
									$aData[$iKey] = trim(strip_tags(htmlspecialchars($sValue)));
								}

								// Merge $aTags and $aData
								echo preg_replace($aTags, $aData, $sDisplayFormat);

								// Increment the counter so we can check the book limit next time around
								$iTotal++;
							}
						} // end foreach loop
					} // end if (any parsed books)
				}
			}
			else
			{
				echo 'Please <a href="'.get_bloginfo('wpurl').'/wp-admin/options-general.php?page=bookshelf/bookshelf.php">set your Bookshelf options</a> in your WordPress administration panel.';
			} // end if (userid)
		}
		else
		{
			echo 'Bookshelf requires PHP version 5 or greater.  Please contact your web host for more information.';
		} // end PHP5 check
	} // end bookshelf()



	// Fetch a given URL using file_get_contents or cURL
	function bookshelf_fetch($sUrl)
	{
		// Check if file_get_contents will work
		if ( ini_get('allow_url_fopen') && function_exists('file_get_contents') && $sUrl )
		{
			// Use file_get_contents
			return file_get_contents($sUrl);
		}
		elseif ( function_exists('curl_init') && $sUrl )
		{
			// Fall back to cURL
			$hCurl = curl_init();
			$iTimeout = 5;
			curl_setopt($hCurl, CURLOPT_URL, $sUrl);
			curl_setopt($hCurl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($hCurl, CURLOPT_CONNECTTIMEOUT, $iTimeout);
			$sFileContents = curl_exec($hCurl);
			curl_close($hCurl);
			return $sFileContents;
		}
		else
		{
			return false;
		}
	}

	// Add default options to the DB
	function add_bookshelf_options()
	{
		add_option('bookshelf_cachepath', dirname(__FILE__).'/bookshelf_cache.xml'); // Default cache location
		add_option('bookshelf_userid', ''); // Default to ''
		add_option('bookshelf_private','false'); // Default to 'false'
		add_option('bookshelf_secret_key',''); // Default to ''
		add_option('bookshelf_shelf', 'read'); // Default to 'read'
		add_option('bookshelf_custom', ''); // Default ''
		add_option('bookshelf_sort', 'avg_rating'); // Default to 'avg_rating'
		add_option('bookshelf_order', 'd'); // Default to 'd'
		add_option('bookshelf_update_frequency', 3600); // Default to 'Every hour'
		add_option('bookshelf_book_limit', 5); // Default to max of 5 books
		add_option('bookshelf_display_format', '<li><img src="[cover]" alt="[title] by [author]" style="width:45px" /><br />[title] by [author]</li>'); // Default format
	}



	// Delete the cache file and options stored in the DB
	function delete_bookshelf_options()
	{
		$sCachePath = get_option('bookshelf_cachepath');
		if (file_exists($sCachePath))
		{
			unlink($sCachePath);
		}

		delete_option('bookshelf_cachepath');
		delete_option('bookshelf_userid');
		delete_option('bookshelf_private');
		delete_option('bookshelf_secret_key');
		delete_option('bookshelf_shelf');
		delete_option('bookshelf_custom');
		delete_option('bookshelf_sort');
		delete_option('bookshelf_order');
		delete_option('bookshelf_update_frequency');
		delete_option('bookshelf_book_limit');
		delete_option('bookshelf_display_format');
	}



	// Add the options page to the admin area under Settings when called
	function setup_bookshelf_options()
	{
		add_options_page('Bookshelf Settings', 'Bookshelf', 1, __FILE__, 'bookshelf_options');
	}



	// Register Bookshelf plugin activation/deactivation hooks
	register_activation_hook(__FILE__, 'add_bookshelf_options');
	register_deactivation_hook(__FILE__, 'delete_bookshelf_options');



	// Hook into WordPress to call setup_bookshelf_options() when the admin menu is loaded
	add_action('admin_menu', 'setup_bookshelf_options');



	// Display books on your shelf, no duplicates.
	function bookshelf_test($bUserId,$iBookLimit,$iBookLimitWithBuffer,$sBooksXml)
	{
		if (function_exists('simplexml_load_string') && function_exists('file_put_contents'))
		{
			// Run only if the userid is set up
			if ($bUserId)
			{
				// Make sure this is really the file we needed
				if(strpos($sBooksXml,'<?xml') === false)
				{
						echo 'There has been an error retrieving the shelf data.';
				}
				else
				{
					// Parse the XML
					$xBooksXml = simplexml_load_string($sBooksXml);
					$aBooks = array();
					$iTotal = 1;

					// If we have any parsed books
					if ($xBooksXml)
					{
						$xBooks = $xBooksXml->channel->item;

						// Loop over each book, outputting it in the desired format
						foreach($xBooks as $oBook)
						{
							// If we want to use images, but the current $oBook has no med image, skip it
							if ($bUsingImages && $oBook->book_small_image_url == '')
							{
								continue;
							}

							$sAuthor = $oBook->author_name;

							// Store each book, and check it every iteration so as not to output duplicates
							$sKey = $sAuthor . ' - ' . $oBook->title;

							// If the current book is not in $aBooks and we haven't hit the book limit
							if (!in_array($sKey, $aBooks) != "" && $iTotal <= $iBookLimit)
							{
								// Shove the current book into $aBooks to be checked for next time around
								$aBooks[] = $sKey;

								$aData = array(
									$oBook->title,
									$sAuthor,
									$oBook->book_small_image_url,
								);

								// Clean up data, prevent XSS, etc.
								foreach ($aData as $iKey => $sValue)
								{
									$aData[$iKey] = trim(strip_tags(htmlspecialchars($sValue)));
								}

								// Merge $aTags and $aData
								echo '<li style="border-top: 1px solid #D3D3D3;clear:left"><img src="'.$oBook->book_small_image_url.'" style="width:35px;margin:4px 8px 8px;float:left;clear:left" alt="'.$oBook->title.' by '.$sAuthor.'" /><a href="#" style="display:block;clear:right"><em>'.$oBook->title.'</em> <br />--by '.$sAuthor.'</a></li>';

								// Increment the counter so we can check the book limit next time around
								$iTotal++;
							}
						} // end foreach loop
						echo $xBooksXml;
					} // end if (any parsed books)
				}
			}
			else
			{
				echo 'Please set your Bookshelf options.';
			} // end if (userid)
		}
		else
		{
			echo 'Bookshelf requires PHP version 5 or greater.  Please contact your web host for more information.';
		} // end PHP5 check
	} // end bookshelf()

	// Display the options page in wp-admin
	function bookshelf_options()
	{ ?>
		<div class="wrap" id="book_div">
<?php
		if (function_exists('simplexml_load_string') && function_exists('file_put_contents'))
		{
			// Fetch XML again, since key options (userid, shelf) may have changed
			$sBaseUrl = 'http://www.goodreads.com/review/list_rss/';
			$sShelf = get_option('bookshelf_shelf');
			$sCustomShelf = get_option('bookshelf_custom');
			$sUserId = get_option('bookshelf_userid');
			$sUserPrivate = get_option('bookshelf_private');
			$sSecretKey = get_option('bookshelf_secret_key');
			$sSort = get_option('bookshelf_sort');
			$sOrder = get_option('bookshelf_order');
			$iBookLimit = get_option('bookshelf_book_limit');
			$iBookLimitWithBuffer = $iBookLimit + 1; // Grab extra in case some books don't have artwork
			$sApiKey = '9b5Ah7bNgosnx5k01YGX0g';
			if ($sSecretKey) {
				$sKey = $sSecretKey;
			} else {
				$sKey = $sApiKey;
			}
			$sApiUrl = "{$sBaseUrl}{$sUserId}?key={$sKey}&method=reviews.list&order={$sOrder}&shelf={$sShelf}{$sCustomShelf}&sort={$sSort}";

			if ($sUserId)
			{
				if ($sUserPrivate === 'true')
				{
					if ($sSecretKey)
					{
						$bUserId = true;
					}
					else
					{
						$bUserId = false;
?>
						<div class="error" style="padding: 5px; font-weight: bold;">Your Goodreads profile is private.  Please set your secret key below.</div>
<?php
					}
				}
				else
				{
					$bUserId = true;
				}
			}// end private check

			// Run only if the userid is set up
			if ($bUserId)
			{
				$sBooksXml = bookshelf_fetch($sApiUrl);
				file_put_contents(get_option('bookshelf_cachepath'), $sBooksXml);

				$cUserPrivate = 'false';

				if(strpos($sBooksXml,'<?xml') === false)
				{
					if(strpos($sBooksXml,'404'))
					{
?>
						<div class="error" style="padding: 5px; font-weight: bold;">There has been an error retrieving the shelf data. Please verify the settings in the Bookshelf options.<br />
						<code><?php echo $sApiUrl; ?></code></div>
<?php
					}
					elseif(strpos($sBooksXml,'shelf is private'))
					{
?>
						<div class="error" style="padding: 5px; font-weight: bold;">Your <a href="http://www.goodreads.com/user/show/<?php echo $sUserId; ?>" target="_blank">Goodreads profile</a> appears to be private. Please verify that you have entered your secret key in the Bookshelf options.<br />
						<code><?php echo $sApiUrl; ?></code></div>
<?php
						$cUserPrivate = 'true';
					}
				}
			}
		}
		else
		{
?>
			<div class="error" style="padding: 5px; font-weight: bold;">Bookshelf requires PHP version 5 or greater. Please contact your web host for more information.</div>
<?php
		}
?>
			<h2>Bookshelf Settings</h2>
			<form action="options.php" method="post">
					<?php wp_nonce_field('update-options'); // Protect against XSS ?>
				<div id="poststuff" class="metabox-holder">
					<div class="inner-sidebar">
						<div id="side-sortables" class="meta-box-sortabless ui-sortable" style="position:relative;">
							<div id="book_grlogo" class="postbox">
								<div class="inside">
									<ul>
									<li></li>
									</ul>
									<a href="http://goodreads.com" title="Visit Goodreads"><img src="<?php echo GetPluginUrl(); ?>goodreads_bookmark.png" alt="Goodreads" /></a>
								</div>
							</div>
							<div id="book_grlogo" class="postbox">
								<h3 class="hndle"><span>Test Display</span></h3>
								<div class="inside">
									<p>This is a test of what your Bookshelf will display. This does not apply the display format you specified.</p>
									<ul>
										<?php bookshelf_test($bUserId,$iBookLimit,$iBookLimitWithBuffer,$sBooksXml); ?>
									</ul>
									<br style="clear:both" />
								</div>
							</div>
						</div>
					</div>
				<div class="has-sidebar sm-padded" >
					<div id="post-body-content" class="has-sidebar-content">
						<div class="meta-box-sortabless">
							<div id="book_user" class="postbox">
								<h3 class="hndle"><span>User Information</span></h3>
								<div class="inside">
									<table class="form-table">
										<tbody>
											<tr valign="top">
												<th scope="row">
													<label for="bookshelf_username">Goodreads Userid</label>
												</th>
												<td>
													<input type="text" size="25" value="<?php echo get_option('bookshelf_userid'); ?>" id="bookshelf_userid" name="bookshelf_userid" />
													<br />Enter your <a href="http://goodreads.com" target="_blank">GoodReads</a> user id.
													<br />Go to your account and click "My Books".  In the address bar you will see a url like this:<br />
													<code>http://www.goodreads.com/user/show/<font color="#0000ff">Your User Id is Here</font></code>
													<br />copy and paste the user id into this field.
												</td>
											</tr>
											<tr valign="top">
												<th scope="row">
													<label for="bookshelf_private">Private Account?</label>
												</th>
												<td>
													<?php if ($cUserPrivate == 'true') {
																	$sUserPrivate = 'true';
																} else {
																	$sUserPrivate = get_option('bookshelf_private');
																}?>
													<p>
														<label>
															<input type="radio" <?php if ($sUserPrivate == 'false') { echo 'checked="checked" '; } if ($cUserPrivate == 'true') { echo 'disabled="disabled" '; } ?> class="tog" value="false" name="bookshelf_private" /> No
														</label>
													</p>
													<p>
														<label>
															<input type="radio" <?php if ($sUserPrivate == 'true') { echo 'checked="checked" '; } ?> class="tog" value="true" name="bookshelf_private" /> Yes
														</label>
													</p>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row">
													<label for="bookshelf_secret_key">Secret Key</label>
												</th>
												<td>
													<input type="text" size="30" value="<?php echo get_option('bookshelf_secret_key'); ?>" id="bookshelf_secret_key" name="bookshelf_secret_key" />
													<br />If your <a href="http://goodreads.com" target="_blank">Goodreads</a> profile is private enter your secret key.<br />
													Go to your account and click "My Books" then click "rss".  In the address bar you will see a url like this:<br /> <code>http://www.goodreads.com/review/list_rss/<?php if ($sUserId) { echo $sUserId; } else { echo 'User ID'; } ?>?key=<font color="#0000ff">Your Secret Key</font>&shelf=...</code><br />
													Copy and paste the secret key into this field.  Make sure you get the whole thing, it is long.  You want all of the letters and numbers that come after <code>key=</code> and before <code>&shelf</code>.
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>

							<div id="book_opt" class="postbox">
								<h3 class="hndle"><span>Options</span></h3>
								<div class="inside">
									<table class="form-table">
										<tbody>
											<tr valign="top">
												<th scope="row">Shelf</th>
												<td>
													<?php $sShelf = get_option('bookshelf_shelf'); ?>
													<p>
														<label>
															<input type="radio" <?php if ($sShelf == 'read') { echo 'checked="checked" ';} ?> class="tog" value="read" id="bookshelf_shelf3" name="bookshelf_shelf" onclick="clearBox()" /> Read
														</label>
													</p>
													<p>
														<label>
															<input type="radio" <?php if ($sShelf == 'currently-reading') { echo 'checked="checked" '; } ?> class="tog" value="currently-reading" id="bookshelf_shelf2" name="bookshelf_shelf" onclick="clearBox()" /> Currently Reading
														</label>
													</p>
													<p>
														<label>
															<input type="radio" <?php if ($sShelf == 'to-read') { echo 'checked="checked" '; } ?> class="tog" value="to-read" id="bookshelf_shelf1" name="bookshelf_shelf" onclick="clearBox()" /> To Read
														</label>
													</p>
													<p>
														<label><input type="text" size="25" value="<?php if ($sCustomShelf) { echo get_option('bookshelf_custom'); } ?>" id="bookshelf_custom" name="bookshelf_custom" onclick="clearRadio()" />
														</label>
													</p>
														<script type = "text/javascript">
														function clearBox() {
														document.getElementById("bookshelf_custom").value = '';
														}
														function clearRadio() {
														document.getElementById("bookshelf_shelf1").checked = false;
														document.getElementById("bookshelf_shelf2").checked = false;
														document.getElementById("bookshelf_shelf3").checked = false;
														}
														</script>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row">
													<label for="bookshelf_book_limit">Book Limit</label>
												</th>
												<td>
													Show <input type="text" size="3" value="<?php echo get_option('bookshelf_book_limit'); ?>" id="bookshelf_book_limit" name="bookshelf_book_limit" /> books at most. (no more than 50)
												</td>
											</tr>
											<tr valign="top">
												<th scope="row"></th>
												<td>
													<p>
														NOTE: At this time not all sort options work for custom shelves.
													</p>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row">
													<label for="bookshelf_sort">Sorted By</label>
												</th>
												<td>
													<select id="bookshelf_sort" name="bookshelf_sort">
														<?php $sSort = get_option('bookshelf_sort'); ?>
														<option <?php if ($sSort == 'position') { echo 'selected="selected" '; } ?> value="position">position</option>
														<option <?php if ($sSort == 'votes') { echo 'selected="selected" '; } ?> value="votes">votes</option>
														<option <?php if ($sSort == 'rating') { echo 'selected="selected" '; } ?> value="rating">rating</option>
														<option <?php if ($sSort == 'shelves') { echo 'selected="selected" '; } ?> value="shelves">shelves</option>
														<option <?php if ($sSort == 'avg_rating') { echo 'selected="selected" '; } ?> value="avg_rating">avg rating</option>
														<option <?php if ($sSort == 'isbn') { echo 'selected="selected" '; } ?> value="isbn">isbn</option>
														<option <?php if ($sSort == 'comments') { echo 'selected="selected" '; } ?> value="comments">comments</option>
														<option <?php if ($sSort == 'author') { echo 'selected="selected" '; } ?> value="author">author</option>
														<option <?php if ($sSort == 'title') { echo 'selected="selected" '; } ?> value="title">title</option>
														<option <?php if ($sSort == 'notes') { echo 'selected="selected" '; } ?> value="notes">notes</option>
														<option <?php if ($sSort == 'cover') { echo 'selected="selected" '; } ?> value="cover">cover</option>
														<option <?php if ($sSort == 'review') { echo 'selected="selected" '; } ?> value="review">review</option>
														<option <?php if ($sSort == 'random') { echo 'selected="selected" '; } ?> value="random">random</option>
														<option <?php if ($sSort == 'date_read') { echo 'selected="selected" '; } ?> value="date_read">date read</option>
														<option <?php if ($sSort == 'year_pub') { echo 'selected="selected" '; } ?> value="year_pub">year pub</option>
														<option <?php if ($sSort == 'date_added') { echo 'selected="selected" '; } ?> value="date_added">date added</option>
														<option <?php if ($sSort == 'num_ratings') { echo 'selected="selected" '; } ?> value="num_ratings">num ratings</option>
														<option <?php if ($sSort == 'date_updated') { echo 'selected="selected" '; } ?> value="date_updated">date updated</option>
													</select>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row">
													<label for="bookshelf_order">Sort Order</label>
												</th>
												<td>
													<p>
														<label>
															<input type="radio" <?php if ($sOrder == 'd') { echo 'checked="checked" '; } ?> class="tog" value="d" name="bookshelf_order" /> Descending
														</label>
													</p>
													<p>
														<label>
															<input type="radio" <?php if ($sOrder == 'a') { echo 'checked="checked" '; } ?> class="tog" value="a" name="bookshelf_order" /> Ascending
														</label>
													</p>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row">
													<label for="bookshelf_update_frequency">Update Frequency</label>
												</th>
												<td>

													<select id="bookshelf_update_frequency" name="bookshelf_update_frequency">
														<?php $iUpdateFrequency = get_option('bookshelf_update_frequency'); ?>
														<option <?php if ($iUpdateFrequency == 900) { echo 'selected="selected" '; } ?> value="900">Every 15 minutes</option>
														<option <?php if ($iUpdateFrequency == 1800) { echo 'selected="selected" '; } ?> value="1800">Every 30 minutes</option>
														<option <?php if ($iUpdateFrequency == 3600) { echo 'selected="selected" '; } ?> value="3600">Every hour</option>
														<option <?php if ($iUpdateFrequency == 86400) { echo 'selected="selected" '; } ?> value="86400">Every day</option>
													</select>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>

							<div id="book_disp" class="postbox">
								<h3 class="hndle"><span>Display</span></h3>
								<div class="inside">
									<table class="form-table">
										<tbody>
											<tr valign="top">
												<th scope="row">
													<label for="bookshelf_display_format">Display Format</label>
												</th>
												<td>
													<p>
														<label for="bookshelf_display_format">The Bookshelf tags below can be used among standard <abbr title="HyperText Markup Language">HTML</abbr> to customize the book display format.  Tags can be used more than once, or completely left out, depending on your preferences.  The block of code you design below will be used for each book, so put other non-book-related code around your Bookshelf call:
															<pre>&lt;?php if(function_exists('bookshelf')) { bookshelf(); } ?&gt;</pre>
															<ul style="margin: 0px; padding: 0px; list-style: none;">
																<li><code>[title]</code> Book Title.</li>
																<li><code>[author]</code> Author's Name.</li>
																<li><code>[cover]</code> Book cover image source.</li>
																<li><code>[isbn]</code> Books ISBN.</li>
																<li><code>[read]</code> Date you finished reading the book. (yyyy/mm/dd)</li>
																<li><code>[review]</code> Your review of the book. (NOTE: Can be long!)</li>
																<li><code>[short_review]</code> Review shortened to 200 char.</li>
																<li><code>[published]</code> Year the book was published.</li>
																<li><code>[description]</code> Book's description. (NOTE: Can be long!)</li>
																<li><code>[short_description]</code> Description shortened to 200 char.</li>
																<li><code>[user_rating]</code> Your rating displayed as a number.</li>
																<li><code>[avg_rating]</code> Books average rating displayed as a number.</li>
																<li><code>[url]</code> Goodreads book link address.</li>
															</ul>
														</label>
													</p>
													<p>
														<textarea class="code" style="width: 98%; font-size: 12px;" id="bookshelf_display_format" rows="8" cols="60" name="bookshelf_display_format"><?php echo get_option('bookshelf_display_format'); ?></textarea>
													</p>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>

							<div id="book_user" class="postbox">
								<div class="inside">
									<p class="submit">
										<input type="hidden" name="action" value="update" />
										<input type="hidden" name="page_options" value="bookshelf_userid,bookshelf_private,bookshelf_secret_key,bookshelf_shelf,bookshelf_custom,bookshelf_update_frequency,bookshelf_book_limit,bookshelf_order,bookshelf_sort,bookshelf_display_format" />
										<input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
									</p>
								</div>
							</div>
						</div>
					</div>
				</div>
				</div>
			</form>
		</div>

		<?php
	}

	function GetPluginPath() {
		$path = dirname(__FILE__);
		return trailingslashit(str_replace("\\","/",$path));
	}

	function GetPluginUrl() {

		//Try to use WP API if possible, introduced in WP 2.6
		if (function_exists('plugins_url')) return trailingslashit(plugins_url(basename(dirname(__FILE__))));

		//Try to find manually... can't work if wp-content was renamed or is redirected
		$path = dirname(__FILE__);
		$path = str_replace("\\","/",$path);
		$path = trailingslashit(get_bloginfo('wpurl')) . trailingslashit(substr($path,strpos($path,"wp-content/")));
		return $path;
	}

?>