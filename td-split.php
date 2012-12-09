<?php 

// the csv file of notes exported from toodle-do
$toodledo_file = 'td_notes.csv';

// the directory where you wish to export your notes
// make sure that this directory has the permissions required for writing
// IE, on mac OS X:   	cd $export_root; chmod 755 .; chown _www .;
$export_root = '/Library/WebServer/Documents/misc/toodledo/';

// create an array of lines from the export file
$lines = file($toodledo_file);

// set the extension you want for exported files
// Elements uses .md, but feel free to use .txt etc
$ext = ".md";

// regex to identify a new file
$re = '/^"([^"]*)","?([^",]*)"?,"([^"]*)","([^"]*)","(.*)$/';
for ($i = 1; $i < count($lines); ) {
	if ( preg_match( $re, $lines[$i], $matches ) ) {
		// output basic information about the file
		print_r( $matches );
		$i++;

		// initialize content
		$content = $matches[5]; 
		while ( isset( $lines[$i] ) && !preg_match($re, $lines[$i]) ) {
			// append each line to the content until we've hit another line that denotes a new toodledo page
			$content .= $lines[$i];
			$i++;
		}

		// determine which directory to output the file and create it if it doesn't already exist
		$dir = $matches[2] ? $export_root . $matches[2] . "/" : $export_root;
		if ( !file_exists( $dir ) ) {
			mkdir( $dir );
		}

		// replace slashes with underscores, '/' is not allowed in *nix file names
		$path = $dir . str_replace("/", "_", $matches[1]) . $ext;
		$fh = fopen( $path, 'w' );

		// OPTIONAL: fix duplicated carriage returns
		// $content = str_replace("\r\n\r\n", "\r\n", $content);
		
		// write content to the file and cleanup
		fwrite( $fh, $content );
		fclose( $fh );
	}
}

?>
