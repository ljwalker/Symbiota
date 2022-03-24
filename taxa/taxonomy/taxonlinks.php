<?php
include_once ('../config/symbini.php');
include_once ($SERVER_ROOT . '/classes/TaxonProfile.php');
header("Content-Type: text/html; charset=".$CHARSET);

$tid = $_REQUEST["tid"];
//$tlid = array_key_exists('tlid', $_REQUEST) ? $_REQUEST['tlid'] : '';
$submitAction = array_key_exists('submitaction', $_POST) ? $_POST['submitaction'] : '';

// Sanitation
if(!is_numeric($tlid)) $tlid = 0;
if(!is_numeric($tid)) $tid = 0;
$submitAction = filter_var($submitAction, FILTER_SANITIZE_STRING);

$taxLinkManager = new TaxonProfile();

$isEditor = false;
if($IS_ADMIN || array_key_exists('CollAdmin',$USER_RIGHTS)) $isEditor = true;

$statusStr = '';
if($isEditor && $submitAction) {
	if($submitAction == 'submitTaxaLinkEdits'){
		$status = $taxLinkManager->editTaxaLink($_POST);
		if(!$status) $statusStr = $taxLinkManager->getErrorMessage();
	}
	elseif($submitAction == 'deleteTaxaLink'){
		$status = $taxLinkManager->deleteTaxaLink($_POST['delTaxaLinkID']);
		if(!$status) $statusStr = $taxLinkManager->getErrorMessage();
	}
		elseif($submitAction == 'addTaxaLink'){
		$status = $taxLinkManager->addTaxaLink($_POST);
		if(!$status) $statusStr = $taxLinkManager->getErrorMessage();
	}
}

$linkArr = $taxLinkManager->getLinkArr($tid);

?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> - Taxon Links Manager</title>
	<?php
	$activateJQuery = true;
	include_once ($SERVER_ROOT.'/includes/head.php');
	?>
	<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery.js" type="text/javascript"></script>
	<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-ui.js" type="text/javascript"></script>
	<script type="text/javascript">
		function toggleEditor(){
			$(".editTerm").toggle();
			$(".editFormElem").toggle();
			$("#editButton-div").toggle();
			$("#edit-legend").toggle();
			$("#unitDel-div").toggle();
		}
	</script>
	<style type="text/css">
		fieldset{ margin: 10px; padding: 15px; width: 700px }
		legend{ font-weight: bold; }
		label{ text-decoration: underline; }
		#edit-legend{ display: none }
		.field-div{ margin: 3px 0px }
		.editIcon{  }
		.editTerm{ }
		.editFormElem{ display: none }
		#editButton-div{ display: none }
		#unitDel-div{ display: none }
		.button-div{ margin: 15px }
		.link-div{ margin:0px 30px }
		#status-div{ margin:15px; padding: 15px; color: red; }
	</style>
