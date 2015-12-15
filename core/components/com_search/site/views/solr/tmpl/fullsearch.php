<!-- Code snippet container -->
<form action="<?php echo Route::url('index.php'); ?>" method="post">
<section class="search-container">
	<div class="inner">
			<div class="search-settings">
				<fieldset>
					<legend>Filters:</legend>
					<select name="search[type]">
						<option>Filter by Type</option>
						<option>Citations</option>
						<option>Publications</option>
					</select>
				</fieldset>
			</div>
			<div class="search-area">
				<div class="workarea">
					<div class="workarea-row">
						<div class="querybar">
							<input type="text" id="querybox" name="search[query]" placeholder="What are you looking for?" <?php echo ($this->queryString != '' ? 'value="'.$this->queryString.'"' : ''); ?> />
							<button name="search-submit" id="search" type="submit">
								<span id="search-button-icon"></span>
								<span id="search-button-text">Search</span>
							</button>
						</div>
				</div>
				<div class="workarea-row">
					<div class="results">
						<?php if (count($this->results) > 0 && $this->results != ''): ?>
							<?php foreach ($this->results as $result): ?>
						<!-- result -->
						<div class="result">
							<div class="type-icon">
							<span><?php echo $result->hubtype; ?></span>
							</div>
							<div class="result-data">
							<?php
								$x = 0;
								$title = '';
										foreach ($result->title as $t)
										{
											$title .= $t;
											if (count($result->title) - 1 < $x)
											{
												$title .= ' / ';
											}
											$x++;
										}
							?>
							<span class="title"><a href="#"><?php echo stripslashes($title); ?></a></span>
								<span class="description">
									<?php if ($result->description != ''): ?>
										<p><?php echo (strlen($result->description) > 220) ? strip_tags(substr($result->description, 0, 220)) . '...' : strip_tags($result->description); ?></p>
									<?php endif; ?>
									<?php if ($result->fulltext != ''): ?>
										<p><?php echo (strlen($result->fulltext) > 220) ? strip_tags(substr($result->fulltext, 0, 220)) . '...' : strip_tags($result->fulltext); ?></p>
									<?php endif; ?>
								</span>
								<span class="author">Created by: 
									<?php
										if (isset($result->created_by))
										{
											$user = User::getInstance($result->created_by)->get('username');
										}
										elseif (isset($result->uid))
										{
											$user = User::getInstance($result->uid)->get('username');
										}
										elseif (isset($result->author))
										{
											$user = $result->author;
										}
										else
										{
											$user = Lang::txt('COM_SEARCH_UNKNOWN');
										}
										echo $user;
									?>
								</span>
								<span class="date">Created: <a href="#"><?php echo $result->created; ?></a></span>
								<span class="tags">
									<?php $tags = explode(',' , $result->tags); ?>
									<?php if (count($tags) > 0): ?>
									<ul>
									<?php foreach (explode(',' ,$result->tags) as $tag): ?>
									<?php if ($tag != ''): ?>
										<li class="tag"><?php echo $tag; ?></li>
									<?php endif; ?>
									<?php endforeach; ?>
									</ul>
									<?php endif; ?>
								</span>
							</div><!-- end result data -->
						</div><!-- end result -->
						<div class="spacer"></div>
						<?php endforeach; ?>
						<div class="statistics">
						<span class="stat" id="numberorResults">Found:<?php echo $this->results->getNumFound(); ?></span>
						</div><!-- end search-area -->
						<?php elseif ($this->queryString != ''): ?>
							<div class="no-result">
								<div class="inner">
								<?php echo Lang::txt('COM_SEARCH_NO_RESULTS'); ?>
								</div> <!-- end inner -->
							</div> <!-- end no-result -->
						<?php else: ?>
							<div class="no-result">
								<div class="inner">
									<h2 class="intro-title"><?php echo  Lang::txt('What are you looking for?'); ?></h2>
									<div class="intro-suggestion">
										<p>You are able to search fields by entering the fieldname and the term you are searching for.</br>
										For example: <code>title:hubzero</code>
										</p>
									</div><!-- end intro suggestion -->
									<div class="intro-suggestion">
										<p>Narrow your search by using logical operators.</br> 
										For example: <code>(blog AND author:Bob) OR burgers</code>
										</p>
									</div><!-- end intro suggestion -->
									<div class="intro-suggestion">
										<p>Perform wildcard searches by using an asterisk (*).</br>
										For example: <code>nano*</code>
										</p>
									</div><!-- end intro suggestion -->
								</div> <!-- end inner -->
							</div> <!-- end no-result -->
						<?php endif; ?>
					</div><!-- end results -->
				</div><!-- end workarea row -->
			</div><!-- end workarea -->
	</div><!-- end inner -->
</section>
<input type="hidden" name="task" value="search" />
<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
</form>
<!-- End code snippet container -->
