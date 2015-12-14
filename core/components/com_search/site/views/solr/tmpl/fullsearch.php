<?php if (0): ?>
<!-- Guide page heading -->
<header class="guide-header">
	<h1><?php echo Lang::txt('COM_SEARCH'); ?></h1>
</header>
<!-- End guide page heading -->
<?php endif; ?>

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

/* Tablet class devices */
@media only screen and (max-width: 800px) {
    .search-settings {
			width: 100%;
			display: table-row;
			height: 100%;
    }
		.search-settings legend{
			float: left;
			width: 25%;
			text-align: right;
			padding-right: 10px;
		}
		.search-settings select{
			float: left;
			width: 75%;
		}
		.search-settings fieldset {
			padding: 10px;
		}
		.search-area {
			padding: 10px;
			width: 100%;
			display: table-row;
		}
		.workarea {
				margin-top: 10px;
				margin-right: auto;
				margin-left: auto;
				width: 90%;
		}
	.query #search {
		height: 100%;
		width: 50px;
	}
	.querybar #search-button-text:before {
		font-family: "Fontcons";
    content: "\f002"; /* unicode characters must start with a backslash */  
		height: 100%;
		width: 100%;
		padding-right: 3px;
	}
} 
/* Phablet */
@media only screen and (max-width: 500px) {
	#querybox {
		width: 75%;
	}

}

/* Phones */
@media only screen and (max-width: 400px) {
	.querybar #search-button-text {
		display: none;
	}
	.querybar #search-button-icon:before{
		font-family: "Fontcons";
		content: "\f002";
		font-size: 16pt;
	}
	.type-icon {
		display: table-row;
		background-color: #A8BDCC;
		padding: 40px;
		height: 40px;
		line-height: 40px;
		margin-bottom: 10px;
	}
	.type-icon span {
		vertical-align: middle;
		margin-left: 14px;
		color: white;
		font-size: 16pt;
	}
	.type-icon:after{
		height: 10px;
		width: 100%;
		margin-bottom: 10px;
	}
}
</style>

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
								<p><?php echo (strlen($result->description) > 220) ? strip_tags(substr($result->description, 0, 220)) . '...' : strip_tags($result->description); ?></p>
								<p><?php echo (strlen($result->fulltext) > 220) ? strip_tags(substr($result->fulltext, 0, 220)) . '...' : strip_tags($result->fulltext); ?></p>
								</span>
								<!-- <span class="author">Created by: <a href="#"><?php //echo User::getInstance($result->created_by)->get('username', Lang::txt('COM_SEARCH_UNKNOWN')); ?></a></span>
								<span class="author">Created by: <a href="#"><?php //echo User::getInstance($result->uid)->get('username', Lang::txt('COM_SEARCH_UNKNOWN')); ?></a></span>
								-->
								<span class="author">Created by: <a href="#"><?php echo User::getInstance(1003)->get('username', Lang::txt('COM_SEARCH_UNKNOWN')); ?></a></span>
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
						<span class="stat" id="numberorResults">Found: <?php echo $this->results->getNumFound(); ?></span>
						</div><!-- end search-area -->
						<?php else: ?>
							<div class="no-result">
								<div class="inner">
								<?php echo Lang::txt('COM_SEARCH_NO_RESULTS'); ?>
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
