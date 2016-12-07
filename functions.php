<?php
/**
 * Greek string to uppercase
 * Retrieved from: https://github.com/vdw/Greek-string-to-uppercase
 * Correctly converts greek letters to uppercase.
 */
function grstrtoupper($string) {
		$latin_check = '/[\x{0030}-\x{007f}]/u';
		if (preg_match($latin_check, $string))
		{
			$string = strtoupper($string);
		}
		$letters  								= array('α', 'β', 'γ', 'δ', 'ε', 'ζ', 'η', 'θ', 'ι', 'κ', 'λ', 'μ', 'ν', 'ξ', 'ο', 'π', 'ρ', 'σ', 'τ', 'υ', 'φ', 'χ', 'ψ', 'ω');
		$letters_accent 						= array('ά', 'έ', 'ή', 'ί', 'ό', 'ύ', 'ώ');
		$letters_upper_accent 					= array('Ά', 'Έ', 'Ή', 'Ί', 'Ό', 'Ύ', 'Ώ');
		$letters_upper_solvents 				= array('ϊ', 'ϋ');
		$letters_other 							= array('ς');
		$letters_to_uppercase					= array('Α', 'Β', 'Γ', 'Δ', 'Ε', 'Ζ', 'Η', 'Θ', 'Ι', 'Κ', 'Λ', 'Μ', 'Ν', 'Ξ', 'Ο', 'Π', 'Ρ', 'Σ', 'Τ', 'Υ', 'Φ', 'Χ', 'Ψ', 'Ω');
		$letters_accent_to_uppercase 			= array('Α', 'Ε', 'Η', 'Ι', 'Ο', 'Υ', 'Ω');
		$letters_upper_accent_to_uppercase 		= array('Α', 'Ε', 'Η', 'Ι', 'Ο', 'Υ', 'Ω');
		$letters_upper_solvents_to_uppercase 	= array('Ι', 'Υ');
		$letters_other_to_uppercase 			= array('Σ');
		$lowercase = array_merge($letters, $letters_accent, $letters_upper_accent, $letters_upper_solvents, $letters_other);
		$uppercase = array_merge($letters_to_uppercase, $letters_accent_to_uppercase, $letters_upper_accent_to_uppercase, $letters_upper_solvents_to_uppercase, $letters_other_to_uppercase);
		$uppecase_string = str_replace($lowercase, $uppercase, $string);
		return $uppecase_string;
}

// Parse CSV file and find employee
// uses https://github.com/parsecsv/parsecsv-for-php
function parseFind($csvFile, $afm, $surname){
     // init vars
     global $hdr, $customCodes;
     $empOffset = 5;
     $anadrData = [];
     // parse csv
     $csv = new parseCSV();
     $csv->encoding('iso8859-7','UTF-8');
     $csv->delimiter = ";";
     $csv->heading = false;
     // find employee T.M.
     // search if employee has custom code (set @ config.php)
     if (array_key_exists($afm, $customCodes)){
       if (strcmp($surname, $customCodes[$afm]) <> 0)
            return ['parsed' => [], 'month' => []];
       else {
         $csv->offset = $empOffset;
         $condition = $hdr['ΑΦΜ'] . ' is '.$afm;
         $csv->conditions = $condition;
         $csv->parse($csvFile);
         $parsed = $csv->data;
       }
     }
     // else, find @ csv
     else {
       $csv->offset = $empOffset;
       $condition = $hdr['ΑΦΜ'] . ' is '.$afm.' AND 1 contains '.grstrtoupper($surname);
       $csv->conditions = $condition;
       $csv->parse($csvFile);
       $parsed = $csv->data;

       // enhanced check of surname (instead of 'contains' in fullname)
       if ($parsed){
         $tmp = explode(' ',$parsed[0][1]);
         $fileSurname = $tmp[0];
         if (strcmp(grstrtoupper($surname), $fileSurname) <> 0)
            return ['parsed' => [], 'month' => []];
       }
     }
     // find month @ column ΜΗΝΑΣ
     $csv->offset = 1;
     $csv->conditions = $hdr['ΜΗΝΑΣ'] . ' contains ΜΙΣΘΟΔΟΣΙΑ';
     $csv->parse($csvFile);
     //$csv->fields =[19];
     $data = $csv->data;
     if($data){
      $tmp = explode(' ',$data[0][19]);
      $month = $tmp[2] . '_' . $tmp[3];
     }
     else $month = 'ΜΗΝΑΣ';

     // find anadromika (if any)
     $csv->offset = $empOffset;
     $csv->conditions = '';
     $csv->parse($csvFile);
     $data = $csv->data;
     $i = $foundFrom = $foundTo = 0;
     foreach ($data as $row) {
       if (array_key_exists('8',$row) && $afm == $row[8] && !$foundFrom) {
         $foundFrom = $i;
       }
       if ($foundFrom && !$foundTo && array_key_exists('8',$row)){
         if ($row[8] != '' && $row[8] != $afm) {
           $foundTo = $i-1;
         }
       }
       $i++;
     }
     $tempData = array_slice($data, $foundFrom, $foundTo-$foundFrom+1);
     foreach ($tempData as $line) {
       if ($line[10] == 'ΑΝΑΔΡΟΜΙΚΑ')
        $anadrData = $line;
     }
     if (count($anadrData)>0)
        array_push($parsed, $anadrData);

    return ['parsed' => $parsed, 'month' => $month];
}

