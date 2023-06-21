<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ImageLibrarySearch.php');
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/imagelib/search.'.$LANG_TAG.'.php')) $LANG_TAG = 'en';
include_once($SERVER_ROOT.'/content/lang/imagelib/search.'.$LANG_TAG.'.php');
header('Content-Type: text/html; charset='.$CHARSET);

$taxonType = isset($_REQUEST['taxontype']) ? filter_var($_REQUEST['taxontype'], FILTER_SANITIZE_NUMBER_INT) : 0;
$useThes = array_key_exists('usethes',$_REQUEST) ? filter_var($_REQUEST['usethes'], FILTER_SANITIZE_NUMBER_INT) : 0;
$taxaStr = isset($_REQUEST['taxa']) ? $_REQUEST['taxa'] : '';
$phUid = array_key_exists('phuid',$_REQUEST) ? filter_var($_REQUEST['phuid'], FILTER_SANITIZE_NUMBER_INT) : 0;
$tagExistance = array_key_exists('tagExistance',$_REQUEST) ? filter_var($_REQUEST['tagExistance'], FILTER_SANITIZE_NUMBER_INT) : 1;
$tag = array_key_exists('tag',$_REQUEST) ? $_REQUEST['tag'] : '';
$keywords = array_key_exists('keywords',$_REQUEST) ? $_REQUEST['keywords'] : '';
$imageCount = isset($_REQUEST['imagecount']) ? $_REQUEST['imagecount'] : 'all';
$imageType = isset($_REQUEST['imagetype']) ? filter_var($_REQUEST['imagetype'], FILTER_SANITIZE_NUMBER_INT) : 0;
$pageNumber = array_key_exists('page',$_REQUEST) ? filter_var($_REQUEST['page'], FILTER_SANITIZE_NUMBER_INT) : 1;
$cntPerPage = array_key_exists('cntperpage',$_REQUEST) ? filter_var($_REQUEST['cntperpage'], FILTER_SANITIZE_NUMBER_INT) : 200;
$imgTagAction = array_key_exists('imgTagAction',$_REQUEST) ? $_REQUEST['imgTagAction'] : '';
$action = array_key_exists('submitaction',$_REQUEST) ? $_REQUEST['submitaction'] : '';

if(!$useThes && !$action) $useThes = 1;
if(!$taxonType && isset($DEFAULT_TAXON_SEARCH)) $taxonType = $DEFAULT_TAXON_SEARCH;

//Sanitation
if(preg_match('/[^\D]+/', $action)) $action = '';

$connType = 'readonly';
if($action == 'batchAssignTag') $connType = 'write';
$imgLibManager = new ImageLibrarySearch($connType);
$imgLibManager->setTaxonType($taxonType);
$imgLibManager->setUseThes($useThes);
$imgLibManager->setTaxaStr($taxaStr);
$imgLibManager->setPhotographerUid($phUid);
$imgLibManager->setTagExistance($tagExistance);
$imgLibManager->setTag($tag);
$imgLibManager->setKeywords($keywords);
$imgLibManager->setImageCount($imageCount);
$imgLibManager->setImageType($imageType);
if(isset($_REQUEST['db'])) $imgLibManager->setCollectionVariables($_REQUEST);

