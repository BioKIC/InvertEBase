<?php
include_once ('../../config/symbini.php');
include_once ($SERVER_ROOT . '/classes/GeographicThesaurus.php');
header("Content-Type: text/html; charset=" . $CHARSET);

if(!$SYMB_UID) header('Location: ../profile/index.php?refurl=../collections/georef/thesaurus.php?' . htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$geoThesID = array_key_exists('geoThesID', $_REQUEST) ? $_REQUEST['geoThesID'] : '';
$parentID = array_key_exists('parentID', $_REQUEST) ? $_REQUEST['parentID'] : '';
$category = array_key_exists('category', $_POST) ? $_POST['category'] : '';
$submitAction = array_key_exists('submitaction', $_POST) ? $_POST['submitaction'] : '';

// Sanitation
if(!is_numeric($geoThesID)) $geoThesID = 0;
if(!is_numeric($parentID)) $parentID = 0;
$category = filter_var($category, FILTER_SANITIZE_STRING);
$submitAction = filter_var($submitAction, FILTER_SANITIZE_STRING);

$geoManager = new GeographicThesaurus();

$isEditor = false;
if($IS_ADMIN || array_key_exists('CollAdmin',$USER_RIGHTS)) $isEditor = true;

$statusStr = '';
if($isEditor && $submitAction) {
	if($submitAction == 'updateGeoUnit'){
		$statusStr = $geoManager->updateGeoUnit($_POST);
	}
}

$geoArr = $geoManager->getGeograpicList($parentID);

?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> - Geographic Thesaurus Manager</title>
	<?php
	$activateJQuery = true;
	include_once ($SERVER_ROOT . '/includes/head.php');
	?>
	<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery.js" type="text/javascript"></script>
	<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-ui.js" type="text/javascript"></script>
</head>
<body>
	<?php
	$displayLeftMenu = (isset($profile_indexMenu)?$profile_indexMenu:'true');
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div id='innertext'>
		<?php
		if($geoThesID){
			$geoUnit = $geoManager->getGeograpicUnit($geoThesID);
			//Display details for geographic unit with edit and addNew symbols displayed to upper right
			echo '<div style="font-weight:bold;margin-bottom:10px">'.$geoUnit['geoTerm'].'</div>';
			?>
			<a id="editGeoUnitToggleDiv" onclick="toggle('editgeounitdiv');">
			<img class="editimg" src="../../images/edit.png" />
			</div>
		<?php
			echo '<div style="margin-bottom:10px">Need to display geoUnit details here</div>';
			
			//Provide a form to edit the geo unit that is hidden by default until user clicks edit symbol
		?>
			<div  id="editgeounitdiv">
				<div id="geoUnitNameDiv">
					<b>GeoUnit Name</b>
					<input type="text" id="geounitname" name="geounitname" maxlength="250" style="width:200px;" /><br>
					<b>GeoUnit Child</b>
					<br>
					<b>GeoUnit Parent</b>
				</div>
			</div>
		<?php
			//Provide a form for adding a new child term associated with this geo unit


			if(isset($geoUnit['parentID']) && $geoUnit['parentID']) echo '<div><a href="thesaurus.php?parentID='.$geoUnit['parentID'].'">Return to list</a></div>';
			if(isset($geoUnit['parentID']) && $geoUnit['parentID']) echo '<div><a href="thesaurus.php?geoThesID='.$geoUnit['parentID'].'">Show parent term</a></div>';
			if(isset($geoUnit['childCnt']) && $geoUnit['childCnt']) echo '<div><a href="thesaurus.php?parentID='.$geoThesID.'">Show children taxa</a></div>';
		}
		else{
			if($geoArr){
				$titleStr = '';
				if($parentID){
					$untiArr = $geoManager->getGeograpicUnit($parentID);
					$titleStr = '<b>'.$geoArr[key($geoArr)]['category'].'</b> geographic terms within <b>'.$untiArr['geoTerm'].'</b>';
				}
				else{
					$titleStr = '<b>Country</b> Terms';
				}
				echo '<div style=";font-size:1.3em;margin: 10px 0px">'.$titleStr.'</div>';
				echo '<ul>';
				foreach($geoArr as $geoID => $unitArr){
					$termDisplay = $unitArr['geoTerm'];
					if(!$unitArr['acceptedTerm']) $termDisplay = '<a href="thesaurus.php?geoThesID='.$geoID.'">'.$termDisplay.'</a>';
					if($unitArr['abbreviation']) $termDisplay .= ' ('.$unitArr['abbreviation'].') ';
					else{
						$codeStr = '';
						if($unitArr['iso2']) $codeStr = $unitArr['iso2'].', ';
						if($unitArr['iso3']) $codeStr .= $unitArr['iso3'].', ';
						if($unitArr['numCode']) $codeStr .= $unitArr['numCode'].', ';
						if($codeStr) $termDisplay .= ' ('.trim($codeStr,', ').') ';
					}
					if($unitArr['acceptedTerm']) $termDisplay .= ' => <a href="thesaurus.php?geoThesID='.$geoID.'">'.$unitArr['acceptedTerm'].'</a>';
					elseif(isset($unitArr['childCnt']) && $unitArr['childCnt']) $termDisplay .= ' - <a href="thesaurus.php?parentID='.$geoID.'">'.$unitArr['childCnt'].' children</a>';
					echo '<li>'.$termDisplay.'</li>';
				}
				echo '</ul>';
			}
			else{
				echo '<div>No records returned</div>';
			}
			if($geoThesID || $parentID) echo '<div><a href="thesaurus.php">Show base list</a></div>';
		}
		?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>