// filterCol: used for csv numbers.
// Returns proper float type, by replacing , (comma) with . (dot)
function filterCol($ar,$hdr,$ind) {
     return preg_replace("/[^-0-9\.]/",".",str_replace('.','',$ar[$hdr[$ind]]));
}

// Render a table for the given record ($rec) based on the header array ($hdr)
function renderTable($rec, $hdr, $isAnadr = 0) {
   ob_start();
   ?>
   <div class="row">
     <!-- personal -->
     <?php if (!$isAnadr): ?>
      <table class="table table-bordered table-hover table-condensed table-responsive csv-results">
               <thead>
                 <tr>
                  <th colspan=4 class="info">Προσωπικά Στοιχεία</th>
                 </tr>
               </thead>
               <tbody>
                 <tr>
                  <td>Ονοματεπώνυμο</td>
                  <td><?= $rec[$hdr['Ονοματεπώνυμο']]; ?></td>
                  <td >ΑΦΜ</td>
                  <td><?= $rec[$hdr['ΑΦΜ']]; ?></td>
                 </tr>
                 <tr>
                  <td>Βαθμός-ΜΚ</td>
                  <td><?= $rec[$hdr['Μ.Κ.(BM)']]; ?></td>
                  <td></td>
                  <td></td>
                  </tr>
                  <tr>
                     <td>Είδος Απασχόλησης</td>
                     <td><?= $rec[$hdr['ΕΙΔ. ΑΠ.']]; ?></td>
                     <td>Ημέρες</td>
                     <td><?= $rec[$hdr['ΗΜ.ΑΣΦ.']] ? $rec[$hdr['ΗΜ.ΑΣΦ.']] : ''; ?></td>
                 </tr>
               </tbody>
         </table>
       <?php endif; ?>
         <!-- TM -->
         <table class="table table-bordered table-hover table-condensed table-responsive csv-results">
                 <thead>
                  <tr>
                     <th colspan=3 class="info">Τακτική μισθοδοσία</th>
                  </tr>
                  <tr>
                     <th>A/A</th>
                     <th>Τύπος</th>
                     <th>Ποσό</th>
                  </tr>
                 </thead>
                 <tbody>
                  <tr>
                     <td>1</td>
                     <td>Τακτική Μισθοδοσία</td>
                     <td><?= filterCol($rec,$hdr,'ΣΥΝΟΛΟ(ΑΠ)') ?></td>
                  </tr>
                  <?php
                     if (filterCol($rec,$hdr,'Ο.Ε.')>0):
                   ?>
                  <tr>
                     <td>2</td>
                     <td>Οικογενειακό επίδομα</td>
                     <td><?= filterCol($rec,$hdr,'Ο.Ε.') ?></td>
                  </tr>
               <?php endif; ?>
                 </tbody>
         </table>
         <!-- Asfalistika -->
         <table class="table table-bordered table-hover table-condensed table-responsive csv-results">
                 <thead>
                  <tr>
                     <th colspan=3 class="info"><?= $rec[$hdr['ΙΚΑ']]; ?></th>
                  </tr>
                  <tr>
                     <th>A/A</th>
                     <th>Τύπος</th>
                     <th>Ποσό</th>
                  </tr>
                 </thead>
                 <tbody>
                  <tr>
                     <td>1</td>
                     <td>Ασφαλιστικές Εισφορές</td>
                     <td><?= filterCol($rec,$hdr,'ΕΡΓΑΖΟΜ.') ?></td>
                  </tr>
                  <tr>
                     <td>2</td>
                     <td>Εργοδοτικές εισφορές</td>
                     <td><?= filterCol($rec,$hdr,'ΕΡΓΟΔΟΤΗ') ?></td>
                  </tr>
                  <?php
                  // if other TAMEIO
                  if (strlen($rec[$hdr['ΤΑΜΕΙΟ']])>0):
                     $tameio = 1;
                  ?>
                     <tr>
                        <td colspan=3><?= $rec[$hdr['ΤΑΜΕΙΟ']]; ?></td>
                     </tr>
                     <tr>
                        <td>3</td>
                        <td>Ασφαλιστικές Εισφορές</td>
                        <td><?= filterCol($rec,$hdr,'ΕΡΓΑΖΟΜ.(ΤΑΜ)') ?></td>
                     </tr>
                     <tr>
                        <td>4</td>
                        <td>Εργοδοτικές εισφορές</td>
                        <td><?= filterCol($rec,$hdr,'ΕΡΓΟΔΟΤΗ(ΤΑΜ)') ?></td>
                     </tr>
                  <?php endif;
                  ?>
                  <tr>
                     <td colspan = 3>OAEΔ</td>
                  </tr>
                  <tr>
                     <td></td>
                     <td>Υπέρ ΟΑΕΔ</td>
                     <td><?= filterCol($rec,$hdr,'ΟΑΕΔ') ?></td>
                  </tr>
                  <tr>
                     <td colspan=2>ΣΥΝΟΛΟ</td>
                     <td><?= filterCol($rec,$hdr,'ΣΥΝΟΛΟ(ΙΚΑ)')+ filterCol($rec,$hdr,'ΟΑΕΔ')+filterCol($rec,$hdr,'ΣΥΝΟΛΟ(ΤΑΜ)') ?></td>
                  </tr>
                  <tr><td colspan=3></td></tr>
                  <tr class="info">
                     <td colspan=4><h4><strong>Σύνολα</strong></h4></td>
                  </tr>
                  <tr>
                     <td colspan=2>Σύνολο Αποδοχών</td>
                     <td><?= filterCol($rec,$hdr,'ΣΥΝΟΛΟ(ΑΠ)') ?></td>
                  </tr>
                  <tr>
                     <td colspan=2>Σύνολο Ασφαλιστικών Εισφορών</td>
                     <td><?= filterCol($rec,$hdr,'ΣΥΝΟΛΟ(ΙΚΑ)')+ filterCol($rec,$hdr,'ΟΑΕΔ')+filterCol($rec,$hdr,'ΣΥΝΟΛΟ(ΤΑΜ)') ?></td>
                  </tr>
                  <tr>
                     <td colspan=2>Φόρος</td>
                     <td><?= filterCol($rec,$hdr,'ΦΟΡΟΣ') ?></td>
                  </tr>
                  <tr class="success">
                     <td colspan=2>Καθαρά στο Δικαιούχο</td>
                     <td><?= filterCol($rec,$hdr,'ΚΑΘΑΡΑ') ?></td>
                  </tr>
                 </tbody>
         </table>
   </div> <!-- of row-->
<?php
  $synolo_ap = filterCol($rec,$hdr,'ΣΥΝΟΛΟ(ΑΠ)');
  $synolo_asf = filterCol($rec,$hdr,'ΣΥΝΟΛΟ(ΙΚΑ)')+ filterCol($rec,$hdr,'ΟΑΕΔ')+filterCol($rec,$hdr,'ΣΥΝΟΛΟ(ΤΑΜ)');
  $synolo_for = filterCol($rec,$hdr,'ΦΟΡΟΣ');
  $synolo_kath = filterCol($rec,$hdr,'ΚΑΘΑΡΑ');
   $ret = ob_get_contents();
   ob_end_clean();
   return ['out'=>$ret, 'apod'=>$synolo_ap, 'asfal'=>$synolo_asf, 'foros'=> $synolo_for, 'kath'=>$synolo_kath];
} // of function

