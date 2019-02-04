<?php
// 
// Author: Alejandro Landini
// generate.php 10/06/18, 22:03
// toDo:        *complete instal inctructions
// revision:
// 
//

include(dirname(__FILE__) . "/header.php");
$mpi_class = new mpi_class(array(
        'title' => 'Membership Profile Image',
        'name' => 'Membership Profile Image',
        'logo' => 'vcard.png',
        'output_path' => $_REQUEST['path']
    ));

// validate project name
if (!isset($_GET['axp']) || !preg_match('/^[a-f0-9]{32}$/i', $_GET['axp'])) {
	echo "<br>".$mpi_class->error_message('Project file not found.');
	exit;
}
$projectFile = '';
$xmlFile = $mpi_class->get_xml_file($_GET['axp'], $projectFile);

//-------------------------------------------------------------------------------------
//path check 
	try{
		if (!isset( $_POST['path'])){
			throw new RuntimeException('This page has expired');
		}
		
		$path = rtrim(trim($_POST['path']), '\\/');
		if (! is_dir($path)){
			throw new RuntimeException('Invalid path');
		}
		

		if ( ! ( file_exists("$path/lib.php") && file_exists("$path/db.php") && file_exists("$path/index.php") ) ){
			throw new RuntimeException('The given path is not a valid AppGini project path');
		}
		
		if (! is_writable($path."/hooks")){
			throw new RuntimeException('The hooks folder of the given path is not writable');
		}
		
		if (! is_writable($path."/resources")){
			throw new RuntimeException('The resources folder of the given path is not writable');
		}
	} catch (RuntimeException $e){
			echo "<br>".$mpi_class->error_message($e->getMessage());
			exit;
	}
//-------------------------------------------------------------------------------------

$write_to_hooks = ($_REQUEST['dont_write_to_hooks'] == 1 ? false : true);

?>

<div class="bs-docs-section row">
    <h1 class="page-header"><img src="vcard.png" style="height: 1em;"> Membership Profile Image for AppGini</h1>
    <p class="lead"><a href="./index.php">Projects</a> > <a href="./project.php?axp=<?php echo $_GET['axp']; ?>"><?php echo substr( $projectFile , 0 , strrpos( $projectFile , ".")); ?></a> > <a href="./output-folder.php?axp=<?php echo $_GET['axp'] ?>">  Select output folder</a> > Enabling MPI
	</p>

</div>

<h4>Progress log</h4>

<?php
	$mpi_class->progress_log->add("Output folder: $path", 'text-info');

	//coping resources folders
	
	$mpi_class->progress_log->ok();
	$mpi_class->progress_log->line();

	//coping files
        $mpi_class->progress_log->add("<b>Copying new files for '" . substr( $projectFile , 0 , strrpos( $projectFile , ".")) . "' project:</b>");
        
	$source_class = dirname(__FILE__) . '/app-resources/mpi.css';
	$dest_class = $path.'/hooks/mpi.css';
	$mpi_class->copy_file($source_class, $dest_class, true);	
	
	$source_class = dirname(__FILE__) . '/app-resources/mpi.js';
	$dest_class = $path.'/hooks/mpi.js';
	$mpi_class->copy_file($source_class, $dest_class, true);	
	
	$source_class = dirname(__FILE__) . '/app-resources/mpi.php';
	$dest_class = $path.'/hooks/mpi.php';
	$mpi_class->copy_file($source_class, $dest_class, true);	
	
	$source_class = dirname(__FILE__) . '/app-resources/mpi_AJAX.php';
	$dest_class = $path.'/hooks/mpi_AJAX.php';
	$mpi_class->copy_file($source_class, $dest_class, true);	
	
	$source_class = dirname(__FILE__) . '/app-resources/mpi_template.html';
	$dest_class = $path.'/hooks/mpi_template.html';
	$mpi_class->copy_file($source_class, $dest_class, true);
        
	$source_class = dirname(__FILE__) . '/app-resources/no_image.png';
	$dest_class = $path.'/images/no_image.png';
	$mpi_class->copy_file($source_class, $dest_class, true);
        
        
        $extras_file_path= $path . '/membership_profile.php' ;
        $extras_function='<div class="col-md-6">';
        $code="<?php echo file_get_contents('hooks/mpi_template.html');?>";
        $res = $mpi_class->add_to_extras($extras_file_path, $extras_function, $code,1);
	
        if($res){
                $mpi_class->progress_log->add("Installed code into '{$extras_file_path}'.", 'text-success spacer');
        }else{
            $error = $mpi_class->last_error();

            if($error == 'Code already exists'){
                    $mpi_class->progress_log->add("Skipped installing to '{$extras_file_path}', MPI is already installed.", 'text-warning spacer');
            }else{
                    $mpi_class->progress_log->add("Failed to install code '{$extras_file_path}': {$error}", 'text-danger spacer');
                    $mpi_class->progress_log->add($install_instructions, 'spacer');
            }
        }
        
        $extras_file_path= $path . '/hooks/header-extras.php' ;
        $extras_function='';
        $code="<link rel=\"stylesheet\" href=\"hooks/mpi.css\">\n\t\t<script src=\"hooks/mpi.js\"></script>\n\t\t<script>getMpi({cmd:'u'},true,false);</script>\n";
        $res = $mpi_class->add_to_extras($extras_file_path, $extras_function, $code);
	
        if($res){
                $mpi_class->progress_log->add("Installed code into '{$extras_file_path}'.", 'text-success spacer');
        }else{
            $error = $mpi_class->last_error();

            if($error == 'Code already exists'){
                    $mpi_class->progress_log->add("Skipped installing to '{$extras_file_path}', MPI is already installed.", 'text-warning spacer');
            }else{
                    $mpi_class->progress_log->add("Failed to install code '{$extras_file_path}': {$error}", 'text-danger spacer');
                    $mpi_class->progress_log->add($install_instructions, 'spacer');
            }
        }
        if($i) $mpi_class->progress_log->line();
	echo $mpi_class->progress_log->show();
?>

<center>
	<a style="margin:20px;" href="index.php" class="btn btn-success btn-lg"><span class="glyphicon glyphicon-home" ></span>   Start page</a>
</center>

<script>	
	$j( function(){

		$j("#progress").height( $j(window).height() - $j("#progress").offset().top - $j(".btn-success").height() - 100 );

		//add resize event
		$j( window ).resize(function() {
		   $j("#progress").height( $j(window).height() - $j("#progress").offset().top - $j(".btn-success").height() - 100 );
		});

	});
</script>

<?php include(dirname(__FILE__) . "/footer.php"); ?>