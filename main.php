<?php
require_once('head.php');
?>
<body>
  <?php require_once('menu.php');

// if admin
if (isset($_POST['inputSurname']) && 
    $_POST['inputSurname'] == 'admin' && 
    strlen($adminPassword) > 5 && 
    $_POST['inputAfm'] == $adminPassword){
  $inpAfm = 0;
  ?>
  <script>
    $(document).ready(function(){
      $('#upload-form').on('submit', function(e){
        //Stop the form from submitting itself to the server.
        e.preventDefault();
        // empty previous messages from div
        $('#result').empty();
        $.ajax({
            type: "POST",
            url: 'upload.php',
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData:false,
            success: function(data){
                $('<p><strong>'+data+'</strong></p>').appendTo('#result');
                setTimeout(() => {
                  location.reload();
                }, 3000);
            }
        });
      });
      // when file is selected for deletion
      $('.delete-file').on('click', function (e) {
        e.preventDefault();
        const theFile = e.currentTarget.id;
        if (confirm('Θέλετε σίγουρα να διαγράψετε το αρχείο: '+ theFile)) {
          $.ajax({
            type: "POST",
            url: 'upload.php',
            data: {delete: theFile},
            success: function(data){
              $('<p><strong>'+data+'</strong></p>').appendTo('#result-delete');
              setTimeout(() => {
                location.reload();
              }, 3000);
            }
        });
        }
      })
    });
  </script>
  <div class="container">
    <h1>Διαχείριση συστήματος</h1>
    <div class="row">
        <div class="col-md-6">
          <h3>Ανέβασμα αρχείου</h3>
          <form id='upload-form' action="upload.php" class="form-horizontal" method="post" role="form" enctype="multipart/form-data">
              <h4>Επιλέξτε αρχείο για ανέβασμα (μόνο CSV):</h4>
                <input type="file" name="fileToUpload" id="fileToUpload">
                <br>
                <input type="submit" value="Ανέβασμα" name="submit" class="btn btn-md btn-success">
          </form>
          <div id="result"></div>
        </div>
        
        <div class="col-md-6">
          <h3>Υπάρχοντα αρχεία</h3>
          <ul>
            <?php
              // find and show all csv files in folder
              $fileArr = scandir('csv');
              foreach ($fileArr as $line) {
                if (substr($line,-3) == 'csv') 
                  echo "<li>$line&nbsp;<span title='Διαγραφή αρχείου'><a href='#' class='delete-file' id='$line'><i class='fa fa-trash'></i></a></span></li>";
              }
            ?>
          </ul>
          <div id="result-delete"></div>
        </div>
    </div>
    <div class="row">
      <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
        <form class="form-horizontal" method="post" role="form" action='main.php'>
          <input type="submit" value="Έξοδος" name="submit" class="btn btn-lg btn-danger btn-block">
          <input type="hidden" name="logout" value="true">
        </form>
      </div>
    </div>
  </div>
  <?php
}
// end of admin

// display error message if not post
if (empty($_POST)){
  echo "<div class=\"container\"><div class=\"row\"><div class=\"col-md-8\">";
  echo "<h3>Σφάλμα: Απαγορεύεται η πρόσβαση..</h3><br><br></div></div>";
  echo "<div class=\"row\"><div class=\"col-md-2\"><a href=\"index.php\" class=\"btn btn-lg btn-primary btn-block\" >Επιστροφή</a>";
  echo "</div></div></div>";
  exit();
}
// if AFM is not 9 digits
if (isset($_POST['inputAfm']) && 
    strlen($_POST['inputAfm']) <> 9)
{
  echo "<div class=\"container\"><div class=\"row\"><div class=\"col-md-4\">";
  echo "<h3>Σφάλμα: το ΑΦΜ πρέπει να έχει ακριβώς 9 ψηφία...</h3><br><br></div></div>";
  echo "<div class=\"row\"><div class=\"col-md-2\"><a href=\"index.php\" class=\"btn btn-lg btn-primary btn-block\" >Επιστροφή</a>";
  echo "</div></div></div>";
  exit();
}