// Render a table containing grand totals
function renderSynola($apod, $asfal, $foros, $kath) {
  ob_start();
  ?>
  <div class="row">
    <table class="table table-bordered table-hover table-condensed table-responsive csv-results">
      <thead>
        <th class="info" colspan=2>
          <h3>Γενικά Σύνολα</h3>
        </th>
      </thead>
      <tbody>
        <tr><td>Αποδοχές</td><td><?= sprintf("%.2f",$apod) ?></td></tr>
        <tr><td>Ασφαλιστικές Εισφορές</td><td><?= sprintf("%.2f",$asfal) ?></td></tr>
        <tr><td>Φόρος</td><td><?= sprintf("%.2f",$foros) ?></td></tr>
        <tr class="success"><td><strong>Καθαρά</strong></td><td><strong><?= sprintf("%.2f",$kath) ?></strong></td></tr>
      </tbody>
    </table>
  </div>
  <?php
  $ret = ob_get_contents();
  ob_end_clean();
  return $ret;
}

// Log successful entries (or pdf creation) to login_log.txt which is protected by .htaccess
function logToFile($afm, $fname, $pdf = 0) {
  $fh = fopen($fname, 'a');
	$maskedAfm = substr($afm, 0, -4) . '****';
	if ($pdf)
		$data = $maskedAfm . ' has created PDF' ."\n";
	else
  	$data = $maskedAfm . "\t" . date('d-m-Y, H:i:s') . "\t" . $_SERVER['HTTP_USER_AGENT'] ."\n";
  fwrite($fh, $data);
  fclose($fh);
}

// Clean up files after 10 minutes
function clean_up($limit = 10){
	foreach(glob('pdf/*.pdf') as $file){
		$diff = round((time() - filemtime($file)) / 60, 2);
		if($diff > $limit) unlink($file);
	}
}

// get the latest month from csv filenames.
// Requires ALL files to be named YYMM_something.csv (Y: Year, M: month, e.g. 1610_etc.csv)
function getLatestMonth(){
   $csvFiles = glob("csv/*.csv");
   foreach ($csvFiles as $csvFile) {
      if (is_int((int)substr($csvFile,4,4)))
         $monAr[] = substr($csvFile,4,4);
      else
         return;
   }
   $max = max($monAr);
   echo "Τελευταία Μισθοδοσία: " . substr($max, 2, 2) . "/20" . substr($max, 0, 2);
}
 ?>
