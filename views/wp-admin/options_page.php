<div class="wrap" id="book_div">
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
                                                <a href="http://goodreads.com" title="Visit Goodreads"><img src="<?php //echo GetPluginUrl(); ?>goodreads_bookmark.png" alt="Goodreads" /></a>
                                        </div>
                                </div>
                                <div id="book_grlogo" class="postbox">
                                        <h3 class="hndle"><span>Test Display</span></h3>
                                        <div class="inside">
                                                <p>This is a test of what your Bookshelf will display. This does not apply the display format you specified.</p>
                                                <ul>
                                                        <?php //bookshelf_test($bUserId,$iBookLimit,$iBookLimitWithBuffer,$sBooksXml); ?>
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
