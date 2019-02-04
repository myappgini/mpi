<?php
	include(dirname(__FILE__).'/../plugins-resources/loader.php');

	class mpi_class extends AppGiniPlugin{
		/* add any plugin-specific properties here */
		
		public function __construct($config = array()){
			parent::__construct($config);
			
			/* add any further plugin-specific initialization here */
		}
		
		/* add any further plugin-specific methods here */
                
                /**
		 * Injects provided code to a extras file
		 * @param $extras_file_path the full path of the extras file
		 * @param $extras_function name of the extras function to inject code into
		 * @param $code the [PHP] code to inject to the extras file
		 * @param $location 'top' injects code directly after function declaration line
		 *                  'bottom' injects code directly before the last return statement in the
		 *                           function or before the ending curly bracket if no return statement
		 *                           found before it.
		 *                  >>>> 'bottom' is not yet supported -- only 'top' is supported now.
		 * @return true on success, false on failure
		 */
		public function add_to_extras($extras_file_path, $extras_function, $code, $location = 0){
			/* Check if file exists and is writable */
			$extras_code = @file_get_contents($extras_file_path);
                        if (filesize($extras_file_path)>0){
                            if(!$extras_code) return $this->error('add_to_extras', 'Unable to access hook file');
                        }
			
			/* Find extras function */
                        $search = '/('.$extras_function.')/' ;
			preg_match_all($search, $extras_code, $matches, PREG_OFFSET_CAPTURE);
			if(count($matches) < $location + 1) return $this->error('add_to_extras', 'Could not determine correct function location');
			
                        /* start position of extras function */
                        $hf_position = $matches[0][$location][1];

                        /* position of next function, or EOF position if this is the last function in the file */
                        $nf_position = strlen($extras_code);
                        preg_match_all('/(<!-- group and IP address -->)/', $extras_code, $matches, PREG_OFFSET_CAPTURE, $hf_position + 10);
                        if(count($matches)) $nf_position = $matches[0][0][1];

                        /* extras function code */
                        $old_function_code = substr($extras_code, $hf_position, $nf_position - $hf_position);
                        /* Checks $code is not already in there */
                        if(strpos($old_function_code, $code) !== false) return $this->error('add_to_extras', 'Code already exists');

                        /* insert $code and save */
                        $code_comment = "/* Inserted by {$this->title} on " . date('Y-m-d h:i:s') . " */";
                        $new_code ="\n\t\t<?php {$code_comment} ?>\n\t\t{$code}\n\t\t<?php /* End of {$this->title} code */ ?>\n";

                        $new_function_code = preg_replace(
                                "/".makeSafe($extras_function)."/" ,
                                $new_code,
                                $old_function_code, 
                                1
                        );
                        if(!$new_function_code) return $this->error('add_to_extras', 'Error while injecting code');
                        if($new_function_code == $old_function_code) return $this->error('add_to_extras', 'Nothing changed');

                        $extras_code = substr_replace($extras_code, $new_function_code,$hf_position + strlen($extras_function) ,0);
                        if(!@file_put_contents($extras_file_path, $extras_code)) return $this->error('add_to_extras', 'Could not save changes');
				
			return true;
		}
	}