$statusStr = '';
if($action == 'batchAssignTag'){
	$statusStr = '<span style="color:green">'.$LANG['ACTION_SUCCESS'].'</span>';
	if(!$imgLibManager->batchAssignImageTag($_POST)){
		$statusStr = '<span style="color:red">'.$LANG['ACTION_ERROR'].': '.$imgLibManager->getErrorStr().'</span>';
	}
}
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE.' '.$LANG['IMAGE_SEARCH']; ?> </title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	include_once($SERVER_ROOT.'/includes/googleanalytics.php');
	?>
	<link href="<?php echo $CSS_BASE_PATH; ?>/symbiota/collections/listdisplay.css" type="text/css" rel="stylesheet" />
	<link href="../js/jquery-ui/jquery-ui.min.css?ver=1" type="text/css" rel="Stylesheet" />
	<script src="../js/jquery-3.2.1.min.js" type="text/javascript"></script>
	<script src="../js/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
	<script src="../js/symb/collections.index.js?ver=2" type="text/javascript"></script>
	<script type="text/javascript">
		var clientRoot = "<?php echo $CLIENT_ROOT; ?>";

		function validateBatchActionBtn(f){
			if(f.imgTagAction.value == ""){
				alert("<?php echo $LANG['SELECT_TAG']; ?>");
				return false;
			}
			var formVerified = false;
			for(var h=0; h<f.length; h++){
				if(f.elements[h].name == "imgid[]" && f.elements[h].checked){
					formVerified = true;
					break;
				}
			}
			if(!formVerified){
				alert("<?php echo $LANG['SELECT_IMAGE']; ?>");
				return false;
			}
			return true;
		}

		function selectAllImages(cb){
			var boxesChecked = true;
			if(!cb.checked){
				boxesChecked = false;
			}
			var f = cb.form;
			for(var i=0; i<f.length; i++){
				if(f.elements[i].name == "imgid[]") f.elements[i].checked = boxesChecked;
			}
		}
	</script>
	<script src="../js/symb/api.taxonomy.taxasuggest.js?ver=4" type="text/javascript"></script>
	<script src="../js/symb/imagelib.search.js?ver=2" type="text/javascript"></script>
	<style type="text/css">
		fieldset{ padding: 15px }
		fieldset legend{ font-weight:bold }
		label{ font-weight:bold }
		.row-div{ clear: both }
		#action-status-div{ padding: 15px; }
	</style>
