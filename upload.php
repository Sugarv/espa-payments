<?php
$target_dir = "csv/";

// handle delete file
if (isset($_POST['delete'])) {
    if (unlink($target_dir.$_POST['delete']))
        echo "Το αρχείο " . $_POST['delete']. " διαγράφηκε με επιτυχία!";
    die();
}
// source: https://www.w3schools.com/php/php_file_upload.asp

$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

if ( $_FILES["fileToUpload"]["size"] == 0 ) {
   echo "Σφάλμα: Δεν επιλέχθηκε αρχείο.";
   $uploadOk = 0;
   die();
}

// Check if file already exists
if ($uploadOk && file_exists($target_file)) {
    echo "Σφάλμα: Το αρχείο υπάρχει ήδη.";
    $uploadOk = 0;
}
// Check file size
if ($uploadOk && $_FILES["fileToUpload"]["size"] > 1000000) {
    echo "Σφάλμα: Το αρχείο είναι πολύ μεγάλο (>1 ΜΒ).";
    $uploadOk = 0;
}
// Allow certain file formats
if($uploadOk && $fileType != "csv" ) {
    echo "Σφάλμα: Επιτρέπονται μόνο αρχεία CSV.";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "<br>Λυπούμαστε, το αρχείο σας δεν ανέβηκε.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "Το αρχείο με όνομα ". basename( $_FILES["fileToUpload"]["name"]). " ανέβηκε επιτυχώς.";
    } else {
        echo "Λυπούμαστε, παρουσιάστηκε σφάλμα κατά το ανέβασμα...";
    }
}
?>