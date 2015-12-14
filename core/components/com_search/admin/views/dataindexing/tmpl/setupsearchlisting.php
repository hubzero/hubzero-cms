<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('Search: Setup Listing'));
Toolbar::custom('saveHubType', 'save', '', 'Add new HubType', false);
Toolbar::cancel();

$this->css('solr');
$option = $this->option;

\Submenu::addEntry(
	Lang::txt('Overview'),
	'index.php?option='.$option.'&task=configure'
);
\Submenu::addEntry(
	Lang::txt('Search Index'),
	'index.php?option='.$option.'&task=searchindex'
);
\Submenu::addEntry(
	Lang::txt('Index Blacklist'),
	'index.php?option='.$option.'&task=manageBlacklist'
);
?>
<style>
/* Desktop View */
.search-container {
	background: #FFFFFF;
	height: 100%;
	width: 100%;
}

.search-container .inner {
	height: 100%;
	width: 100%;
	display:table;
}

.search-settings {
	background-color: #E8E8E8;
	height: 100%;
	width: 15%;
	height: 100%;
	padding: 2%;
	display:table-cell;
}

.search-settings select {
	width: 90%;
	padding: 5px;
	line-height: 1 !important;
}

.search-settings fieldset {
	width: 100%;
	height: 100%;
}

.search-area {
	height: 100%;
	width: 85%;
	padding: 2%;
	display:table-cell;
}

#querybox {
	width: 80%;
}

.querybar #search {
	height:100%;
	padding: 10px;
	background-color: #9C9CCC;
	color: white;
  font-family: 'Segoe UI',Tahoma,Arial,Helvetica,sans-serif;
  cursor: pointer;
  color: #fff;
  border: 1px solid #D9D9D9;
}
.querybar #search-button-text:before {
	font-family: "Fontcons";
 	content: "\f002"; /* unicode characters must start with a backslash */  
	height: 100%;
	width: 100%;
	padding-right: 3px;
}

.result {
	display:table;
	width: 90%;
	height: 100%;
	border-bottom: 1px solid #D9D9D9;
}

.spacer {
	height: 10px;
	padding: 10px;
	width: 100%;
}

.type-icon {
	display: table-cell;
	line-height: 100%;
}

.type-icon span {
	display: inline-block;
	vertical-align: middle;
	line-height: normal
}
		
.result-data {
	display: table-cell;
	width: 80%;
	height: 100%;
}

.result-data .title a {
	font-size: 16pt;
	color: #24A0E0;
}

.result-data .description p {
	font-size: 9pt; 
	color: gray;
	margin: 0 0 5px 0;
}

.result-data .author {
}

.result-data .date{
}

.result-data .tags {
	margin-top: 5px;
	padding: 5px;
}
</style> 

<script>
$('document').ready(function(){

	var DEBUG = 0;

	var url = "https://" + window.location.host + "<?php echo htmlspecialchars_decode(Route::url('index.php?option='.$this->option.'&controller='.$this->controller.'&task=sampleRow')); ?>";

	if (DEBUG)
	{
		console.log(url);
	}

	$('.location').change(function(){
			var value = $(this).val();
			var loc = $(this).attr('name');
			var length = 260;

			if (DEBUG)
			{
				console.log(loc + " is set to: " + value);
			}

			$.post(url, {type:"<?php echo $this->type; ?>", rowID:'3'})
				.done(function(data) {
					var row = JSON.parse(data);
					if (row[value].length > length)
					{
						row[value] = row[value].substring(0,length) + '...';
					}
						$('#'+loc).text(row[value]);
				});
		});
	});
</script>

<div class="grid">
<div class="col span6">
<form name="adminForm" class="editform" id="item-form" action="<?php echo Route::url('index.php'); ?> method="post">
<fieldset class="adminform">
<div class="grid">
<legend>HubType Dynamic Fields</legend>
	<div class="col span4">
		<?php foreach ($this->placeholders as $placeholder): ?>
		<div class="input_wrap grid">
				<div class="col span6">
				<label for="<?php echo $placeholder; ?>"><?php echo $placeholder; ?>: </label>
				</div>
				<div class="col span6">
				<select class="location" name="<?php echo $placeholder; ?>">
				<option value=""> - </option>
				<?php foreach ($this->dynamicFields as $struct): ?>
					<option value="<?php echo $struct; ?>"><?php echo $struct; ?></option>
				<?php endforeach; ?>
				</select>
			</div>
		</div><!-- /.input_wrap -->
		<div class="spacer"></div>
		<?php endforeach; ?>
	</div><!-- /.col .span4 -->
</fieldset>
<input type="hidden" name="task" value="saveHubType" />
<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
</form>
</div><!-- /.col .span6 -->
<div class="col span5">
<div class="spacer"></div>
  <!-- result -->
  <div class="result">
    <div class="type-icon">
      <span>Publication</span>
    </div>
    <div class="result-data">
      <span class="title"><a href="#">The Importance of Being Earnest</a></span>
      <span class="description">
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                </span>
                <span class="author">Created by: <a href="#">Oscar Wilde</a></span>
                <span class="date">Created: <a href="#"><?php echo Date::of(); ?></a></span>
                <span class="tags">
                  <ul>
                    <li class="tag">Reading</li>
                    <li class="tag">Writing</li>
                    <li class="tag">Arithmetic</li>
                  </ul>
                </span>
              </div><!-- end result data -->
            </div><!-- end result -->
		</div> <!-- /.result-data -->
	</div>
<?php //endforeach; ?>
</div><!-- /.col .span6 -->
</div> <!-- /.grid -->


