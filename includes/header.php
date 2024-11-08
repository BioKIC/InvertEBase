<?php
if($LANG_TAG == 'en' || !file_exists($SERVER_ROOT.'/content/lang/templates/header.' . $LANG_TAG . '.php'))
	include_once($SERVER_ROOT . '/content/lang/templates/header.en.php');
else include_once($SERVER_ROOT . '/content/lang/templates/header.' . $LANG_TAG . '.php');
$SHOULD_USE_HARVESTPARAMS = $SHOULD_USE_HARVESTPARAMS ?? false;
$collectionSearchPage = $SHOULD_USE_HARVESTPARAMS ? '/collections/index.php' : '/collections/search/index.php';
?>
<div class="header-wrapper">
	<header>
		<div class="top-wrapper">
			<a class="screen-reader-only" href="#end-nav"><?= $LANG['H_SKIP_NAV'] ?></a>
			<nav class="top-login" aria-label="horizontal-nav">
				<?php
				if ($USER_DISPLAY_NAME) {
					?>
					<div class="welcome-text bottom-breathing-room-rel">
						<?= $LANG['H_WELCOME'] . ' ' . $USER_DISPLAY_NAME ?>!
					</div>
					<span style="white-space: nowrap;" class="button button-tertiary bottom-breathing-room-rel">
						<a href="<?= $CLIENT_ROOT ?>/profile/viewprofile.php"><?= $LANG['H_MY_PROFILE'] ?></a>
					</span>
					<span style="white-space: nowrap;" class="button button-secondary bottom-breathing-room-rel">
						<a href="<?= $CLIENT_ROOT ?>/profile/index.php?submit=logout"><?= $LANG['H_LOGOUT'] ?></a>
					</span>
					<?php
				} else {
					?>
					<span class="button button-secondary" >
						<a href="<?= $CLIENT_ROOT . "/profile/index.php?refurl=" . htmlspecialchars($_SERVER['SCRIPT_NAME'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . "?" . htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES); ?>">
							<?= $LANG['H_LOGIN'] ?>
						</a>
					</span>
					<?php
				}
				?>
			</nav>
			<div class="top-brand">
				<a href="<?= $CLIENT_ROOT ?>">
					<div class="image-container">
						<img src="<?= $CLIENT_ROOT ?>/images/layout/InvertEBase_logo_rbg.png" alt="InvertEBase logo">
					</div>
				</a>
				<div class="brand-name">
					<h1 style="color:#02336A;">InvertEBase</h1>
					<h2 style="color:#02336A;">Reaching back to see the future</h2>
				</div>
			</div>
		</div>
		<div class="menu-wrapper" >
			<!-- Hamburger icon -->
			<input class="side-menu" type="checkbox" id="side-menu" name="side-menu" />
			<label class="hamb hamb-line hamb-label" for="side-menu" tabindex="0">☰</label>
			<!-- Menu -->
			<nav class="top-menu" aria-label="hamburger-nav">
				<ul class="menu" >
					<li>
						<a href="<?= $CLIENT_ROOT ?>/index.php">
							<?= $LANG['H_HOME'] ?>
						</a>
					</li>
                                        <li>
                                                <a href="<?= $CLIENT_ROOT . '/misc/aboutproject.php' ?>">
                                                        Project Information
                                                </a>
                                        </li>
                                        <li>
                                                <a href="#">Search</a>
                                                <ul>
                                              		<li>
								<a href="<?= $CLIENT_ROOT . $collectionSearchPage ?>">
                                                        		<?= $LANG['H_SEARCH'] ?>
                                                		</a>
                                        		</li>
                                                        <li>
                                                		<a href="<?= $CLIENT_ROOT ?>/collections/map/index.php" rel="noopener noreferrer">
                                                        		<?= $LANG['H_MAP_SEARCH'] ?>
                                                		</a>
                                        		</li>
                                                        <li>
                                                                <a href="<?= $CLIENT_ROOT ?>/checklists/dynamicmap.php?interface=checklist" rel="noopener noreferrer">
                                                                        Dynamic Checklists
                                                                </a>
                                                        </li>
                                                </ul>
                                        </li>
                                        <li>
                                                <a href="#">Images</a>
                                                <ul>
                                                        <li>
                                                                <a href="<?= $CLIENT_ROOT ?>/imagelib/index.php">
                                                                        Image Browser
                                                                </a>
                                                        </li>
                                        		<li>
                                                		<a href="<?= $CLIENT_ROOT ?>/imagelib/search.php">
                                                        		Search Images
                                                		</a>
                                        		</li>
                                                </ul>
                                        </li>
                                        <li>
                                                <a href="#">Inventories</a>
                                                <ul>
                                                        <li>
                                                                <a href="<?= $CLIENT_ROOT ?>/projects/index.php?pid=6">
                                                                        Geographic Inventories
                                                                </a>
                                                        </li>
                                                        <li>
                                                                <a href="<?= $CLIENT_ROOT ?>/projects/index.php?pid=5">
                                                                        Taxonomic Inventories
                                                                </a>
                                                        </li>
                                                </ul>
                                        </li>
                                        <li>
                                                <a href="#">Glossaries</a>
                                                <ul>
                                                        <li>
                                                                <a href="<?= $CLIENT_ROOT ?>/glossary/sources.php">
                                                                        Contributors
                                                                </a>
                                                        </li>
                                                        <li>
                                                                <a href="<?= $CLIENT_ROOT ?>/glossary/index.php">
                                                                       Search Glossary
                                                                </a>
                                                        </li>
                                                </ul>
                                        </li>
					<li>
						<a href="<?= $CLIENT_ROOT ?>/includes/usagepolicy.php">
							<?= $LANG['H_DATA_USAGE'] ?>
						</a>
					</li>
                                        <li>
                                                <a href="#">Associated Projects</a>
                                                <ul>
                                                        <li>
                                                                <a href="https://invertebase.org/stri/" target="_blank">
                                                                        STRI Marine Portal
                                                                </a>
                                                        </li>
                                                        <li>
                                                                <a href="https://scan-bugs.org/portal/">
                                                                        SCAN-bugs
                                                                </a>
                                                        </li>
                                                        <li>
                                                                <a href="https://ecdysis.org/">
                                                                        Ecdysis
                                                                </a>
                                                        </li>
                                                        <li>
                                                                <a href="https://www.gbif.org/">
                                                                        GBIF
                                                                </a>
                                                        </li>
                                                        <li>
                                                                <a href="https://www.idigbio.org/">
                                                                        iDigBio
                                                                </a>
                                                        </li>
                                                </ul>
                                        </li>
					<li>
						<a href='<?= $CLIENT_ROOT ?>/sitemap.php'>
							<?= $LANG['H_SITEMAP'] ?>
						</a>
					</li>
					<!-- <li id="lang-select-li">
						<label for="language-selection"><?= $LANG['H_SELECT_LANGUAGE'] ?>: </label>
						<select oninput="setLanguage(this)" id="language-selection" name="language-selection">
							<option value="en">English</option>
							<option value="es" <?= ($LANG_TAG=='es'?'SELECTED':'') ?>>Español</option>
							<option value="fr" <?= ($LANG_TAG=='fr'?'SELECTED':'') ?>>Français</option>
						</select>
					</li> -->
				</ul>
			</nav>
		</div>
		<div id="end-nav"></div>
	</header>
</div>