</head>
<body>
	<?php
	$displayLeftMenu = (isset($profile_indexMenu)?$profile_indexMenu:'true');
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class="navpath">
		<a href="../index.php">Home</a> &gt;&gt;
		<b><a href="index.php">Taxon Links</a></b>
	</div>
	<div id='innertext'>
		<?php
		if($statusStr){
			echo '<div id="status-div">'.$statusStr.'</div>';
		}
		if($tid){
			$linkArr = $taxLinkManager->getLinkArr($tid);
			//$rankArr = $geoManager->getGeoRankArr();
			?>
			<div id="updateTaxLink-div" style="clear:both;margin-bottom:10px;">
				<fieldset id="edit-fieldset">
					<legend>Edit Links<span id="edit-legend"></span></legend>
					<div style="float:right">
						<span class="editIcon"><a href="#" onclick="toggleEditor()"><img class="editimg" src="../images/edit.png" /></a></span>

					</div>
					<form name="unitEditForm" action="index.php" method="post">
						<div class="field-div">
							<label>Link Title</label>:
							<span class="editLink"><?php echo $linkArr['title']; ?></span>
							<span class="editFormElem"><input type="text" name="title" value="<?php echo $linkArr['title'] ?>" style="width:200px;" required /></span>
						</div>
						<div class="field-div">
							<label>URL</label>:
							<span class="editTerm"><?php echo $linkArr['url']; ?></span>
							<span class="editFormElem"><input type="text" name="url" value="<?php echo $linkArr['url'] ?>" style="width:50px;" /></span>
						</div>
						<div class="field-div">
							<label>Source Identifier</label>:
							<span class="editTerm"><?php echo $linkArr['sourceIdentifier']; ?></span>
							<span class="editFormElem"><input type="text" name="sourceIdentifier" value="<?php echo $linkArr['sourceIdentifier'] ?>" style="width:50px;" /></span>
						</div>
						<div class="field-div">
							<label>Owner</label>:
							<span class="editTerm"><?php echo $linkArr['owner']; ?></span>
							<span class="editFormElem"><input type="text" name="owner" value="<?php echo $linkArr['owner'] ?>" style="width:50px;" /></span>
						</div>
						<div id="editButton-div" class="button-div">
							<input name="tlid" type="hidden" value="<?php echo $tlid; ?>" />
							<button type="submit" name="submitaction" value="submitTaxaLinkEditsEdits">Save Edits</button>
						</div>
					</form>
				</fieldset>
			</div>
			<div id="unitDel-div">
				<form name="linkDeleteForm" action="index.php" method="post">
					<fieldset>
						<legend>Delete Link</legend>
						<div class="button-div">
							<input name="parentID" type="hidden" value="<?php echo $geoUnit['parentID']; ?>" />
							<input name="delGeoThesID" type="hidden"  value="<?php echo $geoThesID; ?>" />
							<button type="submit" name="submitaction" value="deleteTaxaLink" onclick="return confirm('Are you sure you want to delete this link?')">Delete Link</button>
						</div>
					</fieldset>
				</form>
			</div>
			
			
			
			
			
			<?php
			echo '<div class="link-div">';
			echo '<div><a href="index.php?'.(isset($geoUnit['parentID'])?'parentID='.$geoUnit['parentID']:'').'">Show '.(isset($geoUnit['geoLevel'])?$rankArr[$geoUnit['geoLevel']]:'').' terms</a></div>';
			if(isset($geoUnit['childCnt']) && $geoUnit['childCnt']) echo '<div><a href="index.php?parentID='.$geoThesID.'">Show children</a></div>';
			echo '</div>';
		}
		else{
			?>
			
			
			
			
			
			<div style="float:right">
				<span class="editIcon"><a href="#" onclick="$('#addTaxLink-div').toggle();"><img class="editimg" src="../images/add.png" /></a></span>
			</div>
			<div id="addTaxLink-div" style="clear:both;margin-bottom:10px;display:none">
				<!--This should also be visible when !$geoThesID -->
				<fieldset id="new-fieldset">
					<legend>Add Link</legend>
					<form name="unitAddForm" action="index.php" method="post">
						<div class="field-div">
							<label>Title</label>:
							<span><input type="text" name="title" style="width:200px;" required /></span>
						</div>
						<div class="field-div">
							<label>URL</label>:
							<span><input type="text" name="url" style="width:50px;" /></span>
						</div>
						<div class="field-div">
							<label>Source Identifier</label>:
							<span><input type="text" name="sourceIdentifier" style="width:50px;" /></span>
						</div>
						<div class="field-div">
							<label>Owner</label>:
							<span><input type="text" name="owner" style="width:50px;" /></span>
						</div>
						<div id="addButton-div" class="button-div">
							<button type="submit" name="submitaction" value="addTaxaLink">Add Link</button>
						</div>
					</form>
				</fieldset>
			</div>
			
			
			
			
			
			<?php
			if($geoArr){
				$titleStr = '';
				$parentArr = $geoManager->getGeograpicUnit($parentID);
				if($parentID){
					$rankArr = $geoManager->getGeoRankArr();
					$titleStr = '<b>'.$rankArr[$geoArr[key($geoArr)]['geoLevel']].'</b> geographic terms within <b>'.$parentArr['geoTerm'].'</b>';
				}
				else{
					$titleStr = '<b>Root Terms (terms without parents)</b>';
				}
				echo '<div style=";font-size:1.3em;margin: 10px 0px">'.$titleStr.'</div>';
				echo '<ul>';
				foreach($geoArr as $geoID => $unitArr){
					$termDisplay = '<a href="index.php?geoThesID='.$geoID.'">'.$unitArr['geoTerm'].'</a>';
					if($unitArr['abbreviation']) $termDisplay .= ' ('.$unitArr['abbreviation'].') ';
					else{
						$codeStr = '';
						if($unitArr['iso2']) $codeStr = $unitArr['iso2'].', ';
						if($unitArr['iso3']) $codeStr .= $unitArr['iso3'].', ';
						if($unitArr['numCode']) $codeStr .= $unitArr['numCode'].', ';
						if($codeStr) $termDisplay .= ' ('.trim($codeStr,', ').') ';
					}
					if($unitArr['acceptedTerm']) $termDisplay .= ' => <a href="index.php?geoThesID='.$unitArr['acceptedID'].'">'.$unitArr['acceptedTerm'].'</a>';
					elseif(isset($unitArr['childCnt']) && $unitArr['childCnt']) $termDisplay .= ' - <a href="index.php?parentID='.$geoID.'">'.$unitArr['childCnt'].' children</a>';
					echo '<li>'.$termDisplay.'</li>';
				}
				echo '</ul>';
				if($parentID) echo '<div class="link-div"><a href="index.php?parentID='.$parentArr['parentID'].'">Show Parent list</a></div>';
			}
			else echo '<div>No records returned</div>';
			if($geoThesID || $parentID) echo '<div class="link-div"><a href="index.php">Show base list</a></div>';
		}
		?>
		
		
		
		
		
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>