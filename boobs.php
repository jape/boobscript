<?php
// B.O.O.B.S.
$file = $argv[1];
$lines = file($file);

if (!$lines) {
	echo "File Not Found.";
	exit();
}

$b_commands = array("(.Y.)", "(.)", "()", "(o", "o)", "(_Y_)", "36DD", "8==D");
$p_commands = array('$v += ', '$v = pow($v, ', '$v = 0', '$bc=0; while(true) {', '$bc++;}', '$mode = 1', '$mode = 0', 'print ($mode == 1 ? chr($v) : $v)');

$src = '$mode = 0; $v = 0;';
foreach($lines as $line) {												// Each line in the code file
	$pos = 0;																				// Character position in line
	if (substr($line, 0, 2) == "))") continue;			// Comment
	
	$line = preg_replace('/\s+/', '', $line);				// Strip spaces

	while($pos <= strlen($line)-1) {								// While not at the end of the string...

		for($i=0; $i<count($b_commands); $i++) {			// Loop through Boobies commands
			$b_cmd = $b_commands[$i];										// ...
			echo substr($line, $pos, strlen($b_cmd))."\n";
			if (substr($line, $pos, strlen($b_cmd)) == $b_cmd) {	// If substring matches command...
				echo "MATCH\n";
				$pos += strlen($b_cmd);										// Update character position
				$src .= $p_commands[$i];									// Add PHP translated command to PHP interpreted src
				
				//echo substr($line, $pos, strlen($b_cmd)).":".$b_cmd."\n";
				switch($b_cmd) {													// Special operations for commands
					case "(.Y.)":														// $v = ...
						$num = 0;
						while($line[$pos] == ")") {
							$num++;
							$pos++;
						}
						$src .= $num;
						break;

					case "(.)":															// $v = pow($v, ...
						$num = 1;
						while(substr($line, $pos, 3) == "(.)") {
							$num++;
							$pos += 3;
						}
						$src .= $num . ")";										// Close the pow(x,y) call
						break;


					default:
//						$pos++;
						break;

				}	// switch
				$src .= ";";
			}  else if (($line[$pos] >= 0 && $line[$pos] < 10) && (substr($line, $pos, 2) != "8=")) { // A conditional
				$num = 0;
				while($line[$pos] > 0 && $line[$pos] < 10) {
					$num .= $line[$pos];
					$pos++;
				}
				switch ($line[$pos]) {
					case "A": // break if iteration = ...
						$src .= 'if ($bc == '.$num.') break;';
						$pos++;		// Because after the integer part, there is a letter denoting the action.
						break;

					case "B":	// break if var = ...
						$src .= 'if ($v == '.$num.') break;';
						$pos++;		// Because after the integer part, there is a letter denoting the action.
						break;
				}
				//$pos++;
			}
// if substr matches command
			
		}	
	} // while not at end of str
}

echo "\nProgram PHP source\n----------------------\n";
echo $src;
echo "\nProgram $file output\n----------------------\n";
eval($src);
echo "\n\n";
?>
