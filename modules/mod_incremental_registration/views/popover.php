<?php 
ximport('Hubzero_Environment');
ximport('Hubzero_Geo');

$defaultCountries = array(
					array('code' => 'af', 'name' => 'AFGHANISTAN'),
					array('code' => 'ax', 'name' => 'ALAND ISLANDS'),
					array('code' => 'al', 'name' => 'ALBANIA'),
					array('code' => 'dz', 'name' => 'ALGERIA'),
					array('code' => 'as', 'name' => 'AMERICAN SAMOA'),
					array('code' => 'ad', 'name' => 'ANDORRA'),
					array('code' => 'ao', 'name' => 'ANGOLA'),
					array('code' => 'ai', 'name' => 'ANGUILLA'),
					array('code' => 'aq', 'name' => 'ANTARCTICA'),
					array('code' => 'ag', 'name' => 'ANTIGUA AND BARBUDA'),
					array('code' => 'ar', 'name' => 'ARGENTINA'),
					array('code' => 'am', 'name' => 'ARMENIA'),
					array('code' => 'aw', 'name' => 'ARUBA'),
					array('code' => 'au', 'name' => 'AUSTRALIA'),
					array('code' => 'at', 'name' => 'AUSTRIA'),
					array('code' => 'az', 'name' => 'AZERBAIJAN'),
					array('code' => 'bs', 'name' => 'BAHAMAS'),
					array('code' => 'bh', 'name' => 'BAHRAIN'),
					array('code' => 'bd', 'name' => 'BANGLADESH'),
					array('code' => 'bb', 'name' => 'BARBADOS'),
					array('code' => 'by', 'name' => 'BELARUS'),
					array('code' => 'be', 'name' => 'BELGIUM'),
					array('code' => 'bz', 'name' => 'BELIZE'),
					array('code' => 'bj', 'name' => 'BENIN'),
					array('code' => 'bm', 'name' => 'BERMUDA'),
					array('code' => 'bt', 'name' => 'BHUTAN'),
					array('code' => 'bo', 'name' => 'BOLIVIA, PLURINATIONAL STATE OF'),
					array('code' => 'ba', 'name' => 'BOSNIA AND HERZEGOVINA'),
					array('code' => 'bw', 'name' => 'BOTSWANA'),
					array('code' => 'bv', 'name' => 'BOUVET ISLAND'),
					array('code' => 'br', 'name' => 'BRAZIL'),
					array('code' => 'io', 'name' => 'BRITISH INDIAN OCEAN TERRITORY'),
					array('code' => 'bn', 'name' => 'BRUNEI DARUSSALAM'),
					array('code' => 'bg', 'name' => 'BULGARIA'),
					array('code' => 'bf', 'name' => 'BURKINA FASO'),
					array('code' => 'bi', 'name' => 'BURUNDI'),
					array('code' => 'kh', 'name' => 'CAMBODIA'),
					array('code' => 'cm', 'name' => 'CAMEROON'),
					array('code' => 'ca', 'name' => 'CANADA'),
					array('code' => 'cv', 'name' => 'CAPE VERDE'),
					array('code' => 'ky', 'name' => 'CAYMAN ISLANDS'),
					array('code' => 'cf', 'name' => 'CENTRAL AFRICAN REPUBLIC'),
					array('code' => 'td', 'name' => 'CHAD'),
					array('code' => 'cl', 'name' => 'CHILE'),
					array('code' => 'cn', 'name' => 'CHINA'),
					array('code' => 'cx', 'name' => 'CHRISTMAS ISLAND'),
					array('code' => 'cc', 'name' => 'COCOS (KEELING) ISLANDS'),
					array('code' => 'co', 'name' => 'COLOMBIA'),
					array('code' => 'km', 'name' => 'COMOROS'),
					array('code' => 'cg', 'name' => 'CONGO'),
					array('code' => 'cd', 'name' => 'CONGO, THE DEMOCRATIC REPUBLIC OF THE'),
					array('code' => 'ck', 'name' => 'COOK ISLANDS'),
					array('code' => 'cr', 'name' => 'COSTA RICA'),
					array('code' => 'ci', 'name' => 'COTE DIVOIRE'),
					array('code' => 'hr', 'name' => 'CROATIA'),
					array('code' => 'cu', 'name' => 'CUBA'),
					array('code' => 'cy', 'name' => 'CYPRUS'),
					array('code' => 'cz', 'name' => 'CZECH REPUBLIC'),
					array('code' => 'dk', 'name' => 'DENMARK'),
					array('code' => 'dj', 'name' => 'DJIBOUTI'),
					array('code' => 'dm', 'name' => 'DOMINICA'),
					array('code' => 'do', 'name' => 'DOMINICAN REPUBLIC'),
					array('code' => 'ec', 'name' => 'ECUADOR'),
					array('code' => 'eg', 'name' => 'EGYPT'),
					array('code' => 'sv', 'name' => 'EL SALVADOR'),
					array('code' => 'gq', 'name' => 'EQUATORIAL GUINEA'),
					array('code' => 'er', 'name' => 'ERITREA'),
					array('code' => 'ee', 'name' => 'ESTONIA'),
					array('code' => 'et', 'name' => 'ETHIOPIA'),
					array('code' => 'fk', 'name' => 'FALKLAND ISLANDS (MALVINAS)'),
					array('code' => 'fo', 'name' => 'FAROE ISLANDS'),
					array('code' => 'fj', 'name' => 'FIJI'),
					array('code' => 'fi', 'name' => 'FINLAND'),
					array('code' => 'fr', 'name' => 'FRANCE'),
					array('code' => 'gf', 'name' => 'FRENCH GUIANA'),
					array('code' => 'pf', 'name' => 'FRENCH POLYNESIA'),
					array('code' => 'tf', 'name' => 'FRENCH SOUTHERN TERRITORIES'),
					array('code' => 'ga', 'name' => 'GABON'),
					array('code' => 'gm', 'name' => 'GAMBIA'),
					array('code' => 'ge', 'name' => 'GEORGIA'),
					array('code' => 'de', 'name' => 'GERMANY'),
					array('code' => 'gh', 'name' => 'GHANA'),
					array('code' => 'gi', 'name' => 'GIBRALTAR'),
					array('code' => 'gr', 'name' => 'GREECE'),
					array('code' => 'gl', 'name' => 'GREENLAND'),
					array('code' => 'gd', 'name' => 'GRENADA'),
					array('code' => 'gp', 'name' => 'GUADELOUPE'),
					array('code' => 'gu', 'name' => 'GUAM'),
					array('code' => 'gt', 'name' => 'GUATEMALA'),
					array('code' => 'gg', 'name' => 'GUERNSEY'),
					array('code' => 'gn', 'name' => 'GUINEA'),
					array('code' => 'gw', 'name' => 'GUINEA-BISSAU'),
					array('code' => 'gy', 'name' => 'GUYANA'),
					array('code' => 'ht', 'name' => 'HAITI'),
					array('code' => 'hm', 'name' => 'HEARD ISLAND AND MCDONALD ISLANDS'),
					array('code' => 'va', 'name' => 'HOLY SEE (VATICAN CITY STATE)'),
					array('code' => 'hn', 'name' => 'HONDURAS'),
					array('code' => 'hk', 'name' => 'HONG KONG'),
					array('code' => 'hu', 'name' => 'HUNGARY'),
					array('code' => 'is', 'name' => 'ICELAND'),
					array('code' => 'in', 'name' => 'INDIA'),
					array('code' => 'id', 'name' => 'INDONESIA'),
					array('code' => 'ir', 'name' => 'IRAN, ISLAMIC REPUBLIC OF'),
					array('code' => 'iq', 'name' => 'IRAQ'),
					array('code' => 'ie', 'name' => 'IRELAND'),
					array('code' => 'im', 'name' => 'ISLE OF MAN'),
					array('code' => 'il', 'name' => 'ISRAEL'),
					array('code' => 'it', 'name' => 'ITALY'),
					array('code' => 'jm', 'name' => 'JAMAICA'),
					array('code' => 'jp', 'name' => 'JAPAN'),
					array('code' => 'je', 'name' => 'JERSEY'),
					array('code' => 'jo', 'name' => 'JORDAN'),
					array('code' => 'kz', 'name' => 'KAZAKHSTAN'),
					array('code' => 'ke', 'name' => 'KENYA'),
					array('code' => 'ki', 'name' => 'KIRIBATI'),
					array('code' => 'kp', 'name' => 'KOREA, DEMOCRATIC PEOPLES REPUBLIC OF'),
					array('code' => 'kr', 'name' => 'KOREA, REPUBLIC OF'),
					array('code' => 'kw', 'name' => 'KUWAIT'),
					array('code' => 'kg', 'name' => 'KYRGYZSTAN'),
					array('code' => 'la', 'name' => 'LAO PEOPLES DEMOCRATIC REPUBLIC'),
					array('code' => 'lv', 'name' => 'LATVIA'),
					array('code' => 'lb', 'name' => 'LEBANON'),
					array('code' => 'ls', 'name' => 'LESOTHO'),
					array('code' => 'lr', 'name' => 'LIBERIA'),
					array('code' => 'ly', 'name' => 'LIBYAN ARAB JAMAHIRIYA'),
					array('code' => 'li', 'name' => 'LIECHTENSTEIN'),
					array('code' => 'lt', 'name' => 'LITHUANIA'),
					array('code' => 'lu', 'name' => 'LUXEMBOURG'),
					array('code' => 'mo', 'name' => 'MACAO'),
					array('code' => 'mk', 'name' => 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF'),
					array('code' => 'mg', 'name' => 'MADAGASCAR'),
					array('code' => 'mw', 'name' => 'MALAWI'),
					array('code' => 'my', 'name' => 'MALAYSIA'),
					array('code' => 'mv', 'name' => 'MALDIVES'),
					array('code' => 'ml', 'name' => 'MALI'),
					array('code' => 'mt', 'name' => 'MALTA'),
					array('code' => 'mh', 'name' => 'MARSHALL ISLANDS'),
					array('code' => 'mq', 'name' => 'MARTINIQUE'),
					array('code' => 'mr', 'name' => 'MAURITANIA'),
					array('code' => 'mu', 'name' => 'MAURITIUS'),
					array('code' => 'yt', 'name' => 'MAYOTTE'),
					array('code' => 'mx', 'name' => 'MEXICO'),
					array('code' => 'fm', 'name' => 'MICRONESIA, FEDERATED STATES OF'),
					array('code' => 'md', 'name' => 'MOLDOVA, REPUBLIC OF'),
					array('code' => 'mc', 'name' => 'MONACO'),
					array('code' => 'mn', 'name' => 'MONGOLIA'),
					array('code' => 'me', 'name' => 'MONTENEGRO'),
					array('code' => 'ms', 'name' => 'MONTSERRAT'),
					array('code' => 'ma', 'name' => 'MOROCCO'),
					array('code' => 'mz', 'name' => 'MOZAMBIQUE'),
					array('code' => 'mm', 'name' => 'MYANMAR'),
					array('code' => 'na', 'name' => 'NAMIBIA'),
					array('code' => 'nr', 'name' => 'NAURU'),
					array('code' => 'np', 'name' => 'NEPAL'),
					array('code' => 'nl', 'name' => 'NETHERLANDS'),
					array('code' => 'an', 'name' => 'NETHERLANDS ANTILLES'),
					array('code' => 'nc', 'name' => 'NEW CALEDONIA'),
					array('code' => 'nz', 'name' => 'NEW ZEALAND'),
					array('code' => 'ni', 'name' => 'NICARAGUA'),
					array('code' => 'ne', 'name' => 'NIGER'),
					array('code' => 'ng', 'name' => 'NIGERIA'),
					array('code' => 'nu', 'name' => 'NIUE'),
					array('code' => 'nf', 'name' => 'NORFOLK ISLAND'),
					array('code' => 'mp', 'name' => 'NORTHERN MARIANA ISLANDS'),
					array('code' => 'no', 'name' => 'NORWAY'),
					array('code' => 'om', 'name' => 'OMAN'),
					array('code' => 'pk', 'name' => 'PAKISTAN'),
					array('code' => 'pw', 'name' => 'PALAU'),
					array('code' => 'ps', 'name' => 'PALESTINIAN TERRITORY, OCCUPIED'),
					array('code' => 'pa', 'name' => 'PANAMA'),
					array('code' => 'pg', 'name' => 'PAPUA NEW GUINEA'),
					array('code' => 'py', 'name' => 'PARAGUAY'),
					array('code' => 'pe', 'name' => 'PERU'),
					array('code' => 'ph', 'name' => 'PHILIPPINES'),
					array('code' => 'pl', 'name' => 'POLAND'),
					array('code' => 'pt', 'name' => 'PORTUGAL'),
					array('code' => 'pr', 'name' => 'PUERTO RICO'),
					array('code' => 'qa', 'name' => 'QATAR'),
					array('code' => 're', 'name' => 'REUNION'),
					array('code' => 'ro', 'name' => 'ROMANIA'),
					array('code' => 'ru', 'name' => 'RUSSIAN FEDERATION'),
					array('code' => 'rw', 'name' => 'RWANDA'),
					array('code' => 'sh', 'name' => 'SAINT HELENA, ASCENSION AND TRISTAN DA CUNHA'),
					array('code' => 'kn', 'name' => 'SAINT KITTS AND NEVIS'),
					array('code' => 'lc', 'name' => 'SAINT LUCIA'),
					array('code' => 'mf', 'name' => 'SAINT MARTIN'),
					array('code' => 'pm', 'name' => 'SAINT PIERRE AND MIQUELON'),
					array('code' => 'vc', 'name' => 'SAINT VINCENT AND THE GRENADINES'),
					array('code' => 'ws', 'name' => 'SAMOA'),
					array('code' => 'sm', 'name' => 'SAN MARINO'),
					array('code' => 'st', 'name' => 'SAO TOME AND PRINCIPE'),
					array('code' => 'sa', 'name' => 'SAUDI ARABIA'),
					array('code' => 'sn', 'name' => 'SENEGAL'),
					array('code' => 'rs', 'name' => 'SERBIA'),
					array('code' => 'sc', 'name' => 'SEYCHELLES'),
					array('code' => 'sl', 'name' => 'SIERRA LEONE'),
					array('code' => 'sg', 'name' => 'SINGAPORE'),
					array('code' => 'sk', 'name' => 'SLOVAKIA'),
					array('code' => 'si', 'name' => 'SLOVENIA'),
					array('code' => 'sb', 'name' => 'SOLOMON ISLANDS'),
					array('code' => 'so', 'name' => 'SOMALIA'),
					array('code' => 'za', 'name' => 'SOUTH AFRICA'),
					array('code' => 'gs', 'name' => 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS'),
					array('code' => 'es', 'name' => 'SPAIN'),
					array('code' => 'lk', 'name' => 'SRI LANKA'),
					array('code' => 'sd', 'name' => 'SUDAN'),
					array('code' => 'sr', 'name' => 'SURINAME'),
					array('code' => 'sj', 'name' => 'SVALBARD AND JAN MAYEN'),
					array('code' => 'sz', 'name' => 'SWAZILAND'),
					array('code' => 'se', 'name' => 'SWEDEN'),
					array('code' => 'ch', 'name' => 'SWITZERLAND'),
					array('code' => 'sy', 'name' => 'SYRIAN ARAB REPUBLIC'),
					array('code' => 'tw', 'name' => 'TAIWAN'),
					array('code' => 'tj', 'name' => 'TAJIKISTAN'),
					array('code' => 'tz', 'name' => 'TANZANIA, UNITED REPUBLIC OF'),
					array('code' => 'th', 'name' => 'THAILAND'),
					array('code' => 'tl', 'name' => 'TIMOR-LESTE'),
					array('code' => 'tg', 'name' => 'TOGO'),
					array('code' => 'tk', 'name' => 'TOKELAU'),
					array('code' => 'to', 'name' => 'TONGA'),
					array('code' => 'tt', 'name' => 'TRINIDAD AND TOBAGO'),
					array('code' => 'tn', 'name' => 'TUNISIA'),
					array('code' => 'tr', 'name' => 'TURKEY'),
					array('code' => 'tm', 'name' => 'TURKMENISTAN'),
					array('code' => 'tc', 'name' => 'TURKS AND CAICOS ISLANDS'),
					array('code' => 'tv', 'name' => 'TUVALU'),
					array('code' => 'ug', 'name' => 'UGANDA'),
					array('code' => 'ua', 'name' => 'UKRAINE'),
					array('code' => 'ae', 'name' => 'UNITED ARAB EMIRATES'),
					array('code' => 'uk', 'name' => 'UNITED KINGDOM'),
					array('code' => 'us', 'name' => 'UNITED STATES'),
					array('code' => 'um', 'name' => 'UNITED STATES MINOR OUTLYING ISLANDS'),
					array('code' => 'uy', 'name' => 'URUGUAY'),
					array('code' => 'uz', 'name' => 'UZBEKISTAN'),
					array('code' => 'vu', 'name' => 'VANUATU'),
					array('code' => 've', 'name' => 'VENEZUELA, BOLIVARIAN REPUBLIC OF'),
					array('code' => 'vn', 'name' => 'VIET NAM'),
					array('code' => 'vg', 'name' => 'VIRGIN ISLANDS, BRITISH'),
					array('code' => 'vi', 'name' => 'VIRGIN ISLANDS, U.S.'),
					array('code' => 'wf', 'name' => 'WALLIS AND FUTUNA'),
					array('code' => 'eh', 'name' => 'WESTERN SAHARA'),
					array('code' => 'ye', 'name' => 'YEMEN'),
					array('code' => 'zm', 'name' => 'ZAMBIA'),
					array('code' => 'zw', 'name' => 'ZIMBABWE')
);
?>
<div id="overlay"></div>
<div id="questions">
	<h2>Help us keep this website and its services free</h2>
	<p>Please provide a little more information about yourself. <small>(<a href="/legal/privacy">Why do we need this information?</a>)</small></p>
	<p>We'll award you with <strong>15</strong> points for each question you answer. You can use these points towards items in the site <a href="/store">store</a>, or to place bounties on <a href="/answers">questions</a> and <a href="/wishlist">wishes</a>.</p>
	<form action="" method="post">
		<ol>
				<?php if (isset($row['orgtype'])): ?>
				<li>
					<label for="orgtype">Which item best describes your organizational affiliation? </label>
					<div class="indented">
					<?php if (isset($errors['orgtype'])): ?>
						<p class="warning">Please select your organizational affiliation</p>
					<?php endif; ?>
					<select id="orgtype" name="orgtype">
						<option selected="selected" value="">(select from list)</option>
						<option <?php if (isset($_POST['orgtype']) && $_POST['orgtype'] == 'universityundergraduate') echo 'selected="selected" '; ?>value="universityundergraduate">University / College Undergraduate</option>
						<option <?php if (isset($_POST['orgtype']) && $_POST['orgtype'] == 'universitygraduate') echo 'selected="selected" '; ?>value="universitygraduate">University / College Graduate Student</option>
						<option <?php if (isset($_POST['orgtype']) && $_POST['orgtype'] == 'universityfaculty') echo 'selected="selected" '; ?>value="universityfaculty">University / College Faculty</option>
						<option <?php if (isset($_POST['orgtype']) && $_POST['orgtype'] == 'universitystaff') echo 'selected="selected" '; ?>value="universitystaff">University / College Staff</option>
						<option <?php if (isset($_POST['orgtype']) && $_POST['orgtype'] == 'precollegestudent') echo 'selected="selected" '; ?>value="precollegestudent">K-12 (Pre-College) Student</option>
						<option <?php if (isset($_POST['orgtype']) && $_POST['orgtype'] == 'precollegefacultystaff') echo 'selected="selected" '; ?>value="precollegefacultystaff">K-12 (Pre-College) Faculty/Staff</option>
						<option <?php if (isset($_POST['orgtype']) && $_POST['orgtype'] == 'nationallab') echo 'selected="selected" '; ?>value="nationallab">National Laboratory</option>
						<option <?php if (isset($_POST['orgtype']) && $_POST['orgtype'] == 'industry') echo 'selected="selected" '; ?>value="industry">Industry / Private Company</option>
						<option <?php if (isset($_POST['orgtype']) && $_POST['orgtype'] == 'government') echo 'selected="selected" '; ?>value="government">Government Agency</option>
						<option <?php if (isset($_POST['orgtype']) && $_POST['orgtype'] == 'military') echo 'selected="selected" '; ?>value="military">Military</option>
						<option <?php if (isset($_POST['orgtype']) && $_POST['orgtype'] == 'unemployed') echo 'selected="selected" '; ?>value="unemployed">Retired / Unemployed</option>
					</select>
					</div>
				</li>
				<?php endif; ?>
				<?php if (isset($row['organization'])): ?>
				<li>
					<label for="org">Which organization are you affiliated with? </label><br />
					<div class="indented">
					<?php if (isset($errors['organization'])): ?>
						<p class="warning">Please select an organization or enter one in the provided "other" field</p>
					<?php endif; ?>
					<select id="org" name="org">
						<option value="">(select from list or enter other)</option>
						<?php 
						$dbh->setQuery('SELECT organization FROM #__xorganizations ORDER BY organization');
						foreach ($dbh->loadAssocList() as $org)
							echo '<option value="'.$org['organization'].'"'.(isset($_POST['org']) && $_POST['org'] === $org['organization'] ? ' selected="selected"' : '').'>'.$org['organization'].'</option>';
						?>
					</select>
					<br />
					<label for="org-other">Not listed? Enter your organization here: </label><br />
					<input id="org-other" type="text" name="org-other" value="<?php echo isset($_POST['org-other']) ? str_replace('"', '&quot;', $_POST['org-other']) : ''; ?>" />
					</div>
				</li>
				<?php endif; ?>
				<?php if (isset($row['reason'])): ?>
				<li>
					<label for="reason">What is the primary purpose of your account? </label>
					<div class="indented">
					<?php if (isset($errors['reason'])): ?>
						<p class="warning">Please select the reason for your account or enter one in the provided "other" field</p>
					<?php endif; ?>
					<select id="reason" name="reason">
						<?php $val = isset($_POST['reason']) ? $_POST['reason'] : ''; ?>
						<option value="">(select from list or enter other)</option>
						<option <?php if ($val === 'Required for class') echo 'selected="selected" '; ?>value="Required for class">Required for class</option>
						<option <?php if ($val === 'Developing a new course') echo 'selected="selected" '; ?>value="Developing a new course">Developing a new course</option>
						<option <?php if ($val === 'Using in an existing course') echo 'selected="selected" '; ?>value="Using in an existing course">Using in an existing course</option>
						<option <?php if ($val === 'Using simulation tools for research') echo 'selected="selected" '; ?>value="Using simulation tools for research">Using simulation tools for research</option>
						<option <?php if ($val === 'Using as background for my research') echo 'selected="selected" '; ?>value="Using as background for my research">Using as background for my research</option>
						<option <?php if ($val === 'Learning about subject matter') echo 'selected="selected" '; ?>value="Learning about subject matter">Learning about subject matter</option>
						<option <?php if ($val === 'Keeping current in subject matter') echo 'selected="selected" '; ?>value="Keeping current in subject matter">Keeping current in subject matter</option>
					</select>
					<br />
					<label for="reason-other">Have a different reason? </label><br />
					<input id="reason-other" type="text" name="reason-other" value="<?php echo isset($_POST['reason-other']) ? str_replace('"', '&quot;', $_POST['reason-other']) : ''; ?>" />
					</div>
				</li>
				<?php endif; ?>
				<?php if (isset($row['name'])): ?>
				<li>
					<label for="name">What is your name?</label>
					<?php if (isset($errors['name'])): ?>
						<p class="warning">Please enter your name</p>
					<?php endif; ?>
					<ol id="name-inp">
						<li>
							<label>
								<span>First:</span>
								<input type="text" value="<?php if (isset($_POST['name']['first'])) echo str_replace('"', '&quot;', $_POST['name']['first']); ?>" name="name[first]">
							</label>
						</li>
						<li>
							<label>
								<span>Middle:</span>
								<input type="text" value="<?php if (isset($_POST['name']['middle'])) echo str_replace('"', '&quot;', $_POST['name']['middle']); ?>" name="name[middle]">
							</label>
						</li>
						<li>
							<label>
								<span>Last:</span>
								<input type="text" value="<?php if (isset($_POST['name']['last'])) echo str_replace('"', '&quot;', $_POST['name']['last']); ?>" name="name[last]">
							</label>
					</li>
					</ol>
				</li>
				<?php endif; ?>
				<?php if (isset($row['gender'])): ?>
					<li>
						<fieldset>			
							<legend>What is your gender?</legend>
							<div class="indented">
								<?php if (isset($errors['gender'])): ?>
									<p class="warning">Please select your gender, or choose not to reveal it</p>
								<?php endif; ?>
								<label><input <?php if (isset($_POST['gender']) && $_POST['gender'] == 'male') echo 'checked="checked" '; ?>type="radio" class="option" value="male" name="gender"> Male</label>
								<label><input <?php if (isset($_POST['gender']) && $_POST['gender'] == 'female') echo 'checked="checked" '; ?>type="radio" class="option" value="female" name="gender"> Female</label>
								<label><input <?php if (isset($_POST['gender']) && $_POST['gender'] == 'refused') echo 'checked="checked" '; ?>type="radio" class="option" value="refused" name="gender"> Do not wish to reveal</label>
							</div>
						</fieldset>
					</li>
				<?php endif; ?>
				<?php if (isset($row['url'])): ?>
					<li>
						<label for="url">What is your web site address?</label>
						<div class="indented">
							<?php if (isset($errors['url'])): ?>
								<p class="warning">Please enter your web site URL</p>
							<?php endif; ?>
							<input type="text" id="url" name="url" value="<?php if (isset($_POST['url'])) echo str_replace('"', '&quot;', $_POST['url']); ?>" />
						</div>
					</li>
				<?php endif; ?>
				<?php if (isset($row['phone'])): ?>
					<li>
						<label for="phone">What is your phone number?</label>
						<div class="indented">
							<?php if (isset($errors['phone'])): ?>
								<p class="warning">Please enter your phone number</p>
							<?php endif; ?>
							<input type="text" id="phone" name="phone" value="<?php if (isset($_POST['phone'])) echo str_replace('"', '&quot;', $_POST['phone']); ?>" />
						</div>
					</li>
				<?php endif; ?>
				<?php if (isset($row['countryorigin'])): ?> 
					<li>
						<?php $country = isset($_POST['countryorigin']) ? $_POST['countryorigin'] : Hubzero_Geo::ipcountry(Hubzero_Environment::ipAddress()); ?>
						<fieldset>
							<legend>Are you a Legal Citizen or Permanent Resident of the <abbr title="United States">US</abbr>?</legend>
							<div class="indented">
								<?php if (isset($errors['countryorigin'])): ?>
									<p class="warning">Please select your country of origin</p>
								<?php endif; ?>
								<label><input type="radio" class="option" name="countryorigin_us" id="corigin_usyes" value="yes" <?php if (strtolower($country) == 'us' || (isset($_POST['countryorigin_us']) && $_POST['countryorigin_us'] == 'yes')) echo 'checked="checked"'; ?> />Yes</label> 
								<label><input type="radio" class="option" name="countryorigin_us" id="corigin_usno" value="no" <?php if (!empty($_POST['countryorigin']) && strtolower($country) != 'us' || (isset($_POST['countryorigin_us']) && $_POST['countryorigin_us'] == 'no')) echo 'checked="checked"'; ?> />No</label>
							</div>

							<div class="indented">
								<label for="countryorigin">If not, please select your country of origin</label>
								<select style="display: block" name="countryorigin" id="countryorigin">
									<option value="">Select country...</option>
								<?php
									$countries = Hubzero_Geo::getcountries();
									if (!$countries) {
										$countries = $defaultCountries;
									}
									foreach ($countries as $c) {
										echo '<option value="' . $c['code'] . '"';
										if ($country == $c['code']) {
											echo ' selected="selected"';
										}
										echo '>' . htmlentities($c['name'], ENT_COMPAT, 'UTF-8') . '</option>'."\n";
									}
								?>
								</select>
							</div>
						</fieldset>
					</li>
				<?php endif; ?>
				<?php if (isset($row['countryresident'])): ?> 
					<li>
						<?php $country = isset($_POST['countryresident']) ? $_POST['countryresident'] : Hubzero_Geo::ipcountry(Hubzero_Environment::ipAddress()); ?>
						<fieldset>
							<legend>Do you currently live in the <abbr title="United States">US</abbr>?</legend>
							<div class="indented">
								<?php if (isset($errors['countryresident'])): ?>
									<p class="warning">Please select your country of residency</p>
								<?php endif; ?>
								<label><input type="radio" class="option" name="countryresident_us" id="cores_usyes" value="yes" <?php if (strtolower($country) == 'us' || (isset($_POST['countryresident_us']) && $_POST['countryresident_us'] == 'yes')) echo 'checked="checked"'; ?> />Yes</label> 
								<label><input type="radio" class="option" name="countryresident_us" id="cores_usno" value="no" <?php if (!empty($_POST['countryresident']) && strtolower($country) != 'us' || (isset($_POST['countryresident_us']) && $_POST['countryresident_us'] == 'no')) echo 'checked="checked"'; ?> />No</label>
							</div>

							<div class="indented">
								<label for="countryresident">If not, please select the country where you currently reside</label>
								<select style="display: block" name="countryresident" id="countryresident">
									<option value="">Select country...</option>
								<?php
									$countries = Hubzero_Geo::getcountries();
									if (!$countries) {
										$countries = $defaultCountries;
									}
									foreach ($countries as $c) {
										echo '<option value="' . $c['code'] . '"';
										if ($country == $c['code']) {
											echo ' selected="selected"';
										}
										echo '>' . htmlentities($c['name'], ENT_COMPAT, 'UTF-8') . '</option>'."\n";
									}
								?>
								</select>
							</div>
						</fieldset>
					</li>
				<?php endif; ?>
				<?php if (isset($row['race'])): ?>
					<?php $race = isset($_POST['race']) ? $_POST['race'] : array(); ?>
					<li>
						<fieldset>
							<legend>If you are a U.S. Citizens or Permanent Residents (<a class="popup 675x678" href="/register/raceethnic">more information</a>), select your race(s) below</legend>
							<?php if (isset($errors['race'])): ?>
								<p class="warning">Please select your race(s)</p>
							<?php endif; ?>
							<div class="indented">
								<label><input type="checkbox" class="option" name="race[]" id="racenativeamerican" value="nativeamerican" <?php if (in_array('nativeamerican', $race)) echo 'checked="checked" '; ?>/>American Indian or Alaska Native</label>
								<div class="indented">
									<label class="indent">
										Tribal Affiliation(s)
										<input name="racenativetribe" id="racenativetribe" type="text" value="<?php if (isset($_POST['racenativetribe'])) echo str_replace('"', '&quot;', $_POST['racenativetribe']); ?>" />
									</label>
								</div>

								<label style="display: block"><input type="checkbox" class="option" name="race[]" id="raceasian" value="asian" <?php if (in_array('asian', $race)) echo 'checked="checked" '; ?> />Asian</label>

								<label style="display: block"><input type="checkbox" class="option" name="race[]" id="raceblack" value="black" <?php if (in_array('black', $race)) echo 'checked="checked" '; ?> />Black or African American</label>
								<label style="display: block"><input type="checkbox" class="option" name="race[]" id="racehawaiian" value="hawaiian" <?php if (in_array('hawaiian', $race)) echo 'checked="checked" '; ?> />Native Hawaiian or Other Pacific Islander</label>
								<label style="display: block"><input type="checkbox" class="option" name="race[]" id="racewhite" value="white" <?php if (in_array('white', $race)) echo 'checked="checked" '; ?> />White</label>
								<label style="display: block"><input type="checkbox" class="option" name="race[]" id="racerefused" value="refused" <?php if (in_array('refused', $race)) echo 'checked="checked" '; ?> />Do not wish to reveal</label>
						</fieldset>
					</li>
				<?php endif; ?>
				<?php if (isset($row['disability'])): ?>
					<li>
						<fieldset>
							<legend>Do you have any disabilities or impairments?</legend>
							<?php if (isset($errors['disability'])): ?>
								<p class="warning">Please make a selection, or choose to refuse to answer</p>
							<?php endif; ?>
							<div class="indented">
								<label><input <?php if (isset($_POST['disability']) && $_POST['disability'] == 'yes') echo 'checked="checked" '; ?>type="radio" value="yes" id="disabilityyes" name="disability" class="option"> Yes</label>
								<fieldset class="indented">
									<label><input type="checkbox" id="disabilityblind" name="specificDisability[]" value="visually impaired" class="option"> Blind / Visually Impaired</label><br />
									<label><input type="checkbox" id="disabilitydeaf" name="specificDisability[]" value="hard of hearing" class="option"> Deaf / Hard of Hearing</label><br />
									<label><input type="checkbox" id="disabilityphysical" name="specificDisability[]" value="physical disability" class="option"> Physical / Orthopedic Disability</label><br />
									<label><input type="checkbox" id="disabilitylearning" name="specificDisability[]" value="cognitive disability" class="option"> Learning / Cognitive Disability</label><br />
									<label><input type="checkbox" id="disabilityvocal" name="specificDisability[]" value="speech disability" class="option"> Vocal / Speech Disability</label><br />
									<label>Other (please specify): <input type="text" value="" id="disabilityother" name="otherDisability"></label>
								</fieldset>
								<label><input <?php if (isset($_POST['disability']) && $_POST['disability'] == 'no') echo 'checked="checked" '; ?>type="radio" value="no" id="disabilityno" name="disability" class="option"> No (none)</label><br />
								<label><input <?php if (isset($_POST['disability']) && $_POST['disability'] == 'refused') echo 'checked="checked" '; ?>type="radio" value="refused" id="disabilityrefused" name="disability" class="option"> Do not wish to reveal</label>
							</div>
						</fieldset>
					</li>
				<?php endif; ?>
				<?php if (isset($row['mailPreferenceOption'])): ?>
					<li>
						<?php if (isset($errors['mailPreferenceOption'])): ?>
							<p class="warning">Please make a selection.</p>
						<?php endif; ?>
						<label for="mailPreferenceOption">Would you like to receive email updates (newsletters, etc.)?</label>
						<div class="indented">
							<select size="3" name="mailPreferenceOption">
								<option value="-1" selected="selected">- Select email option &mdash;</option>
								<option value="1">Yes, send me emails</option>
								<option value="0">No, don't send me emails</option>
							</select>
						</div>
					</li>
				<?php endif; ?>
			</ol>
		<p>
			<input type="hidden" name="incremental-registration" value="update" />
			<button type="submit" name="submit" value="submit" type="submit">Submit</button>
			<button type="submit" name="submit" value="opt-out" type="submit">Ask me later</button>
		</p>
	</form>
</div>