</head>
<body>
	<?php
	$displayLeftMenu = false;
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class="navpath">
		<a href="../index.php"><?php echo $LANG['HOME']; ?></a> &gt;&gt;
		<a href="contributors.php"><?php echo $LANG['IMAGE_CONTRIBUTORS']; ?></a> &gt;&gt;
		<b><?php echo $LANG['IMAGE_SEARCH']; ?></b>
	</div>
	<!-- This is inner text! -->
	<div id="innertext">
		<form name="imagesearchform" id="imagesearchform" action="search.php" method="post">
			<?php
			if($statusStr){
				echo '<div id="action-status-div">'.$statusStr.'</div>';
			}
			?>
			<div id="search-div">
				<fieldset>
					<legend><?php echo $LANG['SEARCH_CRITERIA']; ?></legend>
					<?php
					$isEditor = 0;
					if($IS_ADMIN) $isEditor = 1;
					elseif(array_key_exists('CollAdmin', $USER_RIGHTS) || array_key_exists('CollEditor', $USER_RIGHTS)) $isEditor = 2;
					elseif(array_key_exists('TaxonProfile', $USER_RIGHTS)) $isEditor = 2;
					if($isEditor){
						echo '<div style="float:right"><a href="#" onclick="$(\'.editor-div\').toggle()"><img class="icon-img" style="width:15px;" src="../images/edit.png"></a></div>';
					}
					?>
					<div id="criteria-div">
						<div class="row-div">
							<div style="float:left;">
								<select id="taxontype" name="taxontype">
									<?php
									for($h=1;$h<6;$h++){
										echo '<option value="'.$h.'" '.($imgLibManager->getTaxonType()==$h?'SELECTED':'').'>'.$LANG['SELECT_1-'.$h].'</option>';
									}
									?>
								</select>:&nbsp;
							</div>
							<div style="float:left;">
								<input id="taxa" name="taxa" type="text" style="width:450px;" value="<?php echo $imgLibManager->getTaxaStr(); ?>" title="<?php echo $LANG['SEPARATE_MULTIPLE']; ?>" autocomplete="off" />
							</div>
							<div style="float:left;margin-left:10px;" >
								<input name="usethes" type="checkbox" value="1" <?php if(!$action || $imgLibManager->getUseThes()) echo 'CHECKED'; ?> ><?php echo $LANG['INCLUDE_SYN']; ?>
							</div>
						</div>
						<div class="row-div">
							<label><?php echo $LANG['PHOTOGRAPHER'] ?></label>:
							<select name="phuid">
								<option value="">All Image Contributors</option>
								<option value="">-----------------------------</option>
								<?php
								$uidList = $imgLibManager->getPhotographerUidArr();
								foreach($uidList as $uid => $name){
									echo '<option value="'.$uid.'" '.($imgLibManager->getPhotographerUid()==$uid?'SELECTED':'').'>'.$name.'</option>';
								}
								?>
							</select>
						</div>
						<?php
						if($tagArr = $imgLibManager->getTagArr()){
							?>
							<div class="row-div">
								<label>Image Tags</label>:
								<select name="tagExistance">
									<option value="1">with</option>
									<option value="0" <?php echo ($tagExistance?'':'SELECTED'); ?>>without</option>
								</select>
								<select name="tag" >
									<option value="">Select Tag</option>
									<option value="">--------------</option>
									<?php
									foreach($tagArr as $tagKey => $displayText){
										echo '<option value="'.$tagKey.'" '.($tag==$tagKey?'SELECTED ':'').'>'.$displayText.'</option>';
									}
									?>
								</select>
							</div>
							<?php
						}
						?>
						<!--
						<div style="clear:both;margin-bottom:5px;">
							Image Keywords:
							<input type="text" id="keywords" style="width:350px;" name="keywords" value="<?php //echo $imgLibManager->getKeywordSuggest(); ?>" title="Separate multiple keywords w/ commas" />
						</div>
						-->
						<?php
						$collList = $imgLibManager->getFullCollectionList();
						$specArr = (isset($collList['spec'])?$collList['spec']:null);
						$obsArr = (isset($collList['obs'])?$collList['obs']:null);
						?>
						<div class="row-div">
							<label>Image Counts</label>:
							<select id="imagecount" name="imagecount">
								<option value="all" <?php echo ($imgLibManager->getImageCount()=='all'?'SELECTED ':''); ?>>All images</option>
								<option value="taxon" <?php echo ($imgLibManager->getImageCount()=='taxon'?'SELECTED ':''); ?>>One per taxon</option>
								<?php
								if($specArr){
									?>
									<option value="specimen" <?php echo ($imgLibManager->getImageCount()=='specimen'?'SELECTED ':''); ?>>One per specimen</option>
									<?php
								}
								?>
							</select>
						</div>
						<div class="row-div">
							<div style="margin-bottom:5px;float:left;">
								<label>Image Type</label>:
								<select name="imagetype" onchange="imageTypeChanged(this)">
									<option value="0">All Images</option>
									<option value="1" <?php echo ($imgLibManager->getImageType() == 1?'SELECTED':''); ?>>Specimen/Vouchered Images</option>
									<option value="3" <?php echo ($imgLibManager->getImageType() == 3?'SELECTED':''); ?>>Field Images (lacking specific locality details)</option>
								</select>
							</div>
							<div style="margin:0px 40px;float:left">
								<button name="submitaction" type="submit" value="search">Load Images</button>
							</div>
						</div>
						<?php
						if($specArr || $obsArr){
							$allChecked = '';
							if(!isset($_REQUEST['db']) || in_array('all', $_REQUEST['db'])) $allChecked = 'checked';
							?>
							<div id="collection-div" style="margin:15px;clear:both;display:<?php echo ($imgLibManager->getImageType() == 1 || $imgLibManager->getImageType() == 2?'':'none'); ?>">
								<fieldset>
									<legend>Collections</legend>
									<div id="specobsdiv">
										<div style="margin:0px 0px 10px 5px;">
											<input id="dballcb" name="db[]" class="specobs" value='all' type="checkbox" onclick="selectAll(this);" <?php echo $allChecked ?> />
											<?php echo $LANG['SELECT_ALL']; ?>
										</div>
										<?php
										$imgLibManager->outputFullCollArr($specArr, 9999);
										if($specArr && $obsArr) echo '<hr style="clear:both;margin:20px 0px;"/>';
										$imgLibManager->outputFullCollArr($obsArr, 9999);
										?>
									</div>
								</fieldset>
							</div>
							<?php
						}
						?>
					</div>
					<?php
					if($isEditor){
						?>
						<div class="editor-div" style="display:none; clear: both;">
							<fieldset>
							<legend>Action Panel</legend>
								<div class="row-div">
									<label>Image Tag:</label>
									<select name="imgTagAction">
										<option value="">Select Tag</option>
										<option value="">---------------------</option>
										<?php
										foreach($tagArr as $k){
											echo '<option value="'.$k.'">'.$k.'</option>';
										}
										?>
									</select>
									<button name="submitaction" type="submit" value="batchAssignTag" onclick="return validateBatchActionBtn(this.form)">Batch Assign Tag</button>
								</div>
								<div class="row-div">
									<input id="imgselectall" name="imgselectall" type="checkbox" onclick="selectAllImages(this);" />
									<?php echo $LANG['SELECT_ALL_IMAGES']; ?>
								</div>
							</fieldset>
						</div>
						<?php
					}
					?>
				</fieldset>
				<hr>
			</div>
			<?php
			if($action){
				?>
				<div id="imagesdiv">
					<div id="imagebox">
						<?php
						$imageArr = $imgLibManager->getImageArr($pageNumber,$cntPerPage);
						$recordCnt = $imgLibManager->getRecordCnt();
						if($imageArr){
							$lastPage = ceil($recordCnt / $cntPerPage);
							$startPage = ($pageNumber > 4?$pageNumber - 4:1);
							$endPage = ($lastPage > $startPage + 9?$startPage + 9:$lastPage);
							$url = 'search.php?'.$imgLibManager->getQueryTermStr().'&submitaction=search';
							$pageBar = '<div style="float:left" >';
							if($startPage > 1){
								$pageBar .= '<span class="pagination" style="margin-right:5px;"><a href="'.$url.'&page=1">First</a></span>';
								$pageBar .= '<span class="pagination" style="margin-right:5px;"><a href="'.$url.'&page='.(($pageNumber - 10) < 1 ?1:$pageNumber - 10).'">&lt;&lt;</a></span>';
							}
							for($x = $startPage; $x <= $endPage; $x++){
								if($pageNumber != $x){
									$pageBar .= '<span class="pagination" style="margin-right:3px;"><a href="'.$url.'&page='.$x.'">'.$x.'</a></span>';
								}
								else{
									$pageBar .= "<span class='pagination' style='margin-right:3px;font-weight:bold;'>".$x."</span>";
								}
							}
							if(($lastPage - $startPage) >= 10){
								$pageBar .= '<span class="pagination" style="margin-left:5px;"><a href="'.$url.'&page='.(($pageNumber + 10) > $lastPage?$lastPage:($pageNumber + 10)).'">&gt;&gt;</a></span>';
								if($recordCnt < 10000) $pageBar .= '<span class="pagination" style="margin-left:5px;"><a href="'.$url.'&page='.$lastPage.'">Last</a></span>';
							}
							$pageBar .= '</div><div style="float:right;margin-top:4px;margin-bottom:8px;">';
							$beginNum = ($pageNumber - 1)*$cntPerPage + 1;
							$endNum = $beginNum + $cntPerPage - 1;
							if($endNum > $recordCnt) $endNum = $recordCnt;
							$pageBar .= "Page ".$pageNumber.", records ".number_format($beginNum)."-".number_format($endNum)." of ".number_format($recordCnt)."</div>";
							$paginationStr = $pageBar;
							echo '<div style="width:100%;">'.$paginationStr.'</div>';
							echo '<div style="clear:both;margin:5 0 5 0;"><hr /></div>';
							echo '<div style="width:98%;margin-left:auto;margin-right:auto;">';
							$occArr = array();
							$collArr = array();
							if(isset($imageArr['occ'])){
								$occArr = $imageArr['occ'];
								unset($imageArr['occ']);
								$collArr = $imageArr['coll'];
								unset($imageArr['coll']);
							}
							foreach($imageArr as $imgId => $imgArr){
								$imgUrl = $imgArr['url'];
								$imgTn = $imgArr['thumbnailurl'];
								if($imgTn){
									$imgUrl = $imgTn;
									if($IMAGE_DOMAIN && substr($imgTn,0,1)=='/') $imgUrl = $IMAGE_DOMAIN.$imgTn;
								}
								elseif($IMAGE_DOMAIN && substr($imgUrl,0,1)=='/'){
									$imgUrl = $IMAGE_DOMAIN.$imgUrl;
								}
								?>
								<div class="tndiv" style="margin-bottom:15px;margin-top:15px;">
									<div class="tnimg">
										<?php
										$anchorLink = '';
										if($imgArr['occid']){
											$anchorLink = '<a href="#" onclick="openIndPU('.$imgArr['occid'].');return false;">';
										}
										else{
											$anchorLink = '<a href="#" onclick="openImagePopup('.$imgId.');return false;">';
										}
										echo $anchorLink.'<img src="'.$imgUrl.'" /></a>';
										?>
									</div>
									<div class="details-div">
										<?php
										if($isEditor == 2){
											if($imgArr['occid']){
												$collid = $occArr[$imgArr['occid']]['collid'];
												if($collid){
													if(array_key_exists('CollAdmin', $USER_RIGHTS) && in_array($collid, $USER_RIGHTS['CollAdmin'])) $isEditor = true;
													elseif(array_key_exists('CollEditor', $USER_RIGHTS) && in_array($collid, $USER_RIGHTS['CollEditor'])) $isEditor = true;
												}
											}
											else{
												if(array_key_exists('TaxonProfile', $USER_RIGHTS)) $isEditor = true;
											}
										}
										if($isEditor){
											echo '<div class="editor-div" style="display:none;margin-top:3px;"><input name="imgid[]" type="checkbox" value="'.$imgId.'"></div>';
										}
										$sciname = $imgArr['sciname'];
										if(!$sciname && $imgArr['occid'] && $occArr[$imgArr['occid']]['sciname']) $sciname = $occArr[$imgArr['occid']]['sciname'];
										if($sciname){
											if(strpos($imgArr['sciname'],' ')) $sciname = '<i>'.$sciname.'</i>';
											if($imgArr['tid']) echo '<a href="#" onclick="openTaxonPopup('.$imgArr['tid'].');return false;" >';
											echo $sciname;
											if($imgArr['tid']) echo '</a>';
											echo '<br />';
										}
										$photoAuthor = '';
										$authorLink = '';
										if($imgArr['uid']){
											$photoAuthor = $uidList[$imgArr['uid']];
											if(strlen($photoAuthor) > 23){
												$nameArr = explode(',',$photoAuthor);
												$photoAuthor = array_shift($nameArr);
											}
										}
										if($imgArr['occid']){
											$authorLink = '<a href="#" onclick="openIndPU('.$imgArr['occid'].');return false;">';
											if(!$photoAuthor){
												if($occArr[$imgArr['occid']]['recordedby']) $photoAuthor = $occArr[$imgArr['occid']]['recordedby'];
												else{
													if(strpos($occArr[$imgArr['occid']]['catnum'], $collArr[$occArr[$imgArr['occid']]['collid']]) !== 0)
														$photoAuthor = $collArr[$occArr[$imgArr['occid']]['collid']].': ';
													$photoAuthor .=  $occArr[$imgArr['occid']]['catnum'];
												}
											}
										}
										if(!$authorLink) $authorLink = $anchorLink;
										echo $authorLink.htmlspecialchars($photoAuthor).'</a>';
										?>
									</div>
								</div>
								<?php
							}
							echo "</div>";
							if($lastPage > $startPage){
								echo "<div style='clear:both;margin:5 0 5 0;'><hr /></div>";
								echo '<div style="width:100%;">'.$paginationStr.'</div>';
							}
							?>
							<div style="clear:both;"></div>
							<?php
						}
						else{
							echo '<h3>No images exist matching your search criteria. Please modify your search and try again.</h3>';
						}
						?>
					</div>
				</div>
				<?php
			}
			?>
		</form>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>