// if user has entered 9-digit AFM & surname
if (isset($_POST['inputAfm']) && strlen($_POST['inputAfm']) == 9 && 
    isset($_POST['inputSurname']) && strlen($_POST['inputSurname'])>0) {
  // initialize vars
  $inpAfm = $_POST['inputAfm'];
  $inpSurname = $_POST['inputSurname'];
  $found = $single = $hasAnadr = 0;
  $allRecords = [];
  
  $tmStr = 'Τ.Μ.';
  $adStr = 'ΑΔ.ΑΣΘ.';
  $synStr = 'ΣΥΝΟΛΟ';
  $anadrStr = 'ΑΝΑΔΡΟΜΙΚΑ';
  $yperStr = 'ΥΠΕΡΩΡΙΕΣ';
  $afStr = 'ΑΦΑΙΡΟΥΜΕΝΟ ΠΟΣΟ';
  $sympStr = 'ΣΥΜΨ. ΠΡ. ΜΗΝΑ';
  
  // init pdf output
  $pdfOutput = "<h4>$dnsiStr</h4><h3>Ενημέρωση μισθοδοσίας αναπληρωτών ΕΣΠΑ/ΠΔΕ</h3>";

  // find all csv files in csv directory
  $csvFiles = glob("csv/*.csv");

  // find employee in files
  foreach ($csvFiles as $csvFile) {
    // call parseFind to parse csv file, search,find & get results
    $data = parseFind($csvFile, $inpAfm, $inpSurname);
    $parsed = $data['parsed'];
    $month = $data['month'];
    if (count($parsed)>0){
      $found = 1;
      $allRecords[$month] = $parsed;
      $months[] = $month;
    }
  } //of foreach

  // if not found, display error & exit
  if (!$found){
    echo "<div class=\"container\"><div class=\"row\"><div class=\"col-md-6\">";
    echo "<h3>Σφάλμα: Ο υπάλληλος δε βρέθηκε ή έχετε καταχωρήσει λανθασμένα στοιχεία...</h3><br><br></div></div>";
    echo "<div class=\"row\"><div class=\"col-md-2\"><a href=\"index.php\" class=\"btn btn-lg btn-primary btn-block\" >Επιστροφή</a>";
    echo "</div></div></div>";
    exit();
  }
  // log to file
  if ($canLog)
    logToFile($inpAfm, $logFile);
?>
<div class="container">
   <!-- Create nav-tabs -->
   <div class="panel with-nav-tabs panel-default">
     <div class="panel-heading">
       <ul class="nav nav-tabs">
         <?php
         $first = 1;
         foreach ($months as $mon) {
           echo "<li ";
           echo $first ? "class=\"active\"" : '';
           echo "><a data-toggle=\"tab\" href=\"#$mon\">$mon</a></li>";
           $first = 0;
         }
         ?>
       </ul>
     </div>
   <!-- Create tab-content -->
   <div class="panel-body">
   <div class="tab-content">
  <?php
    $first = 1;
    // for each month
    foreach ($allRecords as $month => $recordSet) {
      $tm = $synolo = $anadromika = $yperwries = $afair = $symp = [];
      $synolo_ap = $synolo_asf = $synolo_for = $synolo_kath = 0;
      $adeies = [];
      // check if multi records
      if (count($recordSet) == 1) {
        $single = 1;
        $tm = $recordSet[0];
      }
      else {
        // find T.M., adeia etc
        foreach ($recordSet as $rec) {
            // find record types
            // find T.M. or ADEIA
            if (array_key_exists($hdr['ΕΙΔ. ΑΠ.'], $rec)) {
              $eidap = $rec[$hdr['ΕΙΔ. ΑΠ.']];
              if ($eidap == $tmStr)
                $tm = $rec;
              elseif ($eidap == $adStr)
                $adeies[] = $rec;
              elseif ($eidap == $anadrStr)
                $anadromika = $rec;
            }
            // find YPERWRIA or ANADROMIKA or AFAIROYMENO POSO
            if (array_key_exists($hdr['ΕΙΔ. ΑΠ.'], $rec)) {
              $comp = $rec[$hdr['ΕΙΔ. ΑΠ.']];
              if ($comp == $anadrStr)
                $anadromika = $rec;
              elseif ($comp == $yperStr)
                $yperwries = $rec;
              elseif ($comp == $afStr)
                $afair = $rec;
              elseif ($comp == $sympStr)
                $symp = $rec;
            }
          }
      } // of else multi
      if (!$tm)
        continue;
      // start building table @ month tab
      ?>
        <div id=<?= $month?> class="tab-pane fade in <?= $first ? 'active' : '' ?> ">
      <?php
          $ret = renderTable($tm, $hdr);
          $outPut = $ret['out'];
          $synolo_ap += $ret['apod'];
          $synolo_asf += $ret['asfal'];
          $synolo_for += $ret['foros'];
          $synolo_kath += $ret['kath'];

          if ($adeies) {
            $outPut .= "<h3>ΑΔΕΙΕΣ</h3>";
            foreach ($adeies as $ad){
              $ret = renderTable($ad,$hdr);
              $outPut .= $ret['out'];
              $synolo_ap += $ret['apod'];
              $synolo_asf += $ret['asfal'];
              $synolo_for += $ret['foros'];
              $synolo_kath += $ret['kath'];
            }
          }
          if ($anadromika) {
            $outPut .= "<h3>ΑΝΑΔΡΟΜΙΚΑ</h3>";
            $ret = renderSpecial($anadromika,$hdr);
            $outPut .= $ret['out'];
            $synolo_ap += $ret['apod'];
            $synolo_asf += $ret['asfal'];
            $synolo_for += $ret['foros'];
            $synolo_kath += $ret['kath'];
          }
          if ($symp) {
            $outPut .= "<h3>ΣΥΜΨΗΦΙΣΜΟΣ ΠΡΟΗΓΟΥΜΕΝΟΥ ΜΗΝΑ</h3>";
            $ret = renderSpecial($symp,$hdr);
            $outPut .= $ret['out'];
            $synolo_ap += $ret['apod'];
            $synolo_asf += $ret['asfal'];
            $synolo_for += $ret['foros'];
            $synolo_kath += $ret['kath'];
          }
          if ($afair) {
            $outPut .= "<h3>ΑΦΑΙΡΟΥΜΕΝΟ ΠΟΣΟ</h3>";
            $ret = renderSpecial($afair,$hdr);
            $outPut .= $ret['out'];
            $synolo_ap -= $ret['apod'];
            $synolo_asf -= $ret['asfal'];
            $synolo_for -= $ret['foros'];
            $synolo_kath -= $ret['kath'];
          }
          if ($yperwries) {
            $outPut .= "<h3>ΥΠΕΡΩΡΙΕΣ</h3>";
            $ret = renderSpecial($yperwries,$hdr);
            $outPut .= $ret['out'];
            $synolo_ap += $ret['apod'];
            $synolo_asf += $ret['asfal'];
            $synolo_for += $ret['foros'];
            $synolo_kath += $ret['kath'];
          }
          // if anadromika or adeies, print totals
          if ($anadromika || $adeies || $yperwries || $symp || $afair) {
            $outPut .= renderSynola($synolo_ap, $synolo_asf, $synolo_for, $synolo_kath);
          }
          $pdfOutput .= '<h3>' . $month .'</h3><br>' . $outPut . '<pagebreak />';
          echo $outPut;
      ?>
      </div>
        <?php
          $first = 0;
        } // of foreach
        // Remove last pagebreak
        $pdfOutput = substr($pdfOutput, 0, -13);
       ?>
     </div> <!-- of tab-content-->
   </div> <!-- of panel-body -->
 <div class="panel-footer">
 <?php if ($exportPdf){ ?>
 <div class="row">
   <div class="col-md-3 col-sm-3">
     <button id="pdfButton" type="button" name="button" class="btn btn-sm btn-success btn-block">Εξαγωγή όλων σε PDF</button>
   </div>
   <div id="postData" style="display: none;">
     <?= htmlspecialchars($pdfOutput)?>
   </div>
   <div id="pdfLink"></div>
 </div>
 <?php } ?>
 </div>
 </div> <!-- of panel -->
   <div class="row">
     <div class="col-md-2 col-sm-2">
       <form class="form-horizontal" method="post" role="form" action='index.php'>
         <input type="submit" value="Έξοδος" name="submit" class="btn btn-lg btn-danger btn-block">
         <input type="hidden" name="logout" value="true">
       </form>
     </div>
   </div>
</div> <!-- of container -->
 </body>
 <script type = "text/javascript">
    $(document).ready(function() {
      $("#pdfButton").click(function(event){
        var div = document.getElementById("postData");
        var myData = div.textContent;
        var userAfm = <?php echo $inpAfm ? $inpAfm : 0 ?>;
          $.post(
            "pdf.php",
            { afm: userAfm, data: myData },
            function(data) {
              $('#pdfLink').html(data);
            }
          );
      });
    });      
</script>
<?php
} // of else user filled form
require_once('footer.html');
?>

