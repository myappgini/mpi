<?php
	include(dirname(__FILE__)."/header.php");

	// validate project name
	if (!isset($_REQUEST['axp']) || !preg_match('/^[a-f0-9]{32}$/i', $_REQUEST['axp'])){
		echo "<br>".$mpi->error_message('Project file not found.', 'index.php');
		exit;
	}
	
	$axp_md5 = $_REQUEST['axp'];
	$projectFile = '';
	$xmlFile = $mpi->get_xml_file($axp_md5 , $projectFile);
//-----------------------------------------------------------------------------------------
?>

<style>
	.item{
		cursor:pointer;
	}
</style>


<div class="bs-docs-section">
    <h1 class="page-header"><img src="vcard.png" style="height: 1em;"> Membership Profile Image for AppGini</h1>
    <p class="lead">
		<a href="./index.php">Projects</a> > <?php echo substr( $projectFile , 0 , strrpos( $projectFile , ".")); ?>
		<a href="output-folder.php?axp=<?php echo $axp_md5; ?>" class="pull-right btn btn-success btn-lg col-md-3 col-xs-12"><span class="glyphicon glyphicon-play"></span>  Enable MPI</a>
	</p>

</div>

<div class="row">
	<?php
		echo $mpi->show_tables(array(
			'axp' => $xmlFile,
			'click_handler' => 'mpi',
			'classes' => 'col-md-3 col-xs-12'
		)); 
	?>
    <div id="coment" class="col-md-9 col-xs-12">
        
        <div class="bs-callout bs-callout-info"> 
            <h4>Welcome to Mebership profile Image</h4> 
            
            <p>Thank you for choosing this plugin for your project, please click on the MPI enable button to continue.
               <br>
               The assistant will guide you easily to install the application
            </p> 
        </div>
        <div class="bs-callout bs-callout-danger"> 
            <h4>Considerations for its proper functioning</h4> 
            <p>The plugin works with version 5.70 of <strong>AppGini</strong>.<br>
                You also need to have acquired some other official <strong>AppGini</strong> plugin.<br>
                Verify that the <code>projects</code> folder is inside the <code>plugin</code> folder. 
            </p> 
        </div>
    </div>
</div>

<h4 id="bottom-links"><a href="./index.php"> &lt; Or open another project</a></h4>

<?php
	$xmlFile = json_encode($xmlFile);
?>

<script>	

	$j( document ).ready( function(){

		//add resize event
		$j(window).resize(function() {
  			$j("#tables-list").height( $j(window).height() - $j("#tables-list").offset().top -  $j("#bottom-links").height() - 70);
		});
		
		$j(window).resize();

	});

        function mpi(){
            return;
        }

	var xmlFile = <?php echo $xmlFile; ?>;
	
</script>



<?php include(dirname(__FILE__) . "/footer.php"); ?>