<?php
// Parameters
// Διεύθυνση Εκπαίδευσης
$dnsiStr = '';
// Διεύθυνση Εκπαίδευσης (Συντομογραφία)
$dnsiStrShort = '';
// Ιστοσελίδα Διεύθυνσης Εκπαίδευσης
$dnsiLink = '';
// set canLog to 1 to enable logging to file
// file must be .txt to be protected from access
$canLog = 1;
$logFile = 'login_log.txt';
// clean up pdf files after XX minutes
$cleanUpAfter = 10;
// export to pdf or not (1 or 0)
$exportPdf = 1;

// Espa column headers
// ESPA csv is a mess, with duplicate column names, that's why we keep field numbers in this array...
$hdr = Array (
   'Α/Α' => 0, 'Ονοματεπώνυμο' => 1,'ΕΙΔΟΣ ΑΠΑΣΧ.' => 2, 'ΥΠΟΧ. ΩΡΑΡ.' => 3, 'ΔΕ' => 4, 'ΩΡΕΣ ΑΝΑ ΕΒΔ.' => 5, 'ΗΜΕΡΕΣ' => 7, 'ΚΩΔΙΚ.' => 8, 'ΑΦΜ' => 9, 'Μ.Κ.(BM)' => 10,
   'ΕΙΔ. ΑΠ.' => 11, 'ΗΜ.ΑΣΦ.' => 12, 'Μ.Κ.(ΠΟΣΟ)' => 13, 'Ο.Ε.' => 14, 'ΕΠΠ' => 15, 'ΣΥΝΟΛΟ(ΑΠ)' => 16, 'ΦΟΡΟΣ' => 17, 'ΕΚΤΑΚΤΗ ΕΙΣΦΟΡΑ' => 18,
   'ΟΑΕΔ' => 19, 'ΙΚΑ' => 20, 'ΕΡΓΑΖΟΜ.' => 21, 'ΕΡΓΟΔΟΤΗ' => 22, 'ΣΥΝΟΛΟ(ΙΚΑ)' => 23, 'ΤΑΜΕΙΟ' => 24, 'ΕΡΓΑΖΟΜ.(ΤΑΜ)' => 25, 'ΕΡΓΟΔΟΤΗ(ΤΑΜ)' => 26, 'ΣΥΝΟΛΟ(ΤΑΜ)' => 27,
   'ΑΧΡΕΩΣΤ.' => 28, 'ΑΦ.ΠΟΣΟ' => 29, 'ΕΠΙΔ.ΕΡΓ.' => 30, 'ΑΠΟΖ.ΙΚΑ' => 31, 'ΚΑΘΑΡΑ' => 32, 'ΜΕ ΑΠ.ΤΑΜ.' => 33, 'ΜΕ ΑΠ.ΤΑΜ. 2' => 34, 'ΕΝΑΝΤΙ' => 35, 'ΔΙΑΦΟΡΑ' => 36, 'ΜΗΝΑΣ' => 19
);

// custom message to display @ help column
$customMessage = "";

// set custom employee codes, to be used instead of surnames
// sample: $customCodes = array('afm1' => 'code1', 'afm2' => 'code2')
$customCodes = array();

// google Analytics code
// optional: insert google analytics code (e.g. UA-XXXXX-Y)
$gAnalytics = '';

// Admin password for file uploading
// Note: username is 'admin', password must be 6 or more characters
$adminPassword = 'changeme';
?>
